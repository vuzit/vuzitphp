<?php
/*
  Base class for Vuzit resources.  To use this class you need to sign up for 
  Vuzit first: http://vuzit.com/signup
*/
class Vuzit_Base
{
  /*
    Returns a CURL request.  
  */
  protected static function curlRequest()
  {
    $result = curl_init();

    if(substr(Vuzit_Service::$ServiceUrl, 0, 8) == "https://") {
      curl_setopt($result, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($result, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($result, CURLOPT_USERAGENT, Vuzit_Service::$UserAgent); 

    return $result;
  }

  /*
    Changes an array (hash table) of parameters to a url. 
  */
  protected static function paramsToUrl($resource, $params, $id = null)
  {
    $result = Vuzit_Service::$ServiceUrl . "/" . $resource;
    if($id != null) {
      $result .= "/" . $id;
    }
    $result .= ".xml?";

    foreach ($params as $key => &$val) {
      $result .= ($key . '=' . rawurlencode($val) . '&');
    }

    return $result;
  }

  /*
    Returns the default HTTP post parameters array.  
  */
  protected static function postParams($method, $params, $id = '')
  {
    $params['method'] = $method;
    $params['key'] = Vuzit_Service::$PublicKey;

    $timestamp = time();
    $sig = Vuzit_Service::getSignature($method, $id, $timestamp);
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
        // TODO: If is_bool && $val == true then turn to "1"
        //       If is_bool && $val == false then turn to "0"

        $result[$key] = $val;
      }
    }

    return $result;
  }
}
?>
