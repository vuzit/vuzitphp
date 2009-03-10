<?php
/*
Class: Vuzit_Document
  Class for uploading, loading, and deleting documents using the Vuzit Web
  Service API: http://vuzit.com/developer/documents_api.  
*/
class Vuzit_Document
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
  Function: getId
    Returns the document web ID.  

  Returns: 
    string
  */
  public function getId() {
    return $this->id;
  }

  /*
  Function: setId
    Sets the document web ID. 
  */
  public function setId($value) {
    $this->id = $value;
  }

  /*
  Function: getTitle
    Returns the document title.  

  Returns:
    string
  */
  public function getTitle() {
    return $this->title;
  }

  /*
  Function: setTitle
    Sets the document tile. 
  */
  public function setTitle($value) {
    $this->title = $value;
  }

  /*
  Function: getSubject
    Returns the document subject.  

  Returns:
    string
  */
  public function getSubject() {
    return $this->subject;
  }

  /*
  Function: setSubject
    Sets the document subject. 
  */
  public function setSubject($value) {
    $this->subject = $value;
  }

  /*
  Function: getPageCount
    Returns the document page count.  

  Returns:
    int
  */
  public function getPageCount() {
    return $this->pageCount;
  }

  /*
  Function: setPageCount
    Sets the document page count. 
  */
  public function setPageCount($value) {
    $this->pageCount = (int)$value;
  }

  /*
  Function: getPageWidth
    Returns the document page width.  

  Returns:
    int
  */
  public function getPageWidth() {
    return $this->pageWidth;
  }

  /*
  Function: setPageWidth
    Sets the document page width. 
  */
  public function setPageWidth($value) {
    $this->pageWidth = (int)$value;
  }

  /*
  Function: getPageHeight
    Returns the document page height.  

  Returns:
    int
  */
  public function getPageHeight() {
    return $this->pageHeight;
  }

  /*
  Function: setPageHeight
    Sets the document page height. 
  */
  public function setPageHeight($value) {
    $this->pageHeight = (int)$value;
  }

  /*
  Function: getFileSize
    Returns the document file size.  

  Returns:
    string
  */
  public function getFileSize() {
    return $this->fileSize;
  }

  /*
  Function: setFileSize
    Sets the document file size. 
  */
  public function setFileSize($value) {
    $this->fileSize = (int)$value;
  }

  /*
  Function: destroy
    Deletes a document by the ID.  Returns true if it succeeded.  It throws
    a <Vuzit_Exception> on failure. 
    
  Parameters:
    id - Document ID of the document you would like to destroy.  

  Returns:
    'true' if successful
    
  Example:
    >Vuzit_Service::$PublicKey = 'YOUR_PUBLIC_API_KEY';
    >Vuzit_Service::$PrivateKey = 'YOUR_PRIVATE_API_KEY';
    >
    >$result = Vuzit_Document::destroy("DOCUMENT_ID");
  */
  public static function destroy($id)
  {
    $method = "destroy";
    $params['id'] = $id;
    $result = true;

    $post_params = self::postParams($method, $params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    $url = self::paramsToUrl($post_params);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $xml_string = curl_exec($ch);

    $info = curl_getinfo($ch);
    if($info['http_code'] != 200) {
      throw new Vuzit_Exception("HTTP error " . $info['http_code']);
    }
    curl_close($ch);

    return $result;
  }

  /*
  Function: findById
    Finds a document by the ID.  It throws a <Vuzit_Exception> on failure. 
    
  Parameters:
    id - ID of the document you would like to find.  

  Returns:
    <Vuzit_Document>
    
  Example:
    >Vuzit_Service::$PublicKey = 'YOUR_PUBLIC_API_KEY';
    >Vuzit_Service::$PrivateKey = 'YOUR_PRIVATE_API_KEY';
    >
    >$doc = Vuzit_Document::findById("DOCUMENT_ID");
    >echo "Document id: " . $doc->getId();
    >echo "Document title: " . $doc->getTitle();
  */
  public static function findById($id)
  {
    $method = "show";
    $params['id'] = $id;

    $post_params = self::postParams($method, $params);

    $ch = curl_init();
    $url = self::paramsToUrl($post_params);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $xml_string = curl_exec($ch);
    $info = curl_getinfo($ch);

    if(!$xml_string) {
      throw new Vuzit_Exception("CURL load failed");
    }
    // TODO: This needs to be re-added some time in the future by looking at the
    //       error codes.  I would add it but they aren't documented.  
    //if($info['http_code'] != 200) {
    //  throw new Vuzit_Exception("HTTP error " . $info['http_code']);
    //}

    // Prevent the warnings if the XML is malformed
    $xml = @simplexml_load_string($xml_string); 
    curl_close($ch);

    if(!$xml) {
      throw new Vuzit_Exception("Error loading XML response");
    }
    if($xml->code) {
      throw new Vuzit_Exception($xml->msg, (int)$xml->code);
    }
    if(!$xml->web_id) {
      throw new Vuzit_Exception("Unknown error occurred");
    }

    // Success!
    $result = new Vuzit_Document();
    $result->setId($xml->web_id); 

    if($xml->title) {
      $result->setTitle($xml->title);
      $result->setSubject($xml->subject);
      $result->setPageCount($xml->page_count);
      $result->setPageWidth($xml->width);
      $result->setPageHeight($xml->height);
      $result->setFileSize($xml->file_size);
    }

    return $result;
  }

  /*
  Function: upload
     Uploads a file to Vuzit. It throws a <Vuzit_Exception> on failure. 
  
  Parameters:
    file - Path to the file on the file system
    secure - (Optional) Security type.  A value of 'true' indicates private and a value
             of 'false' indicates public. 
    file_type - (Optional) Type of the file: 'pdf', 'doc', etc.  
     
  Returns:
    <Vuzit_Document>
     
  Example:
    >Vuzit_Service::$PublicKey = 'YOUR_PUBLIC_API_KEY';
    >Vuzit_Service::$PrivateKey = 'YOUR_PRIVATE_API_KEY';
    >
    >$doc = Vuzit_Document::upload("c:/path/to/document.pdf");
    >echo "Document id: " . $doc->getId();
  */
  public static function upload($file, $secure = true, $fileType = null)
  {
    $method = "create";

    if($file_type != null) {
      $params['file_type'] = $fileType;
    }
    $params['secure'] = ($secure) ? '1' : '0';
    $params['upload'] = "@".$file;

    $post_params = self::postParams($method, $params);

    $ch = curl_init();
    $url = Vuzit_Service::$ServiceUrl . "/documents.xml";
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $xml_string = curl_exec($ch);
    if(!$xml_string) {
      throw new Vuzit_Exception("CURL load failed");
    }

    $info = curl_getinfo($ch);
    // TODO: This needs to be re-added some time in the future by looking at the
    //       error codes.  I would add it but they aren't documented.  
    //if($info['http_code'] != 201) {
    //  throw new Vuzit_Exception("HTTP error, expected 201 but got: " . $info['http_code']);
    //}

    // Prevent the warnings if the XML is malformed
    $xml = @simplexml_load_string($xml_string); 
    curl_close($ch);

    if(!$xml) {
      throw new Vuzit_Exception("Error loading XML response");
    }

    if($xml->code) {
      throw new Vuzit_Exception($xml->msg, (int)$xml->code);
    }

    if(!$xml->web_id) {
      throw new Vuzit_Exception("Unknown error occurred");
    }

    // Success!
    $result = new Vuzit_Document();
    $result->setId($xml->web_id); 

    return $result;
  }

  /*
    Returns the default HTTP post parameters array.  
  */
  private static function postParams($method, $params)
  {
    $params['method'] = $method;
    $params['key'] = Vuzit_Service::$PublicKey;

    $timestamp = time();
    $sig = Vuzit_Service::getSignature($method, $params['id'], $timestamp);
    $params['signature'] = $sig;
    $params['timestamp'] = sprintf("%d", $timestamp);

    $result = array();
    foreach ($params as $key => &$val) {
      if(!empty($val)) {
        if (is_array($val)) {
          $val = implode(',', $val);
        }

        if($key != 'upload' && substr($val, 0, 1) == "@"){
          $val = chr(32).$val;
        }

        $result[$key] = $val;
      }
    }

    return $result;
  }

  /*
    Changes an array (hash table) of parameters to a url. 
  */
  private static function paramsToUrl($params)
  {
    $result = Vuzit_Service::$ServiceUrl . "/documents/" . $params['id'] . ".xml?";

     foreach ($params as $key => &$val) {
        $result .= ($key . '=' . rawurlencode($val) . '&');
    }

    return $result;
  }
}
?>