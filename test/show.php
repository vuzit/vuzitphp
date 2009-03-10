<?php
require_once 'test_include.php';

$doc = Vuzit_Document::findById($show_id);

$timestamp = time();
$sig = Vuzit_Service::getSignature("show", $doc->getId(), $timestamp);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Vuzit Show Example</title>
    <link href="http://vuzit.com/stylesheets/Vuzit-2.6.css" rel="Stylesheet" type="text/css" />
    <script src="http://vuzit.com/javascripts/Vuzit-2.6.js" type="text/javascript"></script>
    
    <script type="text/javascript">
      // Called when the page is loaded.  
      function initialize()  {
        vuzit.Base.apiKeySet("<?php echo Vuzit_Service::$PublicKey; ?>"); 
        var options = {signature: '<?php echo rawurlencode($sig); ?>', 
                       timestamp: '<?php echo $timestamp ?>', ssl: true}
        var viewer = vuzit.Viewer.fromId("<?php echo $doc->getId(); ?>", options);
        
        viewer.display(document.getElementById("vuzit_viewer"), { zoom: 1 });
      }
    </script>
    
  </head>
  <body onload="initialize()">
    <h2>
      Document - <?php echo $doc->getID(); ?>
    </h2>
    <ul>
      <li>Title: <?php echo $doc->getTitle(); ?></li>
      <li>Subject: <?php echo $doc->getSubject(); ?></li>
      <li>Page count: <?php echo $doc->getPageCount(); ?></li>
      <li>Width: <?php echo $doc->getPageWidth(); ?></li>
      <li>Height: <?php echo $doc->getPageHeight(); ?></li>
      <li>File size: <?php echo $doc->getFileSize(); ?></li>
    </ul>
    
    <div id="vuzit_viewer" style="width: 650px; height: 500px;"></div>
  </body>
</html>
