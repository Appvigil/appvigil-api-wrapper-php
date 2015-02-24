<?php
/**
* 
*/
class HttpConnection
{

	public $user_agent;
	function __construct()
	{
		# code...
	}

	function get($res, $params)
	{
		include "config/Config.php";

		if(isset($this->user_agent) && $this->user_agent != '')
			$user_agent = $this->user_agent;

	    $url = $apiProto.'://'.$apiHost.'/'.$resource[$res];

	    $url .= '?' . http_build_query($params);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	    $data = curl_exec($ch);
		
	    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    if ($status == 200) {
	        return $data;
	    } else {
	        return $status;
	    }
	}

	function post($res, $params)
	{
		include "config/Config.php";

		if(isset($this->user_agent) && $this->user_agent != '')
			$user_agent = $this->user_agent;
		
	    $url = $apiProto.'://'.$apiHost.'/'.$resource[$res];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		//curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'curl_progress_callback');
		curl_setopt($ch, CURLOPT_NOPROGRESS, false);
		curl_setopt($ch, CURLOPT_BUFFERSIZE,64000);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		if(PHP_VERSION_ID >= 50500 && isset($params["app"]))
	    {
	    	$params["app"] = str_replace("@","",$params["app"]);
	    	$params["app"] = new CurlFile($params["app"], 'apk');
	    	curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
	    }

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$data = curl_exec($ch);
	    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

	    if ($status == 200) {
	        return $data;
	    } else {
	        return $status;
	    }
	}
}

//for uploading callback from curl
function curl_progress_callback($dltotal, $dlnow, $ultotal, $ulnow)
{
	echo ".";
}
?>