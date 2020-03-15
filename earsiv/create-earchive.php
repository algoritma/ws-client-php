<?php
/*
webservis oturumunuzun aktif olduğundan emin olun.
oturum sona ermiş ise önce auth/login.php ile giriş yapın.
*/

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

if (!ini_get('display_errors')) ini_set('display_errors', 1);
if (!ini_get('display_startup_errors')) ini_set('display_startup_errors', 'on');
// Tüm hataları görelim
error_reporting(E_ALL);

    include '../servisLinkleri.php';

	ini_set("soap.wsdl_cache_enabled", "0"); // wsdl cache 'ini devre disi birak

	$Username = 'usertest';
	$Password = 'test123';

	$client = new SoapClient($authentication);
try {
	$Req["USER_NAME"] =	$Username;
	$Req["PASSWORD"] = $Password;
	$RequestHeader["SESSION_ID"] = "-1";
	$Req["REQUEST_HEADER"] = $RequestHeader;
	$Res = $client->Login($Req);
	$archiveClient = new SoapClient($eArsiv);

	//$content = file_get_contents('earsivxml.php');
	require_once 'earsivxml.php';

	$Request = array(
			"REQUEST_HEADER"	=>	array(
			"SESSION_ID"		=>	$Res->SESSION_ID,
			"COMPRESSED"		=>	"N"
		),
		"ArchiveInvoiceExtendedContent"	=>	array(
			"INVOICE_PROPERTIES"=>	array(
			"EARSIV_FLAG"		=>	"Y",
			"EARSIV_PROPERTIES"	=>	array(
				"EARSIV_TYPE"		=>	"NORMAL",
				"EARSIV_EMAIL_FLAG"	=>	"Y",
				"EARSIV_EMAIL"		=>	"info@izibiz.com.tr",
				"SUB_STATUS"		=>	"NEW"
			),
			"INVOICE_CONTENT"	=>	$content
			)
		)
	);

	print_r($Request);
	echo "<br>------------------------------------------<br>";
	$response = $archiveClient->WriteToArchiveExtended($Request);
		$res_transaction_ID = $response->ERROR_TYPE->INTL_TXN_ID;
		$res_err_code 		= $response->ERROR_TYPE->ERROR_CODE;
		$res_err_desc 		= $response->ERROR_TYPE->ERROR_SHORT_DES;

	if ($res_err_code == -1) {
		echo "işlem BAŞARISIZ<br>";
	} else {
		echo "OK<br>";
	}
	echo $res_transaction_ID ."<br>";
	echo $res_err_code ."<br>";
	echo $res_err_desc ."<br>";	
	echo "<br>------------------------------------------<br>";


	print_r($response);
} catch (Exception $exc) { // Hata olusursa yakala
  // Son istegi ekrana bas
  echo "Son yapilan istek asagidadir<br/><pre>";
  echo htmlentities($client->__getLastRequest());
  echo "</pre>";

  echo "<br/><br/><br/>";

  // Son istegin header kismini ekrana bas
  echo "Son yapilan istegin header kismi<br/><pre>";
  echo htmlentities($client->__getLastRequestHeaders());
  echo "</pre>";

  echo "<br/><br/><br/>";

  // Son yapilan istege sunucunun verdigi yanit
  echo "Son yapilan metod cagrisinin yaniti<br/><pre>";
  echo htmlentities($client->__getLastResponse());
  echo "</pre>";

  echo "<br/><br/><br/>";

  // Son yapilan istege sunucunun verdigi yanitin header kismi
  echo "Son yapilan metod cagrisinin yanitinin header kismi<br/><pre>";
  echo htmlentities($client->__getLastResponseHeaders());
  echo "</pre>";

  echo "Soap Hatasi Olustu: " . $exc->getMessage()."<br>";
  //print_r($exc);
}

?>
