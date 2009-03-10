<?php
require_once 'test_include.php';

if(isset($_GET["delete_id"])) {
  $message = "Document specified: " . $_GET["delete_id"];
  try {
    $result = Vuzit_Document::destroy($_GET["delete_id"]);
  }
  catch(Vuzit_Exception $ex) {
    $result = false;
    $message = "Delete failed with code: " . $ex->getCode() . ", message: " . $ex->getMessage();
  }
}
else {
  $message = null;
}
?>

<html>
  <head>
    <title>Delete test</title>
  </head>
  <body onload="">
    <h2>
      <?php
        if($message == null) {
          echo "No document specified";
        }
        else {
          echo $message . "<br/>";
          echo "Success?: " . (($result == true) ? 'yes' : 'no');
        }
      ?>
    </h2>
  </body>
</html>

