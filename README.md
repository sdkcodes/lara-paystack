# LaraPaystack
## A laravel 5 sdk to interface with paystack endpoints
### About
<hr>
This package abstracts Paystack's endpoints into simple methods 

You can find all paystack's endpoints at [https://developers.paystack.co/reference](https://developers.paystack.co/reference)

### Requirements
- PHP >= 7.0
- Composer
- Laravel >= 5.4

### Installation
* Run `composer require sdkcodes/lara-paystack` to install the latest version of LaraPaystack

### Features
- Flexibility: This package basically gives you total control and flexibility over the data you pass to paystack with a few helper methods here and there to help you when needed.

### How payment on Paystack works
In order to collect payments with paystack from your customers, this is how the flow generally looks like:
1. You initialize a transaction with paystack by sending certain to the paystack api, this data usually contains things like the [amount and email (and also name and phone number of the customer)]
2. Paystack responds with a payment url (or authorization url) which is a page on paystack's site that you must redirect your customer to. The actual payment will happen on this page
3. Once payment is complete and successful on this page (i.e your customer has been debited by paystack), paystack redirects the customer back to your site (to a url [callback url] you already specified while initiating transaction from step 1). When redirecting the customer back to your site, paystack will also send the transaction reference of the just concluded transaction as a query parameter.
4. On your callback url now, you should then use the transaction reference returned to verify if the transaction was actually genuine or not before you then proceed to give value to the customer.


### Basic Usage
- You can create a new instance of the Paystack class as such `$paystack = new PaystackService()`, and immediately start to use all methods offered by the SDK.
- You can as well inject it into your constructors or methods e.g
``` 
public function talkToPaystack(Request $request, PaystackService $paystack){
        // Laravel automatically does the instantiation for you through its Service Container
        // and you can start to use the paystack object in your method
}
```
- With LaraPaystack, you can initiate transaction and redirect to payment url in just one line:

`$paystack->initializeTransaction($data)->redirectNow(); `

Below is a sample route and controller that uses the LaraPaystack package to make and verify payment
```
<?php
//web.php
Route::post("/submit-payment-request", "PaymentController@initializeTransaction");

Route::get("/payment-callback", "PaymentController@respondToCallback");
```

```
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sdkcodes\LaraPaystack\PaystackService;

class PaymentController{

    public function initializeTransaction(Request $request, PaystackService $paystack){
        $data = [
            'amount' => 10000 // should be in kobo: this can come from a form submitted by the user
            'email' => $request->email,
            'callback_url' => url('route-to-handle-paystack-callback')
        ];
        // You can check this link for all fields that you can send to paystack https://developers.paystack.co/v1.0/reference#initialize-a-transaction

        // this line will initialize transaction with paystack and automatically help you redirect to the payment url returned by paystack
        $paystack->initializeTransaction($data)->redirectNow(); 
    }

    /** 
    * Controller method to handle response from paystack after payment
    * @param trxref
    */
    public function respondToCallback(Request $request, PaystackService $paystack){
        //sent to us by paystack after successful payment
        $transRef = $request->trxref; 

        // this will verify if the transaction is valid and someone is not just trying to play funny with our payment
    	$paystack->verifyTransaction($transRef); 
        
        //you can now do anything you want with the paymentData
    	$paymentData = $paystack->getPaymentData();
        
    }
}
```

### More
As it stands, here are some of the things you can do with this package:
* Initiate Transaction
* Verify Transaction
* List Transactions
* Create transfer recipient
* Initiate transfer
* Finalize transfer
* List transfer recipients
* List Banks [new]
* Initiate bulk transfer [new]

I plan to keep updating the package and as time permits, cover all of Paystack's endpoints.

You're welcome to contribute as well.

### Expectations
LaraPaystack requires some environment variables be set, and it uses the appropriate paystack key based on your enviroment
* PAYSTACK_TEST_SECRET_KEY=[Your_Paystack_Secret_Test_key]
* PAYSTACK_LIVE_SECRET_KEY=[Your_Paystack_Secret_Live_key]
The `PAYSTACK_TEST_SECRET_KEY` will be automatically used if your `APP_ENV` matches any of the following values: local, test, staging

The `PAYSTACK_LIVE_SECRET_KEY` will be automatically used if your `APP_ENV` matches any of the following values: production

However, you can easily override the environment values by setting a `USE_ENV_AS` variable in your .env 

`USE_ENV_AS` can have either values of `production` to simulate production environment or `testing` to simulate test environment

## Examples
### Bulk Transfer
To use the package for Paystack's bulk transfer, you need to first disable OTP from your paystack dashboard.

You will use the `doBulkTransfer(array $recipients, string $currency="NGN", string $source="balance")` method to carry out bulk transfer.

Note: Only the first argument to the method is required

```
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sdkcodes\LaraPaystack\PaystackService;

class PaymentController{

    /**
    * Initiate bulk transfer and send money to multiple bank accounts at the same time
    */
    public function doMultipleTransfers(Request $request, PaystackService $paystack){
        
        // You can check this link for all fields that you can send to paystack https://developers.paystack.co/reference#initiate-bulk-transfer

        $people = array(
            [
                'amount' => 50000,
                'recipient' => 'RCP_m9yzgv4tbi6f20b'
            ],
            [
                'amount' => 20000,
                'recipient' => 'RCP_z6b9zeky5z379dn'
            ]
        );
        
        $response = $paystack->initiateBulkTransfer($people);
      
    }

}
```

### TODO
Complete documentation

### Inspiration
This package was highly inspired by [@Unicodeveloper's](https://github.com/unicodeveloper) [Laravel Paystack package](https://github.com/unicodeveloper/laravel-paystack)

### License
The package is as free as a bird.

A star of the repo would highly be appreciated