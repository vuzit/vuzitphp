<?php
require_once '../lib/vuzit.php';
require_once 'geo_location.php';
require_once 'browser_info.php';

// GENERAL FUNCTIONS

// Returns the domain for use with the Javascript API global variables.  
function domain()
{
  $url = Vuzit_Service::getServiceUrl();
  return substr($url, 7, strlen($url) - 7);
}

// Returns the parameter from the _GET array or null if it isn't present.
function get($key)
{
  return array_key_exists($key, $_GET) ? $_GET[$key] : null;
}

// Exports a set of CSV fields into a line of CSV text.  The function
// mirrors the PHP fputcsv function.  
function csv_line($fields, $delimeter = ',', $enclosure = '"')
{
  $result = '';

  for($i = 0; $i < count($fields); $i++)
  {
    if($i != 0) {
      $result .= $delimeter;
    }
    $result .= ($enclosure . str_replace('"', '""', $fields[$i]) . $enclosure);
  }

  return $result . "\r\n";
}

// Load the header
function header_load($doc = null)
{
  $id = '';
  $onload = '';
  if($doc != null) 
  {
    $timestamp = time();
    $id = $doc->getId();
    $sig = Vuzit_Service::signature("show", $doc->getId(), $timestamp, get("p"));
    $onload = "initialize()";
  }
?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
      <title>Vuzit <?php echo get("c") ?> Command Example</title>
      <link href="<?php echo Vuzit_Service::getServiceUrl(); ?>/stylesheets/Vuzit-2.9.css" 
            rel="Stylesheet" type="text/css" />
      <script src="<?php echo Vuzit_Service::getServiceUrl(); ?>/javascripts/Vuzit-2.9.js" 
              type="text/javascript"></script>
      <script type="text/javascript">
        // Called when the page is loaded.  
        function initialize()  {
          vuzit.Base.apiKeySet("<?php echo Vuzit_Service::getPublicKey(); ?>"); 
          vuzit.Base.webServerSet({ host: '<?php echo domain(); ?>', port: '80' });
          vuzit.Base.imageServerSet({ host: '<?php echo domain(); ?>', port: '80' });

          var options = { signature: '<?php echo rawurlencode($sig); ?>', 
                          <?php if(get("p") != null) { ?>
                          includedPages: '<?php echo get("p"); ?>', 
                          <?php } ?>
                          timestamp: '<?php echo $timestamp ?>'}
          var viewer = vuzit.Viewer.fromId("<?php echo $id; ?>", options);
          
          viewer.display(document.getElementById("vuzit_viewer"), { zoom: 1 });
        }
      </script>
    </head>

    <body onload="<?php echo $onload; ?>"> 

    <h2>Command: <?php echo get("c"); ?></h2>
<?php
}

// Prints the footer
function footer_load()
{
?>
    </body>
  </html>
<?php
}

// Prints out an array for things like options.  
function printArray($list)
{
  if(count($list) < 1) {
    return '';
  }

  $result = 'Array:';
  $result .= '<ul>';
  foreach ($list as $key => &$value) {
    $result .= '<li>' . $key . ' = ' . $value . '</li>';
  }
  $result .= '</ul>';

  return $result;
}

// COMMAND FUNCTIONS

// Runs the load command
function load_command()
{
  $options = array();
  if(get("p") != null) {
    $options["included_pages"] = get("p");
  }

  $doc = Vuzit_Document::findById(get("id"), $options);
  header_load($doc);
  $pdf_url = Vuzit_Document::downloadUrl($doc->getId(), "pdf");

  // TODO: Grab the file type and use it to determine the other file 
  //       to download?  
  ?>
    <h3>
      Document - <?php echo $doc->getID(); ?>
    </h3>
    <?php echo printArray($options); ?>
    <p>Results:</p>
    <ul>
      <li>Title: <?php echo $doc->getTitle(); ?></li>
      <li>Subject: <?php echo $doc->getSubject(); ?></li>
      <li>Page count: <?php echo $doc->getPageCount(); ?></li>
      <li>Width: <?php echo $doc->getPageWidth(); ?></li>
      <li>Height: <?php echo $doc->getPageHeight(); ?></li>
      <li>File size: <?php echo $doc->getFileSize(); ?></li>
      <li>Status: <?php echo $doc->getStatus(); ?></li>
      <li><a href="<?php echo $pdf_url; ?>">PDF Download</a></li>
    </ul>

    <div id="vuzit_viewer" style="width: 650px; height: 500px;"></div>
  <?php
  footer_load();
}

