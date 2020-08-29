<?php

namespace App\Http\Controllers;

use App\PaymentsC2B;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentsC2BController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentsC2B  $paymentsC2B
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentsC2B $paymentsC2B)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentsC2B  $paymentsC2B
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentsC2B $paymentsC2B)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentsC2B  $paymentsC2B
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentsC2B $paymentsC2B)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentsC2B  $paymentsC2B
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentsC2B $paymentsC2B)
    {
        //
    }
    public function makePayment(){
        $mpesa= new \Safaricom\Mpesa\Mpesa();

        // $b2bTransaction=$mpesa->c2b($ShortCode, $CommandID, $Amount, $Msisdn, $BillRefNumber );

            $CommandID = 121212;

            $b2bTransaction=$mpesa->c2b(600141, $CommandID, 200, +254792107437,1212);

            return "This is successfull.";
    }

    public function mpesaConfirmation(Request $request)
    {
        // $content=json_decode($request->getContent());
        // $mpesa_transaction = new MpesaTransaction();
        // $mpesa_transaction->TransactionType = $content->TransactionType;
        // $mpesa_transaction->TransID = $content->TransID;
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
        // $mpesa_transaction->save();
        // Responding to the confirmation request

        $record = new PaymentsC2B();
        $record->name = 'DataBase Record Called.';
        $record->save();

        $response = new Response();
        $response->headers->set("Content-Type","text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult"=>"Success"]));
        return $response;
    }

        /**
     * J-son Response to M-pesa API feedback - Success or Failure
     */
     public function createValidationResponse($result_code, $result_description){
        $result=json_encode(["ResultCode"=>$result_code, "ResultDesc"=>$result_description]);
        $response = new Response();
        $response->headers->set("Content-Type","application/json; charset=utf-8");
        $response->setContent($result);
        return $response;
    }
    /**
     *  M-pesa Validation Method
     * Safaricom will only call your validation if you have requested by writing an official letter to them
     */
    public function mpesaValidation(Request $request)
    {
        $result_code = "0";
        $result_description = "Accepted validation request.";
        return $this->createValidationResponse($result_code, $result_description);
    }

/**
     * M-pesa Register Validation and Confirmation method
     */
     public function mpesaRegisterUrls()
     {
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl');
         curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '. $this->generateAccessToken()));
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($curl, CURLOPT_POST, true);
         curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'ShortCode' => '600141',
            'ResponseType' => 'Completed',
            'ConfirmationURL' => 'https://safaricommobilemoneyintegration.georgekprojects.tk/v1/transaction/confirmation',
            'ValidationURL' => 'https://safaricommobilemoneyintegration.georgekprojects.tk/v1/validation'
         )));
         $curl_response = curl_exec($curl);
         echo $curl_response;
     }

     public function generateAccessToken()
     {
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

     public function customerMpesaSTKPush()
     {
         $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generateAccessToken()));
         $curl_post_data = [
             //Fill in the request parameters with valid values
             'BusinessShortCode' => 174379,
             'Password' => $this->lipaNaMpesaPassword(),
             'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
             'TransactionType' => 'CustomerPayBillOnline',
             'Amount' => 5,
             'PartyA' => 254792107437, // replace this with your phone number
             'PartyB' => 174379,
             'PhoneNumber' => 254792107437, // replace this with your phone number
             'CallBackURL' => 'https://53a21acaa793.ngrok.io/callBackForTKPush',
             'AccountReference' => "H-lab tutorial",
             'TransactionDesc' => "Testing stk push on sandbox"
         ];

         $data_string = json_encode($curl_post_data);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($curl, CURLOPT_POST, true);
         curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
         $curl_response = curl_exec($curl);
         return gettype($curl_response);
        // $mpesa= new \Safaricom\Mpesa\Mpesa();

        // $stkPushSimulation=$mpesa->STKPushSimulation(174379, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);
     }

     public function callBackForTKPush(){
        $record = new PaymentsC2B();
        $record->name = 'DataBase Record Called.';
        $record->save();
     }
     public function lipaNaMpesaPassword()
     {
         $lipa_time = Carbon::rawParse('now')->format('YmdHms');
         $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
         $BusinessShortCode = 174379;
         $timestamp =$lipa_time;
         $lipa_na_mpesa_password = base64_encode($BusinessShortCode.$passkey.$timestamp);
         return $lipa_na_mpesa_password;
     }
}
