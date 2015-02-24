<?php

// usage: php scan-launcher.php --api-key 1234567890 --api-secret 1234567890 --app-loc testapp.apk

require_once "auto-loader.php";

$errorMessage = array();
$log = new KLogger ($verbose);

checkArguments($help, $helpArray, $variableSet, $variableError);

if($apiKey && $apiSecret && $appLocation)
{
	$accessTokenObject = new AccessToken($apiKey , $apiSecret);
	$log->LogInfo("Generating AccessToken...");

	$newAccessTokenResult = $accessTokenObject->requestNewAccessToken($ttl, $app_key);
	if(!is_object($newAccessTokenResult) && $newAccessTokenResult == 500)
	{
		$log->LogInfo("Damn!!! Api internal error...");
		$log->LogInfo("Reason: ".$newAccessTokenResult);
		$log->LogInfo("I am Sorry...Quitting");
		exit ($newAccessTokenResult);
	}
	$log->LogDebug(print_r($newAccessTokenResult, true));
	if(!isset($newAccessTokenResult->meta->code))
	{
		$log->LogInfo("Damn!!! Unable to generate AccessToken...");
		$log->LogDebug("Reason: ".print_r($newAccessTokenResult, true));
		$log->LogInfo("I am Sorry...Quitting");
		exit (1);
	}
	$metaCode = $newAccessTokenResult->meta->code;
	if($metaCode == SUCCESS)
	{
		$log->LogInfo("AccessToken Generated :) ");
		$accessToken = $newAccessTokenResult->response->access_token;
		$log->LogInfo("AccessToken is: ".$accessToken);
	}
	else{
		$log->LogInfo("Damn!!! Unable to generate AccessToken...");
		$log->LogInfo("Reason: ".$newAccessTokenResult->response->message);
		$log->LogInfo("I am Sorry...Quitting");
		exit ($newAccessTokenResult->meta->code);
	}	
		$uploadObject = new Upload($accessToken);
		$log->LogInfo("Uploading APK file to Appvigil Cloud...This may take a while");
		$uploadResult = $uploadObject->newUpload($appLocation, $appName, $disableDigestCheck);

		$log->LogDebug("\n".print_r($uploadResult, true));

		if(is_object($uploadResult))
		{
			$metaCode = $uploadResult->meta->code;
			if($metaCode == UPLOAD_SUCCESS || $metaCode == DIGEST_SUCCESS )
			{
				$log->LogInfo("App has been uploaded successfully...");
				$uploadId = $uploadResult->response->upload_id;
				$log->LogInfo("Upload ID is: ".$uploadId);
			}
			else{
				$log->LogInfo("Damn!!! Unable to upload apk file to Appvigil Cloud...");
				$log->LogInfo("Reason: ".$uploadResult->response->message);
				$log->LogInfo("I am Sorry...Quitting");
				exit ($uploadResult->meta->code);
			}
		}
		else
		{
			$log->LogInfo("Oppss!!! Provided apk file is not a valid android app...");
			$log->LogInfo("I am Sorry...Quitting");
			exit (1);
		}
		
		
			$scanObject = new Scan($accessToken);
			$log->LogInfo("Launching vulnerability scan..");
			$scanStartResult = $scanObject->startScan($uploadId, $credentials);
			$log->LogDebug(print_r($scanStartResult, true));

			$metaCode = $scanStartResult->meta->code;
			if($metaCode == SCAN_STARTED)
			{
				$log->LogInfo("Scan launched successfully...");
				$scanId = $scanStartResult->response->scan_id;
				$log->LogInfo("Scan ID is: ".$scanId);
			}
			else{
				$log->LogInfo("Damn!!! Unable to launch scan...");
				$log->LogInfo("Reason: ".$scanStartResult->response->message);
				$log->LogInfo("I am Sorry...Quitting");
				exit ($scanStartResult->meta->code);
			}

			
				$log->LogInfo("Scan running...it will be over in few minutes ");
				$log->LogInfo("Please wait...");
				$statusOver = false;
				$startTime = time();

				while(!$statusOver)
				{
					$scanStatusResult = $scanObject->scanStatus($scanId);
					if(is_object($scanStatusResult))
					{
						$metaCode = $scanStatusResult->meta->code;
					}
					if($metaCode == SUCCESS && is_object($scanStatusResult))
					{
						$scanStatus = $scanStatusResult->response->scan_status;
						
						if($scanStatus != "Pending" && $scanStatus != "Running"){
							$statusOver = true;
							$log->LogInfo("Scan Finished.");
							$log->LogDebug(print_r($scanStatusResult, true));
							$log->LogInfo("Scan Status (temp): ".$scanStatus);
							$log->LogInfo("Scan report is available at: ".$scanStatusResult->response->report_url);
							break;
						}
					}
					else{
						$log->LogInfo("Damn!!! Unable to fetch scan status...");
						$log->LogInfo("ScanStatusResult".print_r($scanStatusResult, true));
						$log->LogInfo("Let me retry...");
					}
					sleep(5);
				}
}
else
{
	$log->LogError("--api-key, --api-secret and --app-loc required to launch the scan");
}

?>
