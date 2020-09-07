<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PaymentsC2B;
use Illuminate\Http\Response;
use App\Events\PaymentEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class newImplementation extends Controller
{

    // ! Creating the STK push simulation. 

    /**
     * Lipa na M-PESA password
     * */
    public function lipaNaMpesaPassword()
    {
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $BusinessShortCode = 174379;
        $timestamp = $lipa_time;
        $lipa_na_mpesa_password = base64_encode($BusinessShortCode . $passkey . $timestamp);
        return $lipa_na_mpesa_password;
    }
    /**
     * Lipa na M-PESA STK Push method
     * */
    public function customerMpesaSTKPush()
    {
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' .$this->generateAccessTokens()));
        $curl_post_data = [
            //Fill in the request parameters with valid values
            'BusinessShortCode' => 174379,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1,
            'PartyA' => 254796446324, // replace this with your phone number
            'PartyB' => 174379,
            'PhoneNumber' => 254796446324, // replace this with your phone number
            'CallBackURL' => 'https://safaricommobilemoneyintegration.georgekprojects.tk/api/stkPushCallBack',
            'AccountReference' => "Sample",
            'TransactionDesc' => "Testing stk push on sandbox"
        ];
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        return $curl_response;

    }

    public function callBackForTheSTKPush(Request $request){

        $content = json_decode($request->getContent());
        $mpesa_transaction = new PaymentsC2B();
        // $mpesa_transaction->TransAmount = $content->Body->stkCallback->MerchantRequestID;
        $mpesa_transaction->TransID ="Trans Id.";

        // $mpesa_transaction->TransTime = $content->TransTime;
        // $mpesa_transaction->TransAmount = $content->TransAmount;
        // $mpesa_transaction->BusinessShortCode = $content->BusinessShortCode;
        // $mpesa_transaction->BillRefNumber = $content->BillRefNumber;
        // $mpesa_transaction->InvoiceNumber = $content->InvoiceNumber;
        // $mpesa_transaction->OrgAccountBalance = $content->OrgAccountBalance;
        // $mpesa_transaction->ThirdPartyTransID = $content->ThirdPartyTransID;
        // $mpesa_transaction->MSISDN = $content->MSISDN;
        // $mpesa_transaction->FirstName = $content->FirstName;
        // $mpesa_transaction->MiddleName = $content->MiddleName;
        // $mpesa_transaction->LastName = $content->LastName;        
        $mpesa_transaction->save();

        // Storage::put('attempt3.txt', $content);
        // Storage::disk('local')->put('file.txt',  $content->all());
        Storage::put('attempt3.txt', $content->Body);
        // ! fire the broadcast events. 
        event(new PaymentEvent($content));

        $response = new Response();
        $response->headers->set("Content-Type", "text/xml; charset=utf-8");
        $response->setContent(json_encode(["Lipa Na Mpea Online" => "Success"]));

        return $response;

    }

    // ! creating the function that will be used to generate the access Tokens .  

    public function generateAccessTokens()
    {

        $consumer_key = "i6X9jcGwwkk6LYiBnUGBYlV1YDU0Gujc";
        $consumer_secret = "Z2ylt0kTE5QqA20j";
        $credentials = base64_encode($consumer_key . ":" . $consumer_secret);
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic " . $credentials));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token = json_decode($curl_response);
        return $access_token->access_token;
    }

    // ! creating the confirmation method.

    public function confirmationMethod(Request $request)
    {

        $content = json_decode($request->getContent());
        $mpesa_transaction = new PaymentsC2B();
        $mpesa_transaction->TransactionType = $content->TransactionType;
        $mpesa_transaction->TransID = $content->TransID;
        $mpesa_transaction->TransTime = $content->TransTime;
        $mpesa_transaction->TransAmount = $content->TransAmount;
        $mpesa_transaction->BusinessShortCode = $content->BusinessShortCode;
        $mpesa_transaction->BillRefNumber = $content->BillRefNumber;
        $mpesa_transaction->InvoiceNumber = $content->InvoiceNumber;
        $mpesa_transaction->OrgAccountBalance = $content->OrgAccountBalance;
        $mpesa_transaction->ThirdPartyTransID = $content->ThirdPartyTransID;
        $mpesa_transaction->MSISDN = $content->MSISDN;
        $mpesa_transaction->FirstName = $content->FirstName;
        $mpesa_transaction->MiddleName = $content->MiddleName;
        $mpesa_transaction->LastName = $content->LastName;
        // $mpesa_transaction->TransactionType = 'Lipa Na MPESA.';
        $mpesa_transaction->save();

        // ! fire the broadcast events. 
        event(new PaymentEvent($content));

        //! Responding to the confirmation request
        $response = new Response();
        $response->headers->set("Content-Type", "text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult" => "Success"]));

        return $response;
    }

    public function createValidationResponse($result_code, $result_description)
    {
        $result = json_encode(["ResultCode" => $result_code, "ResultDesc" => $result_description]);
        $response = new Response();
        $response->headers->set("Content-Type", "application/json; charset=utf-8");
        $response->setContent($result);
        return $response;
    }

    // ! creating the validation method. 

    public function validationMethod(Request $request)
    {

        $result_code = "0";
        $result_description = "Accepted validation request.";
        return $this->createValidationResponse($result_code, $result_description);
    }

    // ! registering URLs . 

    public function registerURLS()
    {
        $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $this->generateAccessTokens())); //setting custom header


        $curl_post_data = array(
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
        return $curl_response;
    }

    public function simulateTransaction(Request $request)
    {

        $url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $this->generateAccessTokens())); //setting custom header

        $curl_post_data = array(
            'ShortCode' => '600754',
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'Msisdn' => '254708374149',
            'BillRefNumber' => '00000'
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
        return $curl_response;
    }
}
