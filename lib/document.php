<?php
/*
  Class for uploading, loading, and deleting documents using the Vuzit Web
  Service API: http://vuzit.com/developer/documents_api.  
*/
class Vuzit_Document extends Vuzit_Base
{
  /*
    Constructor.  Creates an empty document.  This is not called directly.  
    Use upload or findById to load an instance. 
  */
  public function __construct() {
    $this->id = null;
    $this->title = null;
    $this->subject = null;
    $this->modifiedAt = null;
    $this->imageType = null;
    $this->pageCount = -1;
    $this->pageWidth = -1;
    $this->pageHeight = -1;
    $this->fileSize = -1;
  }

  /*
    Returns the document web ID.  
  */
  public function getId() {
    return $this->id;
  }

  /*
    Returns the document title.  
  */
  public function getTitle() {
    return $this->title;
  }

  /*
    Returns the document subject.  
  */
  public function getSubject() {
    return $this->subject;
  }

  /*
    Returns the document page count.  

  Returns:
    int
  */
  public function getPageCount() {
    return $this->pageCount;
  }

  /*
    Returns the document page width.  
  */
  public function getPageWidth() {
    return $this->pageWidth;
  }

  /*
    Returns the document page height.  
  */
  public function getPageHeight() {
    return $this->pageHeight;
  }

  /*
    Returns the document file size.  
  */
  public function getFileSize() {
    return $this->fileSize;
  }

  /*
    Deletes a document by the ID.  It throws a <Vuzit_ClientException> on failure. 
  */
  public static function destroy($webId)
  {
    $method = "destroy";
    $params = array();

    $post_params = self::postParams($method, $params, $webId);

    $url = self::paramsToUrl('documents', $post_params, $webId);
    $ch = self::curlRequest();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // only if expecting response
    curl_setopt($ch, CURLOPT_URL, $url);

    $xml_string = curl_exec($ch);

    $info = curl_getinfo($ch);
    if($info['http_code'] != 200) {
      $xml = @simplexml_load_string($xml_string); 
      throw new Vuzit_ClientException((string)$xml->msg, (int)$xml->code);
    }
    curl_close($ch);
  }

  /*
    Finds a document by the ID.  It throws a <Vuzit_ClientException> on failure. 
  */
  public static function findById($webId)
  {
    $method = "show";
    $params = array();

    $post_params = self::postParams($method, $params, $webId);

    $ch = self::curlRequest();
    $url = self::paramsToUrl('documents', $post_params, $webId);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // only if expecting response

    $xml_string = curl_exec($ch);
    $info = curl_getinfo($ch);

    if(!$xml_string) {
      throw new Vuzit_ClientException('CURL load failed: "' . curl_error($ch) . '"');
    }
    // TODO: This needs to be re-added some time in the future by looking at the
    //       error codes.  I would add it but they aren't documented.  
    //if($info['http_code'] != 200) {
    //  throw new Vuzit_ClientException("HTTP error " . $info['http_code']);
    //}

    // Prevent the warnings if the XML is malformed
    $xml = @simplexml_load_string($xml_string); 
    curl_close($ch);

    if(!$xml) {
      throw new Vuzit_ClientException("Error loading XML response");
    }
    if($xml->code) {
      throw new Vuzit_ClientException($xml->msg, (int)$xml->code);
    }
    if(!$xml->web_id) {
      throw new Vuzit_ClientException("Unknown error occurred");
    }

    $result = new Vuzit_Document();
    $result->id = (string)$xml->web_id; 

    if($xml->title) {
      $result->title = (string)$xml->title;
      $result->subject = (string)$xml->subject;
      $result->pageCount = (int)$xml->page_count;
      $result->pageWidth = (int)$xml->width;
      $result->pageHeight = (int)$xml->height;
      $result->fileSize = (int)$xml->file_size;
    }

    return $result;
  }

  /*
     Uploads a file to Vuzit. It throws a <Vuzit_ClientException> on failure. 
  */
  public static function upload($file, $secure = true, $fileType = null)
  {
    $method = "create";

    if(!file_exists($file)) {
      throw new Vuzit_ClientException("Cannot find file at path: $file");
    }

    if($fileType != null) {
      $params['file_type'] = $fileType;
    }
    $params['secure'] = ($secure) ? '1' : '0';
    $params['upload'] = "@".$file;

    $post_params = self::postParams($method, $params);

    $ch = self::curlRequest();
    $url = Vuzit_Service::getServiceUrl() . "/documents.xml";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // only if expecting response
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);

    $xml_string = curl_exec($ch);
    if(!$xml_string) {
      throw new Vuzit_ClientException('CURL load failed: "' . curl_error($ch) . '"');
    }

    $info = curl_getinfo($ch);
    // TODO: This needs to be re-added some time in the future by looking at the
    //       error codes.  I would add it but they aren't documented.  
    //if($info['http_code'] != 201) {
    //  throw new Vuzit_ClientException("HTTP error, expected 201 but got: " . $info['http_code']);
    //}

    // Prevent the warnings if the XML is malformed
    $xml = @simplexml_load_string($xml_string); 
    curl_close($ch);

    if(!$xml) {
      throw new Vuzit_ClientException("Error loading XML response");
    }

    if($xml->code) {
      throw new Vuzit_ClientException($xml->msg, (int)$xml->code);
    }

    if(!$xml->web_id) {
      throw new Vuzit_ClientException("Unknown error occurred");
    }

    $result = new Vuzit_Document();
    $result->id = (string)$xml->web_id; 

    return $result;
  }
}
?>
