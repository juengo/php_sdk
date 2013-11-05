<?php
/**
 * Copyright 2013 Juengo Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
 


if (!function_exists('json_decode')) {
  throw new Exception('Juengo needs the JSON PHP extension.');
}

class Juengo{

	// automatically configurable by contractor
	private $apikey = null;
	private $secret = null;
	private $debug = false;	
	private $sandbox = true;
	
	// Configurable within file
	private static $apiVersion = "2.0";		
	private $curlEnabled = true;
	private static $domain_extension = array("default"="com", "USA"=>"com", "GREECE"=>"gr");
	
	
	function __construct($config) {
		if(!isset($config['APIKEY']) || empty($config['APIKEY']) || !isset($config['SECRET']) || empty($config['SECRET'])){
			$this->showLog('Post parameters are missing');
		}
		$this->apikey = $config['APIKEY'];
		$this->secret = $config['SECRET'];		
		$this->debug = $config['DEBUG'];
		$this->sandbox = (!isset($config['SANDBOX']) ? true : ($config['SANDBOX']==false ? false : true));
		
		if(!function_exists('curl_init')) {
			$this->curlEnabled=false;
			$this->showLog('CURL PHP extension not found. Changing to regular HTTP posts.');
		}	
	}

	private function api_path(){
		if($this->sandbox==true){
			return "http://sandbox.juengo.com";	
		}
		else{
			return "https://developers.juengo.".$this->domain_extension['default'];
		}
	}
	
	
	private function httpRequest($url, $data, $optional_headers=null){
		return ($this->curlEnabled==true ? 
			$this->curlPost($url, $data, array('REQUEST_URI: '.$_SERVER['SERVER_NAME'])) : 
			$this->httpPost($url, $data, array('REQUEST_URI: '.$_SERVER['SERVER_NAME']))
		);
	}	

	private function curlPost($url, $data, $optional_headers = null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		($optional_headers !== null ? curl_setopt($ch, CURLOPT_HTTPHEADER, $optional_headers) : '');		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		$response = curl_exec ($ch);		
		curl_close ($ch);	
		return $response;
	}
	
	private function httpPost($url, $data, $optional_headers = null){
	  $params = array('http' => array(
				  'method' => 'POST',
				  'content' => $data
				));
	  if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	  }
	  $ctx = stream_context_create($params);
	  $fp = @fopen($url, 'rb', false, $ctx);
	  if (!$fp) {
		$this->showLog("Problem with $url, $php_errormsg");
	  }
	  $response = @stream_get_contents($fp);
	  if ($response === false) {
		$this->showLog("Problem reading data from $url, $php_errormsg");
	  }
	  return $response;
	}

	private function showLog($msg){
		if($this->debug==true){
			ini_set("display_errors", "1");
			error_reporting(E_ALL);
			exit($msg);	
		}			
	}
	
	public function curlOn($boolean){		
		if($boolean==false){
			$this->curlEnabled = false;
		}
		else{
			$this->curlEnabled = true;				
		}
	}


	public function getProfile(){
		$jsonResponse = $this->httpRequest($this->api_path()."/api/stable/profile/", 'apikey='.$this->apikey.'&secret='.$this->secret.'');		
		return json_decode($jsonResponse)->response;	
	}
	
	public function getOffers($limit){
		$jsonResponse = $this->httpRequest($this->api_path()."/api/stable/offers/", 'apikey='.$this->apikey.'&secret='.$this->secret.(!isset($limit) || empty($limit) ? '' : '&limit='.$limit));
		return json_decode($jsonResponse)->response;
	}
	
	public function getMerchant($merchant){
		if(!isset($merchant) || empty($merchant)){
			$this->showLog("Merchant unique ID is missing");	
			return false;
		}
		else{
			$jsonResponse = $this->httpRequest($this->api_path()."/api/stable/merchant/", 'apikey='.$this->apikey.'&secret='.$this->secret.(!isset($merchant) || empty($merchant) ? '' : '&merchant_id='.$merchant));
			return json_decode($jsonResponse)->response;
		}
	}
	
	public function getTransactions($type='any', $limit=null, $status=null){
		if($type=='in'){
			$type_suffix = 'in/';
		}
		elseif($type=='out'){
			$type_suffix = 'out/';			
		}
		else{
			$type_suffix = '';	
		}
		$jsonResponse = $this->httpRequest($this->api_path()."/api/stable/transactions/".$type_suffix, 'apikey='.$this->apikey.'&secret='.$this->secret.(!isset($limit) || empty($limit) ? '' : '&limit='.$limit).(!isset($status) || empty($status) ? '' : '&status='.($status=='complete' ? 'complete' : 'pending')));
		return json_decode($jsonResponse)->response;
	}
	
	public function newReward($juengos=0, $users=array()){
		if(empty($juengos) || !isset($juengos) || $juengos<=0 || !is_int($juengos)){
			showLog("Invalid juengos number. Must be an integer value");
		}
		
		$rew_users = array();
		
		if(is_array($users)){
			$rew_users = $users;
		}
		else{
			array_push($rew_users, $users);		
		}
		
		$jsonResponse = $this->httpRequest($this->api_path()."/api/stable/reward/", 'apikey='.$this->apikey.'&secret='.$this->secret.'&juengos='.$juengos.'&users='.serialize($rew_users));
		return json_decode($jsonResponse)->response;
	}	
	
}


?>
