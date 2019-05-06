# LaraPaystack
## A laravel 5 sdk to interface with paystack endpoints
### About
<hr>
This package abstracts Paystack's endpoints into simple methods 
You can find all paystack endpoints at [https://developers.paystack.co/reference](https://developers.paystack.co/reference)

### Requirements
- PHP >= 7.0
- Composer
- Laravel >= 5.4

### Installation
* To install LaraPaystack

### Expectations
LaraPaystack requires some environment variables be set, and it uses the appropriate paystack key based on your enviroment
* PAYSTACK_TEST_SECRET_KEY=[Your_Paystack_Secret_Test_key]
* PAYSTACK_LIVE_SECRET_KEY=[Your_Paystack_Secret_Live_key]
The `PAYSTACK_TEST_SECRET_KEY` will be automatically used if your `APP_ENV` matches any of the following values: local, test, staging

The `PAYSTACK_LIVE_SECRET_KEY` will be automatically used if your `APP_ENV` matches any of the following values: production

However, you can easily override the environment values by setting a `USE_ENV_AS` variable in your .env
`USE_ENV_AS` can have either values of `production` to simulate production environment or `testing` to simulate test environment

### TODO
Complete documentation