// Runs the delete command
function delete_command()
{
  header_load();
  try
  {
    $id = get("id");
    Vuzit_Document::destroy($id);
    echo "Delete succeeded: " . $id;
  }
  catch(Vuzit_ClientException $ex)
  {
    echo "Delete of " . $id . " failed with code [" . $ex->getCode() . 
         "], message: " . $ex->getMessage();
  }
  footer_load();
}

// Runs the upload command
function upload_command()
{
  $options = array();
  if(get("p") != null) {
    $options["download_pdf"] = get("p");
  }
  if(get("d") != null) {
    $options["download_document"] = get("d");
  }
  if(get("s") != null) {
    $options["secure"] = get("s");
  }

  $doc = Vuzit_Document::upload(get("path"), $options);
  header_load($doc);
  ?>
    <h3>
      Document - <?php echo $doc->getID(); ?>
    </h3>
    <?php echo printArray($options); ?>

    <div id="vuzit_viewer" style="width: 650px; height: 500px;"></div>
  <?php
  footer_load();
}

// Runs the event command
function event_command()
{
  $options = array();

  if(get("cu") != null) {
    $options["custom"] = get("cu");
  }
  if(get("e") != null) {
    $options["event"] = get("e");
  }
  if(get("l") != null) {
    $options["limit"] = get("l");
  }

  $list = Vuzit_Event::findAll(get("id"), $options);

  switch(get("o"))
  {
    case "csv":
      event_load_csv($list);
      break;
    default:
      header_load();
      event_load_html($list, $options);
      footer_load();
      break;
  }
}

// Loads the output of the event list as a CSV file.  
function event_load_csv($list, $fileName = "events.csv")
{
  $csv = '';

  $csv .= csv_line(array("web_id", "requested_at", "duration", "event", 
                         "page", "custom", "remote_host", "referer", 
                         "user_agent"));
  foreach($list as $event)
  {
    $browser = new BrowserInfo($event->getUserAgent());
    $fields = array($event->getWebId(),
                    date("m/d/y H:i", $event->getRequestedAt()),
                    $event->getDuration(),
                    $event->getEvent(),
                    $event->getPage(),
                    $event->getCustom(),
                    $event->getRemoteHost(),
                    $event->getReferer(),
                    $browser->getName() . " " . $browser->getVersion()
                   );
    $csv .= csv_line($fields);
  }
  
  header('Content-type: text/csv');
  header("Content-Disposition: attachment; filename=\"" . $fileName . "\";"); 
  echo $csv;
}

// Loads the output of the event list as HTML.  
function event_load_html($list, $options)
{
  ?>
  <h3>
    Document <?php echo $list[0]->getWebId(); ?>
  </h3>
  <p>
    Total events: <?php echo count($list); ?>
  </p>
  <?php echo printArray($options); ?>
  <p>Results:</p>
  <ol>
  <?
  $event = count($list);
  for($i = 0; $i < count($list); $i++)
  { 
    $item = $list[$i];
    $event--;
    $host = $item->getRemoteHost();
    $browser = new BrowserInfo($item->getUserAgent());
    ?>
      <li>
        <?php 
          echo "[" . date("Y-m-d H:i:s", $item->getRequestedAt()) . "] ";

          if($item->getEvent() == "page_view") {
            echo $item->getDuration() . "s - ";
          }
          echo $item->getEvent();

          if($item->getPage() != 0) {
            echo ", p".  $item->getPage(); 
          }
          if($item->getCustom() != null) {
            echo " (" . $item->getCustom() .  ")";
          }
          echo ' - <a href="' . $item->getReferer() . '">URL</a> - ';
          echo ' <a href="location.php?ip=' . $host . '">' . $host . "</a> - ";
          echo $browser->getName() . " " . $browser->getVersion();
          ?>
      </li>
    <?php
  } 
  echo "</ol>";
}

// MAIN EXECUTION

// Load and check the keys
$public_key = get("puk");
$private_key = get("prk");

if($public_key == null || $private_key == null)
{
  echo "Please provide the public (puk) and private (prk) keys";
  return;
}

$service_url = get("su");

if($service_url != null) {
  Vuzit_Service::setServiceUrl($service_url);
}

Vuzit_Service::setPublicKey($public_key);
Vuzit_Service::setPrivateKey($private_key);
Vuzit_Service::setUserAgent("Vuzit Test Suite");

// Grab the command and execute
switch(get("c"))
{
  case "load":
    load_command();
    break;
  case "upload":
    upload_command();
    break;
  case "delete":
    delete_command();
    break;
  case "event":
    event_command();
    break;
}
?>
