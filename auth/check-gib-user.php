<?php
  echo '<h1>get-gib-user-list</h1>';

if (!ini_get('display_errors')) ini_set('display_errors', 1);
if (!ini_get('display_startup_errors')) ini_set('display_startup_errors', 'on');
// Tüm hataları görelim
error_reporting(E_ALL);

ini_set("soap.wsdl_cache_enabled", "0");

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


          $session = '370718bc-b30b-4824-81ff-9051dba07cd2';

          $trace = true;
          $exceptions = false;


          $xml_array -> REQUEST_HEADER -> SESSION_ID  = $session;
            $xml_array -> REQUEST_HEADER -> APPLICATION_NAME  = 'yourAppName';
        $xml_array -> USER -> IDENTIFIER = '5555552511';
        //Mükellefiyeti sorgulanacak firmanın vergi kimlik numarası
        $xml_array -> DOCUMENT_TYPE = 'INVOICE';
        //Mükelleffiyet kontrol edilecek ürün tipi. E-Fatura için INVOICE, E-İrsaliye için DESPATCHADVICE gönderilmelidir.


      try
      {
        $client = new SoapClient($authentication, array('trace' => $trace, 'exceptions' => $exceptions));
        //echo "Client oluşturuldu";
        $response = $client->CheckUser($xml_array);
        
        if ($response->USER == NULL) {
          echo "kayıt yok";
        } else {
          foreach ($response->USER as $user) {
            echo "IDENTIFIER :: ". $user->IDENTIFIER ."<br>";
            echo "ALIAS :: ". $user->ALIAS ."<br>";
            echo "TITLE :: ". $user->TITLE ."<br>";
            echo "TYPE :: ". $user->TYPE ."<br>";
            echo "REGISTER_TIME :: ". $user->REGISTER_TIME ."<br>";
            echo "UNIT :: ". $user->UNIT ."<br>";
            echo "ALIAS_CREATION_TIME :: ". $user->ALIAS_CREATION_TIME ."<br>";
            echo "ACCOUNT_TYPE :: ". $user->ACCOUNT_TYPE ."<br>";
            echo "DELETED :: ". $user->DELETED ."<br>";
            echo "ALIAS_DELETION_TIME :: ". $user->ALIAS_DELETION_TIME ."<br>";
            echo "DOCUMENT_TYPE :: ". $user->DOCUMENT_TYPE ."<br>";
            echo " :..: <br>";
          }
        }

      }
      catch (SoapFault $e)
      {
        echo "Error!";
        echo $e -> getMessage ();
        echo 'Last response: '. $client->__getLastResponse();
      }

?>
