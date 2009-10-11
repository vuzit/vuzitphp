<?php
require_once '../lib/vuzit.php';

$exception = null;

$type = array_key_exists("type", $_GET) ? $_GET["type"] : null;

switch($type)
{
case "bad_api_key":
  try {
    Vuzit_Service::$PublicKey = 'does not exist';
    $doc = Vuzit_Document::findById($show_id);
  }
  catch(Vuzit_ClientException $ex) {
    $exception = $ex;
  }
  break;
case "doc_does_not_exist":
   try {
    $doc = Vuzit_Document::findById("doesnotexistblah");
  }
  catch(Vuzit_ClientException $ex) {
    $exception = $ex;
  }
  break;
case "invalid_signature":
   try {
    Vuzit_Service::$PrivateKey = 'not_a_valid_key';
    $doc = Vuzit_Document::findById($show_id);
  }
  catch(Vuzit_ClientException $ex) {
    $exception = $ex;
  }
  break;
}

?>

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <title>Error Tests</title>
  </head>
  <body onload="">
    <h2>
      Error tests
    </h2>
    <?php
      if($exception) {
        echo "Type: " . $_GET["type"] . "<br/>";
        echo "Code: " . $exception->getCode() . "<br/>";
        echo "Message: " . $exception->getMessage() . "<br/>";
      }
    ?>
    <ul>
      <li><a href="errors.php?type=bad_api_key">Bad API key</a></li>
      <li><a href="errors.php?type=doc_does_not_exist">Document does not exist</a></li>
      <li><a href="errors.php?type=invalid_signature">Invalid signature</a></li>
    </ul>
  </body>
</html>
