<?php
class WrapperTest extends PHPUnit_Framework_TestCase
{
	public static $accessToken;
	public static $uploadId;
	public static $scanId;
	function __construct()
	{
		include_once('config/Constants.php');
	}
	function testAccessTokenConstructSuccess()
	{
		include "TestInput.php";
		$a = new AccessToken($apiKey, $apiSecret);
		$this->assertInstanceOf("AccessToken", $a);
		$this->assertInstanceOf("HttpConnection", $a->httpObject);
		$this->assertEquals($apiKey, $a->apiKey);
		$this->assertEquals($apiSecret, $a->apiSecret);
	}
	function testAccessTokenConstructError()
	{
		$a = new AccessToken();
		$this->assertInstanceOf("AccessToken", $a);
		$this->assertNull($a->httpObject);
		$this->assertNull($a->apiKey);
		$this->assertNull($a->apiSecret);
	}
	function testRequestNewAccessTokenSuccess()
	{
		include "TestInput.php";
		$a = new AccessToken($apiKey, $apiSecret);
		$result =  $a->requestNewAccessToken(9999999, '');
		$this->assertNotNull("object", $result);
		$this->assertEquals(SUCCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);
		$this->assertTrue(ctype_alnum($result->response->access_token));
		//saving this access token for upcoming test cases
		self::$accessToken = $result->response->access_token;

	}
	function testRequestNewAccessTokenError()
	{
		//invalid length input
		$a = new AccessToken("", "");
		$result =  $a->requestNewAccessToken("",'');
		$this->assertNotNull("object", $result);
		$this->assertEquals(INSUFFICIENT_PARAMETER, $result->meta->code);
		$this->assertNotNull($result->response->message);

		//invalid length input
		$a = new AccessToken("123456789", "123456789");
		$result =  $a->requestNewAccessToken("",'');
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_API_LEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		//invalid character input
		$a = new AccessToken("12345678$9", "12345678$9");
		$result =  $a->requestNewAccessToken("",'');
		$this->assertNotNull("object", $result);
		$this->assertEquals(SPECIALCHAR_IN_KEY, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testRenewAccessTokenMethodSuccess()
	{
		$a = new AccessToken(self::$accessToken);
		$result =  $a->renewAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(ACCESS_TOKEN_EXTENDED, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testRenewAccessTokenMethodError()
	{
		$a = new AccessToken("12345678900123456789");
		$result =  $a->renewAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$a = new AccessToken("123456789001234567$9");
		$result =  $a->renewAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(SPECIALCHAR_IN_TOKEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$a = new AccessToken("1234567890");
		$result =  $a->renewAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN_LEN, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testViewAccessTokenMethodSuccess()
	{
		//view access token success
		$a = new AccessToken(self::$accessToken);
		$result =  $a->viewAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(SUCCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);
		$this->assertTrue(ctype_alnum($result->response->access_token));
		$this->assertTrue(is_numeric($result->response->ttl_in_seconds));
	}

	function testViewAccessTokenMethodError()
	{
		$a = new AccessToken("1234567890012345679");
		$result =  $a->viewAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN_LEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$a = new AccessToken("123456789001234567$9");
		$result =  $a->viewAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(SPECIALCHAR_IN_TOKEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$a = new AccessToken("1234567890");
		$result =  $a->viewAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN_LEN, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	//upload
	function testUploadConstructSuccess()
	{
		include "TestInput.php";
		$a = new Upload(self::$accessToken);
		$this->assertInstanceOf("Upload", $a);
		$this->assertInstanceOf("HttpConnection", $a->httpObject);
		$this->assertEquals(self::$accessToken, $a->accessToken);
	}
	function testUploadConstructError()
	{
		$a = new Upload("");
		$this->assertInstanceOf("Upload", $a);
		$this->assertNull($a->httpObject);
		$this->assertNull($a->accessToken);
	}

	function testNewUploadSuccess()
	{
		include "TestInput.php";
		$a = new Upload(self::$accessToken);
		$result =  $a->newUpload($fileLoc, 'phpwrappertestapp', true);
		$this->assertNotNull("object", $result);
		$this->assertEquals(UPLOAD_SUCCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);
		$this->assertTrue(ctype_alnum($result->response->upload_id));
		//passing this uploadid for scan testing
		self::$uploadId = $result->response->upload_id;

		$result =  $a->newUpload($fileLoc, 'phpwrappertestapp', false);
		$this->assertNotNull("object", $result);
		$this->assertEquals(DIGEST_SUCCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);
		$this->assertTrue(ctype_alnum($result->response->upload_id));
	}

	function testNewUploadError()
	{
		include "TestInput.php";
		$a = new Upload(self::$accessToken);
		$result =  $a->newUpload($EfileLoc, 'phpwrappertestapp', true);
		$this->assertEquals(0, $result);

		$result =  $a->newUpload($inv_fileLoc, 'phpwrappertestapp', true);
		$this->assertEquals(CORRUPTED_APP, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testUploadListSuccess()
	{
		$a = new Upload(self::$accessToken);
		$result =  $a->uploadList("", "false");
		$this->assertNotNull("object", $result);
		$this->assertEquals(SUCCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testUploadListError()
	{
		$a = new Upload("12345678901234567890");
		$result =  $a->uploadList("", "false");
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testUploadDetailsSuccess()
	{
		$a = new Upload(self::$accessToken);
		$result =  $a->uploadDetails(self::$uploadId);
		$this->assertNotNull("object", $result);
		$this->assertEquals(SUCCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testUploadDetailsError()
	{
		$a = new Upload(self::$accessToken);
		//666666 for dummy uploadid to check error
		$result =  $a->uploadDetails('666666');
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_UPLOAD_REF_LEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$result =  $a->uploadDetails('b$99b08c270b6165369dd857e8d20c0d7b31e9f8');
		$this->assertNotNull("object", $result);
		$this->assertEquals(SPECIALCHAR_IN_UPLOAD_REF, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	//scan test
	function testScanConstructSuccess()
	{
		include "TestInput.php";
		$a = new Scan(self::$accessToken);
		$this->assertInstanceOf("Scan", $a);
		$this->assertInstanceOf("HttpConnection", $a->httpObject);
		$this->assertEquals(self::$accessToken, $a->accessToken);
	}
	function testScanConstructError()
	{
		$a = new Scan("");
		$this->assertInstanceOf("Scan", $a);
		$this->assertNull($a->httpObject);
		$this->assertNull($a->accessToken);
	}

	function testScanStartSuccess()
	{
		$a = new Scan(self::$accessToken);
		$result =  $a->startScan(self::$uploadId, null);
		$this->assertNotNull("object", $result);
		$this->assertEquals(SCAN_STARTED, $result->meta->code);
		$this->assertNotNull($result->response->message);
		$this->assertTrue(ctype_alnum($result->response->scan_id));
		//passing this scanid for scan status
		self::$scanId = $result->response->scan_id;
	}

	function testScanStartError()
	{
		$a = new Scan(self::$accessToken);
		$result =  $a->startScan(self::$uploadId, null);
		$this->assertNotNull("object", $result);
		$this->assertEquals(UPLOAD_ID_IN_PROCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);

		//666666 for dummy uploadid to check error
		$result =  $a->startScan(666666, null);
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_UPLOAD_REF_LEN, $result->meta->code);
	}

	function testScanListSuccess()
	{
		$a = new Scan(self::$accessToken);
		$result =  $a->listScan("*");
		$this->assertNotNull("object", $result);
		$this->assertEquals(SUCCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testScanListError()
	{
		$a = new Scan("12345678901234567890");
		$result =  $a->listScan("*");
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$a = new Scan(self::$accessToken);
		$result =  $a->listScan(8);
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_SCAN_STATUS, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testScanStatusSuccess()
	{
		$a = new Scan(self::$accessToken);
		$result =  $a->scanStatus(self::$scanId);
		$this->assertNotNull("object", $result);
		$this->assertEquals(SUCCESS, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testScanStatusError()
	{
		$a = new Scan("12345678909876543212");
		$result =  $a->scanStatus(self::$scanId);
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$a = new Scan(self::$accessToken);
		//7777777 for dummy scanid to check error
		$result =  $a->scanStatus('7777777');
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_SCAN_ID, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$result =  $a->scanStatus('2$668fb5002164555e54c7d85b112b25b36672f9e59049545864b64b234e');
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_SCAN_ID, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	//flush accesstoken
	function testFlushAccessTokenMethodSuccess()
	{
		$a = new AccessToken(self::$accessToken);
		$result =  $a->flushAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(ACCESS_TOKEN_FLUSHED, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}

	function testFlushAccessTokenMethodError()
	{
		$a = new AccessToken("1234567890012345679");
		$result =  $a->flushAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN_LEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$a = new AccessToken("123456789001234567$9");
		$result =  $a->flushAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(SPECIALCHAR_IN_TOKEN, $result->meta->code);
		$this->assertNotNull($result->response->message);

		$a = new AccessToken("12345678900");
		$result =  $a->flushAccessToken();
		$this->assertNotNull("object", $result);
		$this->assertEquals(INVALID_TOKEN_LEN, $result->meta->code);
		$this->assertNotNull($result->response->message);
	}
}
?>