<?php
require_once 'test_include.php';

if(isset($_GET["ip"])) {
  try
  {
    $location = GeoLocation::find($_GET["ip"]);
  }
  catch(Exception $ex)
  {
    $message = "Failed with message: " . $ex->getMessage();
  }
}
?>

<html>
  <head>
    <title>Geo location</title>
  </head>
  <body onload="">
    <h2>IP address results:</h2>
    <?php
      echo "Country: " . $location->getCountryName();
      echo "<br/>";
      echo "Region: " . $location->getRegionName();
      echo "<br/>";
      echo "City: " . $location->getCity();
      echo "<br/>";
    ?>
  </body>
</html>

