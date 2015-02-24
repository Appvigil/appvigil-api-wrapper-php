<?php
include_once "HttpConnection.php";

class AccessToken
{

	public $httpObject;
	public $apiKey;
	public $apiSecret;
	public $accessToken;

	function __construct()
	{
		$numArgs = func_num_args();
		$getArgs = func_get_args();
		if($numArgs == 2)
		{
			$this->apiKey = $getArgs[0];
			$this->apiSecret = $getArgs[1];
		}
		else if($numArgs == 1)
		{
			$this->accessToken = $getArgs[0];
		}
		else
		{
			return  false;
		}
		$this->httpObject = new HttpConnection();
	}

	function requestNewAccessToken($ttl = DEFAULT_TTL_VALUE, $app_key = null)
	{
		$params = array('api_key' => $this->apiKey, 'api_secret' => $this->apiSecret, "ttl" => $ttl, "appvigil_app_key" => $app_key);
		$result = $this->httpObject->get("ACCESS_TOKEN_NEW", $params);
		$result = json_decode($result);
		return $result;
	}

	function renewAccessToken($newTtl = DEFAULT_TTL_VALUE)
	{
		$params = array('access_token' => $this->accessToken, 'ttl' => $newTtl);
		$result = $this->httpObject->get("ACCESS_TOKEN_RENEW", $params);
		$result = json_decode($result);
		return $result;
	}

	function viewAccessToken()
	{
		$params = array('access_token' => $this->accessToken);
		$result = $this->httpObject->get("ACCESS_TOKEN_VIEW", $params);
		$result = json_decode($result);
		return $result;
	}

	function flushAccessToken()
	{
		$params = array('access_token' => $this->accessToken);
		$result = $this->httpObject->get("ACCESS_TOKEN_FLUSH", $params);
		$result = json_decode($result);
		return $result;
	}
}

?>

