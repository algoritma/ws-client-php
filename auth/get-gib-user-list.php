<?php
	echo '<h1>get-gib-user-list</h1>';

if (!ini_get('display_errors')) ini_set('display_errors', 1);
if (!ini_get('display_startup_errors')) ini_set('display_startup_errors', 'on');
// Tüm hataları görelim
error_reporting(E_ALL);

    include '../servisLinkleri.php';

    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }


        	$session = 'ebc77bae-96f1-42b4-9f8e-0e0aaf012159';

      		$trace = true;
      		$exceptions = false;

      		$xml_array -> REQUEST_HEADER -> SESSION_ID  = $session;
            $xml_array -> REQUEST_HEADER -> APPLICATION_NAME  = 'yourAppName';
  			$xml_array -> TYPE = 'XML';
  			//Listenin dönüleceği dosya tipi. XML ve CSV olabilir. Varsayılan XMLdir.
  			$xml_array -> DOCUMENT_TYPE = 'INVOICE';
  			//Mükellef listesi çekilmek istenilen ürün tipi. E-Fatura için INVOICE, E-İrsaliye için DESPATCHADVICE gönderilmelidir. Her iki ürüne ait etiketleri çekmek için ALL değeri gönderilebilir. Parametre gönderilmez bütün ürünlere ait Aktif etiket listesi dönülecektir.
  			$xml_array -> ALIAS_TYPE = 'ALL';
  			//Mükelleflerin etiketlerine göre çekmek için kullanılır. Sadece Gönderici Birim etiketini çekmek için GB, Posta Kutusu etiketini çekmek için PK gönderilmelidir. Bütün etiketleri çekmek için ALL değeri gönderilebilir. Varsayılan PKdir. Parametre gönderilmez ise sadece PK etiket listesi dönülecektir. Belge göndermek için sadece PK etiketlerine ihtiyaç bulunmaktadır.
  			$xml_array -> REGISTER_TIME_START = '';
  			//Mükellefiyet başlangıç tarihi. Belirli bir tarihten sonra e-fatura veya e-irsaliye mükellefi sisteme dahil olmuş mükellefleri çekmek için kullanılabilir. Eğer tarih içerisinde saat bilgisi gönderilirse sonuç dönülürken dikkate alınacaktır. format: YYYY-AA-GG veya YYYY-AA-GGTSS:DD:SS formatında 2013-01-01, 2013-01-01T01:01:01
  			$xml_array -> ALIAS_MODIFY_DATE = '';
  			//Etiket durum değişiklik tarihi. Belirli bir tarihten sonra sisteme eklenen veya silinen etiketleri çekmek için kullanılabilir. Bu parametre gönderilirse gönderilen tarihten sonra sisteme eklenen veya silinen etiketler dönülecektir. Eğer tarih içerisinde saat bilgisi gönderilirse sonuç dönülürken dikkate alınacaktır. format: YYYY-AA-GG veya YYYY-AA-GGTSS:DD:SS formatında 2013-01-01, 2013-01-01T01:01:01

			try
			{
				$client = new SoapClient($authentication, array('trace' => $trace, 'exceptions' => $exceptions));
				//echo "Client oluşturuldu";
				$response = $client->GetGibUserList($xml_array);
				$res = $response->CONTENT->_;
				//Parametre-CONTENT	Tip-Base64Encoded	Açıklama-Kriterlere uygun mükelleflere ait GB ve PK adresleri
			}
			catch (SoapFault $e)
			{
				echo "Error!";
				echo $e -> getMessage ();
				echo 'Last response: '. $client->__getLastResponse();
			}

			//dosyayı kaydet
		    touch('GibUserList.zip');
		    $dosya = fopen('GibUserList.zip', 'w');
		    fwrite($dosya, $res);
		    fclose($dosya);

$zip = new ZipArchive;
//$zip->open("php://filter/read=convert.base64-decode/resource={$res}");
$zip->open("GibUserList.zip");
$users = $zip->getFromName('users');
$xml = simplexml_load_string($users);
$json = json_encode($xml);
$user_array = json_decode($json,TRUE);
//print_r($user_array[USER][0]);
//echo "<br>";


$_arr = $user_array[USER][0];
$resultStrings = array();
foreach ($_arr as $key => $values) {
	$resultStrings[] = $key;
}
$sql_str = "(`". strtolower(implode($resultStrings, "`, `")) ."`)";
//echo $sql_str ."<br>";

//INSERT INTO table (id, name, age) VALUES(1, "A", 19) ON DUPLICATE KEY UPDATE  name="A", age=19

$_arr = $user_array[USER];
$resultStrings = array();
$resultStrings = array();
foreach ($_arr as $i => $user) {
	if ($i==1000) break; // test için limit
echo "\n". $i ."\n";
	$sql_str_x = $sql_str ."('". implode("', '", $user) ."');";
echo $sql_str_x ."\n";
}

?>
