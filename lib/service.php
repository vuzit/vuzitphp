<?php
/*
  Global data class. 
*/
class Vuzit_Service
{
  /*
    The Vuzit public API key. 
  */
  public static $PublicKey = '';

  /*
    The Vuzit private API key. Do NOT share this with anyone!  
  */
  public static $PrivateKey = '';

  /*
    The URL of the Vuzit web service.  This only needs to be changed if you 
    are running Vuzit Enterprise on your own server.  The default value is 
    "http://vuzit.com".  To turn on SSL for secure documents use the 
    Vuzit secure server URL: 'https://ssl.vuzit.com'. 
  */
  public static $ServiceUrl = 'http://vuzit.com';

  /*
    The user agent of the request.  
  */  
  public static $UserAgent = "VuzitPHP Library 1.1.0";

  /*
    Returns The signature string.  NOTE: If you are going to use this 
    with the Vuzit Javascript API then the value must be encoded with the 
    PHP rawurlencode function.  See the Wiki example for more information:
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
