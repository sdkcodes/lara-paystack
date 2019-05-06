<?php

use Sdkcodes\LaraPaystack\PaystackService;
Route::get("start-paystack", function(PaystackService $paystack){
    return $paystack->initializeTransaction([]); 
});