<?php

namespace Sdkcodes\LaraPaystack;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\App;

/**
 * Interface with paystack api
 */
class PaystackService
{
	protected $secretKey;

	/**
	 * Base url of Paystack
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * Instance of Guzzle client
	 * @var object
	 */
	protected $client;

	/**
	 * Redirect to payment url
	 * @return $this
	 */
	protected $redirectUrl;

	function __construct()	
	{
		$this->setSecretKey();
		$this->setBaseUrl();
		$this->setRequestOptions();
	}

	public function setSecretKey(){
		
		if (config('lara-paystack.use_env_as') == "production"){
			$this->secretKey = env('PAYSTACK_LIVE_SECRET_KEY');
			return;
		}
		if (config('lara-paystack.use_env_as') == "testing"){
			$this->secretKey = env('PAYSTACK_TEST_SECRET_KEY');
			return;
		}
		if (App::environment(['local', 'test', 'staging'])){
			$this->secretKey = env('PAYSTACK_TEST_SECRET_KEY');
		}
		elseif(App::environment(['production'])){
			$this->secretKey = env('PAYSTACK_LIVE_SECRET_KEY');
		}
		
	}

	public function setBaseUrl(){
		$this->baseUrl = "https://api.paystack.co";
	}

	public function getSecretKey(){
		return $this->secretKey;
	}

	public function getBaseUrl(){
		return $this->baseUrl;
	}

	public function setPaymentUrl($url){
		$this->redirectUrl = $url;
	}

	public function getPaymentUrl(){
		return $this->redirectUrl;
	}
	/**
	 * Set options for making the Client request
	 */
	private function setRequestOptions()
	{
	    $authBearer = 'Bearer '. $this->getSecretKey();

	    $this->client = new Client(
	        [
	            'base_uri' => $this->baseUrl,
	            'headers' => [
	                'Authorization' => $authBearer,
	                'Content-Type'  => 'application/json',
	                'Accept'        => 'application/json'
	            ]
	        ]
	    );
	}

	private function doGetRequest($relativeUrl, $usePayload=true){
		
		$payload["secretKey"] = $this->getsecretKey();
		try{
			if ($usePayload===false){
				$this->response = $this->client->request("GET", $relativeUrl);	
			}
			else{
				$this->response = $this->client->request("GET", $relativeUrl, ["query" => $payload]);	
			}
			
		}
		catch(ConnectException $ex){
			throw new \Exception("Unable to establish connection to Paystack.", 1);
			
		}

	}

	private function doPutRequest($relativeUrl, $payload = []){
		try{
			$this->response = $this->client->request("PUT", $relativeUrl, ["json" => $payload]);
		}
		catch(ConnectException $ex){
			throw new \Exception("Unable to establish connection to Paystack", 1);
			
		}
	}

	private function doDeleteRequest($relativeUrl, $payload=[]){
		try{
			$this->response = $this->client->request("DELETE", $relativeUrl, ["json" => $payload]);
		}
		catch(ConnectException $ex){
			throw new \Exception("Unable to establish connection to Paystack", 1);	
		}	
	}

	private function doPostRequest($relativeUrl, $payload = []){
		
		$payload["secretKey"] = $this->getsecretKey();
		try{
			$this->response = $this->client->request("POST", $relativeUrl, ["json" => $payload]);
		}
		catch(ConnectException $ex){
			throw new \Exception("Unable to establish connection to Paystack", 1);
			
		}

	}

	/**
	 * Initialize payment to paystack
	 * @param array $payload 
	 * @return Sdkcodes\PaystackService
	 */
	public function initializeTransaction($payload){
		
		$this->doPostRequest('transaction/initialize', $payload);
		$url = $this->getResponse()->data->authorization_url;
		$this->setPaymentUrl($url);

		return $this;
	}

	/**
	 * Redirect to payment url
	 * @return type
	 */
	public function redirectNow()
	{

	    return redirect($this->getPaymentUrl());
	}

	public function verifyTransaction($ref){
		$this->doGetRequest("transaction/verify/".$ref);
		$response = $this->getResponse();
		$status = $response->data->status;
		if (strtolower($status) == "success"){
			return $this->getResponse();
		}
		else{
			throw new \Exception("Invalid transaction. Transaction could not be completed");
		}
	}

	public function getPaymentData(){
		return $this->getResponse()->data;
	}

	public function getTransactions(){
		$this->doGetRequest('transaction');
		return $this->getResponse();
	}

	public function createTransferRecipient($payload){
		$this->doPostRequest('transferrecipient', $payload);
		return $this->getResponse();
	}

	public function initiateTransfer($payload){
		$this->doPostRequest('transfer', $payload);
		return $this->getResponse();
	}

	public function listTransferRecipients(){
		$this->doGetRequest('transferrecipient');
		return $this->getResponse();
	}

	/**
	 * Return the response from the latest API call
	 * @return object
	 */
	public function getResponse(){
		return json_decode($this->response->getBody());
	}

	public function finalizeTransfer($payload){
		$this->doPostRequest('transfer/finalize_transfer', $payload);
		return $this->getResponse();
	}
}