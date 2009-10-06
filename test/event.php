<?php
require_once 'test_include.php';

//$options = array("e" => "page_view");
$options = array("v" => "demo_test");

$list = Vuzit_Event::findAll("1fm6", $options);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Vuzit Event Example</title>
    <link href="http://vuzit.com/stylesheets/Vuzit-2.8.css" rel="Stylesheet" type="text/css" />
    <script src="http://vuzit.com/javascripts/Vuzit-2.8.js" type="text/javascript"></script>
    
  </head>
  <body onload="">
    <p>
      Total events: <?php echo count($list); ?>
    </p>
    <?
      $event = count($list);
      for($i = 0; $i < count($list); $i++)
      { 
        $item = $list[$i];
        $event--;
    ?>
    <h3>
      Event <?php echo ($event + 1) ?>
    </h3>
    <ul>
      <li>
        Document <?php echo $item->getWebId(); ?> on 
        <a href="<?php echo $item->getReferer(); ?>"><?php echo $item->getReferer(); ?></a></li>
      <li>
        <?php echo $item->getEvent(); ?> on page <?php echo $item->getPage(); ?> at 
        <?php echo date("Y-d-m H:i:s", $item->getRequestedAt()); ?> 
        (value: <?php echo $item->getValue(); ?>)
      </li>
      <li>Remote host: <a href="location.php?ip=<?php echo $item->getRemoteHost(); ?>">
                        <?php echo $item->getRemoteHost();  ?></a></li>
      <li>User Agent: <?php echo $item->getUserAgent(); ?></li>
    </ul>
    <?php } ?>
    
  </body>
</html>
