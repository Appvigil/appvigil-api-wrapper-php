<?php
include_once "KLogger.php";

$options = getopt("K:S:t:L:N:T:vh:C:D:", array(
	"api-key:", 
	"api-secret:", 
	"ttl:", 
	"app-loc:", 
	"app-name:",
	"access-token:",
	"disable-digest-check",
	"verbose",
	"help",
	"credentials:",
	"app-key:"
	));

$variableSet = "";
$variableError = array();
$helpArray = array(
	"-K \t --api-key" => "* Require the ApiKey before executing your script",
	"-S \t --api-secret" => "* Require the ApiSecret before executing your script",
	"-L \t --app-loc" => "* Require the AppLocation before start your scan",
	"-N \t --app-name" => "* Require the App name",
	"-t \t --ttl  " => "(optional) value to set ttl(TIME TO LIVE) for access_token",
	"-C \t --credentials  " => "(optional) set credentials to launch scan",
	"   \t --disable-digest-check  " => "(optional) to check digest exist or not",
	"-h \t --help  " => "Show this message",
	"-v \t --verbose" => "Run verbosely",
	);

$variableError["help"] = "use --help to show the help's message";
$variableError["required"] = "required values";

if(isset($options["verbose"]) || isset($options["v"]))
{ 
	$verbose = 1; 
} else 
{ 
	$verbose = 2; 
}

if(isset($options["help"]) || isset($options["h"]))
{ 
	$help = true; 
} else 
{ 
	$help = false; 
}
//disable digest check 
if(isset($options["disable-digest-check"]))
{ 
	$disableDigestCheck = true; 
} else 
{ 
	$disableDigestCheck = false; 
}
//ttl
if(isset($options["ttl"]))
{ 
	$ttl = $options["ttl"]; 
} 
else if(isset($options["t"]))
{ 
	$ttl = $options["t"]; 
} 
else 
{ 
	$ttl = DEFAULT_TTL_VALUE;
}

//credentials
if(isset($options["credentials"]) && $options["credentials"])
{ 
	$credentials = $options["credentials"]; 
} 
else if(isset($options["C"]) && $options["C"])
{ 
	$credentials = $options["C"]; 
} 
else
{ 
	$credentials = null;
}

//credetials validation
if(isset($credentials) && $credentials)
{
	$credentialsArray = explode(',', $credentials);
	foreach ($credentialsArray as $key => $value) 
	{
		if(preg_match('#[^a-zA-Z0-9]#',$value) && $value)
		{
		    $variableSet .= "no";
			$variableError["credential"] = "Credentails contain special characters, please check!";
		}
		if (strlen($value) < 8 && $value)
		{
		    $variableSet .= "no";
			$variableError["credential"] = "All credentails should be 8 character's of length!";
		}
	}
} 


//api-key
if(isset($options["api-key"]) && $options["api-key"])
{
	$apiKey = $options["api-key"];
}
else if(isset($options["K"]) && $options["K"])
{
	$apiKey = $options["K"];
}
else
{
	$apiKey = false;
	$variableSet .= "no";
	$variableError["api-key"] = "use --api-key to set API key";
}
//secret-key
if((isset($options["api-secret"])  && $options["api-secret"]))
{
	$apiSecret = $options["api-secret"];
}
else if(isset($options["S"]) && $options["S"])
{
	$apiSecret = $options["S"];
}
else
{
	$apiSecret = false;
	$variableSet .= "no";
	$variableError["api-secret"] = "use --api-secret to set API Secret";
}
//app location
if(isset($options["app-loc"]))
{ 
	$appLocation = $options["app-loc"];
	if (!file_exists($appLocation))
	{
		$variableSet .= "no";
		$variableError["app-loc"]= "APK file not exist"; 
	}
} 
else if(isset($options["L"]))
{ 
	$appLocation = $options["L"]; 
	if (!file_exists($appLocation) || strpos( $appLocation, ".apk") === false)
	{
		$variableSet .= "no";
		$variableError["app-loc"]= "--app-loc APK file not exist"; 
	}
}
else 
{ 
	$appLocation = false;
	$variableSet .= "no";
	$variableError["app-loc"]= "use --app-loc to set apk file location"; 
}

//app name
if(isset($options["app-name"]))
{ 
	$appName = $options["app-name"]; 
} 
else if(isset($options["N"]))
{ 
	$appName = $options["N"]; 
}
else 
{ 
	$appName = false;
	$variableSet .= "no";
	$variableError["app-name"]= "use --app-name to set file name with .apk extention"; 
}

//app key
if(isset($options["app-key"]))
{ 
	$app_key = $options["app-key"]; 
} 
else if(isset($options["D"]))
{ 
	$app_key = $options["D"]; 
}
else
{
	$app_key = null;
}
?>