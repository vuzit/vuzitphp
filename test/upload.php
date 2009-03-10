<?php
require_once 'test_include.php';

$doc = Vuzit_Document::upload("c:/temp/test.pdf", true);

$timestamp = time();
$sig = Vuzit_Service::getSignature("show", $doc->getId(), $timestamp);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <title>Vuzit Upload Example</title>
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
    Loading document <b><?php echo $doc->getID(); ?></b>
    <a href="destroy.php?delete_id=<?php echo $doc->getID(); ?>">Destroy it</a>
    <div id="vuzit_viewer" style="width: 650px; height: 500px;"></div>
  </body>
</html>
