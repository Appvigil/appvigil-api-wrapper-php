<?php
include_once "HttpConnection.php";

class Scan
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

	function startScan($uploadId, $credentials)
	{
		$params = array('access_token' => $this->accessToken, 'upload_id' => $uploadId, 'credential_id' => $credentials);
		$result = $this->httpObject->get("SCAN_START", $params);
		$result = json_decode($result);
		return $result;
	}

	function listScan($statusType)
	{
		$params = array('access_token' => $this->accessToken, "status_type" => $statusType);
		$result = $this->httpObject->get("SCAN_LIST", $params);
		$result = json_decode($result);
		return $result;
	}

	function scanStatus($scanId)
	{
		$params = array('access_token' => $this->accessToken, 'scan_id' => $scanId);
		$result = $this->httpObject->get("SCAN_STATUS", $params);
		$result = json_decode($result);
		return $result;
		
	}

}
?>