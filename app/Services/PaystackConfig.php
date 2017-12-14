<?php
/**
 * Created by PhpStorm.
 * User: UniQue
 * Date: 12/12/2017
 * Time: 2:48 PM
 */

namespace App\Services;


class PaystackConfig{
    public $request_url = 'https://api.paystack.co/transaction/initialize';

    public $query_url = 'https://api.paystack.co/transaction/verify';

    public $test_secret_key = 'sk_test_ac21a0e9ae37d41a4d316980639cfd976a016862';

    public $live_secret_key = '';

    public $test_public_key = 'pk_test_40dd5275ea626aad229cfedb1c06c961985b64d4';

    public $live_public_key = '';

    public function __construct(){
        $this->secretKey = $this->test_secret_key;
        $this->publicKey = $this->test_public_key;
    }

    public function makeRedirectUrl($page){
        return $_SERVER['HTTP_HOST'].$page;
    }

    public function initialize($email,$amount,$txnRef,$redirectPage){
        $postData =  [
            'email' => $email,
            'amount' => $amount,
            "reference" => $txnRef,
            "callback_url" => $this->makeRedirectUrl($redirectPage)
            ];
            $url = $this->request_url;
        $headers = [
            'Authorization: Bearer '.$this->secretKey,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postData));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec ($ch);
        curl_close ($ch);
        if(empty($response)){
            return 0;
        }else{
            $response = json_decode($response, true);
            if(!isset($response['data']['authorization_url'])){
                return 2;
            }elseif(isset($response['data']['authorization_url'])){
                $url = $response['data']['authorization_url'];
                redirect(url($url));
            }
        }

    }

    public function query($txnRef){
        $url = $this->query_url."/".$txnRef;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '. $this->secretKey]
        );
        $response = curl_exec($ch);
        curl_close($ch);
        return $this->queryValidator($response);
    }

    public function queryValidator($response){
        if(empty($response)){
            return 0;
        }else{
            $response = json_decode($response, true);
            if(isset($response['status'])){
                return [
                    'status' => 1,
                    'responseCode' => '00',
                    'responseDescription' => 'Payment Made Successfully'
                ];
            }else{
                return [
                    'status' => 0,
                    'responseCode' => '--',
                    'responseDescription' => 'Payment Not Successful'
                ];
            }
        }
    }

}