<?php

$homePage = "appvigil.co";
$licence = "";
$description = "";
$contact = "";

$apiProto = "https";
$apiHost = "api.appvigil.co/v1.0/";

$resource = array(
	"ACCESS_TOKEN_NEW" => "access_token/request/",
	"ACCESS_TOKEN_RENEW" => "access_token/renew/",
	"ACCESS_TOKEN_VIEW" => "access_token/view/",
	"ACCESS_TOKEN_FLUSH" => "access_token/flush/",
	"UPLOAD_NEW" => "upload/new/",
	"UPLOAD_LIST" => "upload/list/",
	"UPLOAD_DETAILS" => "upload/details/",
	"SCAN_START" => "scan/start/",
	"SCAN_LIST" => "scan/list/",
	"SCAN_STATUS" => "scan/status/"
	);

$user_agent = "PHP_CLI";
?>
