<?php

namespace App\Http\Controllers;

use App\Attraction;
use App\CabinType;
use App\FlightDeal;
use App\Gallery;
use App\HotelDeal;
use App\PackageCategory;
use App\SightSeeing;
use App\TravelPackage;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class TravelPackageController extends Controller
{

    public function packageCreate(){
        $package_categories = PackageCategory::all();
        $cabin_types = CabinType::all();
        return view('backend.travel-packages.new_package', compact('package_categories','cabin_types'));
    }

    public function create(Request $r){

       $options = explode(',',$r->options);
       $flight = 0;
       $hotel = 0;
       $attraction = 0;
        foreach($options as $option){
            if($option === 'flight'){
                $flight = 1;
            }
            if($option === 'hotel'){
                $hotel = 1;
            }
            if($option === 'attraction'){
                $attraction = 1;
            }
        }
        $info = [
            'flight'      => $flight,
            'hotel'       => $hotel,
            'attraction' => $attraction,
            'default'     => $r
        ];

       return TravelPackage::store($info);
    }

    public function createFlightDeal(Request $r){

        return FlightDeal::store($r);
    }

    public function createHotelDeal(Request $r){

        return HotelDeal::store($r);
    }

    public function createAttraction(Request $r){

        $attraction = Attraction::store($r);
        $sight_seeing_titles = explode(',', $r->sight_seeing_titles);
        $sight_seeing_descriptions = explode(',', $r->sight_seeing_descriptions);
        for($i = 0; $i < count($sight_seeing_titles); $i++){
           $sightSeeing = [
               'package_id'    => $r->package_id,
               'attraction_id' => $attraction->id,
               'title'         => $sight_seeing_titles[$i],
               'description'   => $sight_seeing_descriptions[$i]
           ];
           SightSeeing::storeSightSeeing($sightSeeing);
        }
        return $attraction;
    }

    public function createSightSeeings(Request $r){

        return SightSeeing::stor($r);
    }

    public function travelPackages(){
        $packages = TravelPackage::getAllPackagesDesc();
        return view('backend.travel-packages.packages', compact('packages'));
    }

    public function activate($id)
    {
        $response = [
            'status'=>''
        ];

        if (TravelPackage::isActivated($id))
        {
            $response['status'] = 'activated';
            return response()->json($response);
        }else
        {
            if (TravelPackage::activatePackage($id))
            {
                $response['status'] = true;
                return response()->json($response);
            }
            else
            {
                $response['status'] = false;
                return response()->json($response);
            }
        }
    }

    public function deactivate($id)
    {
        $response = [
            'status'=>''
        ];

        if (TravelPackage::isDeactivated($id))
        {
            $response['status'] = 'deactivated';
            return response()->json($response);
        }else
        {
            if (TravelPackage::deactivatePackage($id))
            {
                $response['status'] = true;
                return response()->json($response);
            }
            else
            {
                $response['status'] = false;
                return response()->json($response);
            }
        }
    }

    public function deletePackage($id){
        return TravelPackage::deletePackage($id);
    }

    public function editPackage($id){
        $package_categories = PackageCategory::all();
        $cabin_types = CabinType::all();
        $package    = TravelPackage::find($id);
        $flightDeal = FlightDeal::getByPackageId($id);
        $hotelDeal = HotelDeal::getByPackageId($id);
        $attraction = Attraction::getByPackageId($id);
        return view('backend.travel-packages.edit_package',compact('package','flightDeal','hotelDeal','attraction','package_categories','cabin_types'));

    }

    public function deleteImage(Request $r){
       Gallery::deletePicture($r->id);
    }

    public function categories(){
        $package_categories = PackageCategory::orderBy('id','desc')->get();
        return view('backend.travel-packages.categories',compact('package_categories'));
    }

    public function activateCategory(Request $r){
        $category = PackageCategory::find($r->id);
        if($category->status == 1){
            return 2;
        }
        $category->status = 1;
        $category->update();
        return 1;
    }

    public function deActivateCategory(Request $r){
        $category = PackageCategory::find($r->id);
        if($category->status == 0){
            return 2;
        }
        $category->status = 0;
        $category->update();
        return 1;
    }

    public function categoryCreateOrUpdate(Request $r){
        PackageCategory::store($r);
        Toastr::success('Package information updated in database');
        return redirect(url('backend/travel-packages/categories'));
    }



}
