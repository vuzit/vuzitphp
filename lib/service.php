<?php
/*
  Global data class. 
*/
class Vuzit_Service
{
  private static $publicKey = '';
  private static $privateKey = '';
  private static $serviceUrl = 'http://vuzit.com';
  private static $productName = "VuzitPHP Library 2.2.0";
  private static $userAgent = "VuzitPHP Library 2.2.0";

  // Public static getter and setter methods

  /*
    Returns the public API key.  
   */
  public static function getPublicKey() {
    return self::$publicKey;
  }

  /*
    Sets Vuzit public API key. 
  */
  public static function setPublicKey($key) {
    self::$publicKey = $key;
  }

  /*
    Returns the private API key.  
   */
  public static function getPrivateKey() {
    return self::$privateKey;
  }

  /*
    Sets Vuzit private API key. 
  */
  public static function setPrivateKey($key) {
    self::$privateKey = $key;
  }

  /*
    Returns the service URL.  
   */
  public static function getServiceUrl() {
    return self::$serviceUrl;
  }

  /*
    Sets service URL. 
  */
  public static function setServiceUrl($url) {
    $url = trim($url);
    if(substr($url, -1) == '/') {
      throw new Exception("Trailing slashes (/) in service URLs are invalid");
    }
    self::$serviceUrl = $url;
  }

  /*
    Returns the user agent.  
   */
  public static function getUserAgent() {
    return self::$userAgent;
  }

  /*
    Sets the user agent. 
  */
  public static function setUserAgent($agent) {
    self::$userAgent = ($agent + " (" + self::$productName + ")");
  }

  // Public static methods

  /*
    Returns The signature string.  NOTE: If you are going to use this 
    with the Vuzit Javascript API then the value must be encoded with the 
    PHP rawurlencode function.  See the Wiki example for more information.
  */
  public static function signature($service, $id = '', $time = null, $options = null)
  {
    $time = ($time == null) ? time() : $time;

    $msg = $service . $id . self::getPublicKey() . $time;
    if($options != null) {
      $options_list = array('included_pages', 'watermark', 'query');

      foreach ($options_list as $item) {
        if(array_key_exists($item, $options)) {
          $msg .= $options[$item];
        }
      }
    }
    $hmac = self::hmac_sha1(self::getPrivateKey(), $msg);

    return base64_encode($hmac);
  }

  // Private static methods

  /*
    Returns the HMAC SHA1 value. 
  */
  private static function hmac_sha1($key, $s)
  {
    return pack("H*", sha1((str_pad($key, 64, chr(0x00)) ^ 
                (str_repeat(chr(0x5c), 64))) . pack("H*", sha1((str_pad(
                $key, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) . $s))));
  }
}
?>
