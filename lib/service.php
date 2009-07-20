<?php
/*
Class: Vuzit_Service
  Global data class. 
*/
class Vuzit_Service
{
  /*
  Variable: PublicKey
    The Vuzit public API key. 
    
  Example:
    >Vuzit_Service::$PublicKey = 'YOUR_PUBLIC_API_KEY';
  */
  public static $PublicKey = '';

  /*
  Variable: PrivateKey
    The Vuzit private API key. Do NOT share this with anyone!  
    
  Example:
    >Vuzit_Service::$PrivateKey = 'YOUR_PRIVATE_API_KEY';
  */
  public static $PrivateKey = '';

  /*
  Variable: ServiceUrl
    The URL of the Vuzit web service.  This only needs to be changed if you 
    are running Vuzit Enterprise on your own server.  The default value is 
    "http://vuzit.com".  
    
  Example:
    >Vuzit_Service::$ServiceUrl = 'http://vuzit.yourdomain.com';
  */
  public static $ServiceUrl = 'http://vuzit.com';

  /*
  Variable: UserAgent
    The user agent of the request.  
  */  
  public static $UserAgent = "VuzitPHP Library 1.0.1";

  /*
  Function: getSignature
    Returns The signature string.  NOTE: If you are going to use this 
    with the Vuzit Javascript API then the value must be encoded with the 
    PHP rawurlencode function.  See the Wiki example for more information:

    http://wiki.github.com/vuzit/vuzitphp/code-samples
  
  Parameters:
    service - Name of the service: 'show', 'create', or 'destroy'.  
    id - (Optional) ID of the document. 
    time - (Optional) Optional time stamp (e.g. time()).  

  Example:
    >$timestamp = time();
    >$sig = Vuzit_Service::getSignature("show", "DOCUMENT_ID", $timestamp);
  */
  public static function getSignature($service, $id = '', $time = null)
  {
    $result = null;

    $time = ($time == null) ? time() : $time;
    $msg = $service . $id . self::$PublicKey . $time;
    $hmac = self::hmac_sha1(self::$PrivateKey, $msg);

    return base64_encode($hmac);
  }

  /**
   * Returns the HMAC SHA1 value. 
   */
  private static function hmac_sha1($key, $s)
  {
    return pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ 
                (str_repeat(chr(0x5c), 64))) . pack("H*", sha1((str_pad(
                $key, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $s))));
  }
}
?>
