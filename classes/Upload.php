<?php
include_once "HttpConnection.php";

class Upload
{
	public $httpObject;
	public $accessToken;

	function __construct($accessToken)
	{
		if(isset($accessToken) && $accessToken)
		{
			$this->accessToken = $accessToken;
			$this->httpObject = new HttpConnection();
		}
		else
		{
			return false;
		}
	}

	function newUpload($appLocation, $appName, $digestEnable)
	{
		
		$digestResult = null;
		if(!$digestEnable)
		{
			$appDigest = hash_file('sha256', $appLocation);
			$params = array('access_token' => $this->accessToken, 'app_digest' => $appDigest, 'app_name' => $appName);
			$result = $this->httpObject->post("UPLOAD_NEW", $params);
			$result = json_decode($result);
			if($result->meta->code == APP_DIGEST_NOT_EXIST)
			{
				$digestEnable = true;
				$digestResult = $result->meta->code;
			}
			else
			{
				$digestEnable = false;
				return $result;
			}
		}

		if($digestEnable || $digestResult == APP_DIGEST_NOT_EXIST) 
		{
			$params = array('access_token' => $this->accessToken, 'app' => '@'.$appLocation, 'app_name' => $appName);
			$result = $this->httpObject->post("UPLOAD_NEW", $params);
			$result = json_decode($result);
			return $result;
		}
		
	}

	function uploadList($count, $thisSession)
	{
		$params = array('access_token' => $this->accessToken, 'count' => $count, "this_ses" => $thisSession);
		$result = $this->httpObject->get("UPLOAD_LIST", $params);
		$result = json_decode($result);
		return $result;
	}

	function uploadDetails($uploadId)
	{
		$params = array('access_token' => $this->accessToken, "upload_id" => $uploadId);
		$result = $this->httpObject->get("UPLOAD_DETAILS", $params);
		$result = json_decode($result);
		return $result;
	}
}
?>