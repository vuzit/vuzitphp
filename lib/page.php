<?php
/*
  Class for loading page text. To use this class you need to sign up 
  for Vuzit first: http://vuzit.com/signup
*/
class Vuzit_Page extends Vuzit_Base
{
  /*
    Constructor.  Creates an empty page object.  This is not called directly.  
    Use findAll to load an instance. 
  */
  public function __construct() {
    $this->pageNumber = -1;
    $this->pageText = null;
  }

  /*
    Returns the page number of the page. 
  */
  public function getNumber() {
    return $this->pageNumber;
  }

  /*
    Returns the page text.  
  */
  public function getText() {
    return $this->pageText;
  }

  /*
    Loads an array of pages.  It throws a <Vuzit_ClientException> on failure.  
  */
  public static function findAll($webId, $options = null)
  {
    if(!$webId) {
      throw new Vuzit_ClientException("webId cannot be null");
    }

    $params = self::postParameters("index", $options, $webId);

    $ch = self::curlRequest();
    $url = self::parametersToUrl("documents/$webId/pages.xml", $params);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // only if expecting response

    $xml_string = curl_exec($ch);
    $info = curl_getinfo($ch);

    if(!$xml_string) {
      throw new Vuzit_ClientException('CURL load failed: "' . curl_error($ch) . '"');
    }

    // Prevent the warnings if the XML is malformed
    $xml = @simplexml_load_string($xml_string); 
    curl_close($ch);

    if(!$xml) {
      throw new Vuzit_ClientException("Error loading XML response");
    }
    if($xml->code) {
      throw new Vuzit_ClientException($xml->msg, (int)$xml->code);
    }
    if(!$xml->page) {
      throw new Vuzit_ClientException("Unknown error occurred");
    }

    $result = array();

    foreach($xml->page as $node)
    {
      $page = new Vuzit_Page();
      $page->pageNumber = self::nodeValueInt($node->number); 
      $page->pageText = self::nodeValue($node->text); 

      $result[] = $page;
    }

    return $result;
  }
}
?>