<?php

namespace App\Http\Controllers;

use App\PaymentsC2B;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentsC2BController extends Controller
{
    public function lipaNaMpesaPassword()
    {
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $BusinessShortCode = 174379;
        $timestamp =$lipa_time;
        $lipa_na_mpesa_password = base64_encode($BusinessShortCode.$passkey.$timestamp);
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
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->generateAccessToken()));
        $curl_post_data = [
            //Fill in the request parameters with valid values
            'BusinessShortCode' => 174379,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 5,
            'PartyA' => 254796446324, // replace this with your phone number
            'PartyB' => 174379,
            'PhoneNumber' => 254796446324, // replace this with your phone number
            'CallBackURL' => 'https://safaricommobilemoneyintegration.georgekprojects.tk/api/v1/hlab/stk/pushCallBack',
            'AccountReference' => "H-lab tutorial",
            'TransactionDesc' => "Testing stk push on sandbox"
        ];
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        return $curl_response;
    }
    public function generateAccessToken()
    {
        $consumer_key="CxQOGfi82pAd7nUI73TiR0YshUN2pUAJ";
        $consumer_secret="jeO1yPGrUptywyJw";
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

    public function callBackForTheSTKPush(Request $request){
        $PaymentsC2B = new PaymentsC2B();

        $content=json_decode($request->getContent());
        if ($PaymentsC2B->name) {
            # code...
            $PaymentsC2B->name = $content->FirstName;
        } else {
            # code...
            $PaymentsC2B->name = 'This is the name.';
        }
        
        
        $PaymentsC2B->save();

        return "Success";
    }
}
