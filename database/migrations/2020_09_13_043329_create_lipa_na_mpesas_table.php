<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLipaNaMpesasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lipa_na_mpesas', function (Blueprint $table) {
            $table->id();
            $table->longText('MerchantRequestID')->nullable();
            $table->longText('CheckoutRequestID')->nullable();
            $table->float('Amount')->nullable();
            $table->longText('MpesaReceiptNumber')->nullable();
            $table->longText('TransactionDate')->nullable();
            $table->longText('PhoneNumber')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lipa_na_mpesas');
    }
}
