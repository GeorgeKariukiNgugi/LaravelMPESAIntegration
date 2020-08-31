<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PaymentsC2B;
use Illuminate\Http\Response;
class newImplementation extends Controller
{
    // ! creating the function that will be used to generate the access Tokens .  

    public function generateAccessTokens(){
        
        $consumer_key="i6X9jcGwwkk6LYiBnUGBYlV1YDU0Gujc";
        $consumer_secret="Z2ylt0kTE5QqA20j";
        $credentials = base64_encode($consumer_key.":".$consumer_secret);
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token=json_decode($curl_response);
        return $access_token->access_token;

    }

    // ! creating the confirmation method.

    public function confirmationMethod(Request $request){
        // header('Content-type: application/json');

        $response = '{
            "ResultCode": 0,
            "ResultDesc" "Confirmation Received Successfully."
        }';

        // ! getting the data. 

        // $mpesaResponse = file_get_contents('php://input');
        // $jsonMpesaResponse = json_decode($mpesaResponse, true);

        // ! save the data to the database. 

        $content=json_decode($request->getContent());
        $payment = new PaymentsC2B();
        $payment->name = $content->FirstName.' '.$content->TransAmount;;
        $payment->save();

        return $response;

                    
    }

    public function createValidationResponse($result_code, $result_description){
        $result=json_encode(["ResultCode"=>$result_code, "ResultDesc"=>$result_description]);
        $response = new Response();
        $response->headers->set("Content-Type","application/json; charset=utf-8");
        $response->setContent($result);
        return $response;
    }

    // ! creating the validation method. 

    public function validationMethod(Request $request){

        $result_code = "0";
        $result_description = "Accepted validation request.";
        return $this->createValidationResponse($result_code, $result_description);

    }

    // ! registering URLs . 

    public function registerURLS(){
        $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generateAccessTokens())); //setting custom header
        
        
        $curl_post_data = array(
          //Fill in the request parameters with valid values
          'ShortCode' => '600754',
          'ResponseType' => 'Confirmed',
          'ConfirmationURL' => 'https://safaricommobilemoneyintegration.georgekprojects.tk/api/confirmationURL',
          'ValidationURL' => 'https://safaricommobilemoneyintegration.georgekprojects.tk/api/validationURL',
        );
        
        $data_string = json_encode($curl_post_data);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        $curl_response = curl_exec($curl);
        // print_r($curl_response);
        
        // echo $curl_response;

        return $curl_response;            
    }

    public function simulateTransaction(){
        $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate';
  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generateAccessTokens())); //setting custom header
      
      
        $curl_post_data = array(
                //Fill in the request parameters with valid values
               'ShortCode' => '600754',
               'CommandID' => 'CustomerPayBillOnline',
               'Amount' => '1800',
               'Msisdn' => '254708374149',
               'BillRefNumber' => '00000'
        );
      
        $data_string = json_encode($curl_post_data);
      
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
      
        $curl_response = curl_exec($curl);
        // print_r($curl_response);
      
        return $curl_response;
    }
}
