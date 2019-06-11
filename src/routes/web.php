<?php

use Sdkcodes\LaraPaystack\PaystackService;
Route::get("start-paystack", function(PaystackService $paystack){
    return $paystack->initializeTransaction([]); 
});

Route::get('banks', function(PaystackService $paystack){
	var_dump($paystack->listBanks());
});