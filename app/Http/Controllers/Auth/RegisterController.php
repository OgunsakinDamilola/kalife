<?php

namespace App\Http\Controllers\Auth;

use App\Mail\SuccessfulRegistration;
use App\User;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Exception;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
//    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title'         => 'required|string',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'other_name'    => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'phone_number'  => 'required|string',
            'password'      => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

    protected function create(array $data)
    {
        $user = User::create([
            'title' => array_get($data, 'title'),
            'first_name' => array_get($data, 'first_name'),
            'last_name' => array_get($data, 'last_name'),
            'other_name' => array_get($data, 'other_name'),
            'date_of_birth' => array_get($data, 'date_of_birth'),
            'email' => array_get($data, 'email'),
            'phone_number' => array_get($data, 'phone_number'),
            'address' => array_get($data, 'address'),
            'gender' => array_get($data, 'gender',1),
            'password' => bcrypt($data['password']),
            'account_status' => 1,
        ]);

        $user->attachrole(1);
        try{
            Mail::to($user)->send(new SuccessfulRegistration($data));
        }catch(Exception $e){
            Toastr::info('Could not send email');

        }
        return $user;
    }
}
