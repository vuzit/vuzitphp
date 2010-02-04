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

    if(substr(Vuzit_Service::getServiceUrl(), 0, 8) == "https://") {
      curl_setopt($result, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($result, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($result, CURLOPT_USERAGENT, Vuzit_Service::getUserAgent()); 
    // 60 second timeout to prevent timeouts with large requests,
    // a busy server or IIS delays
    curl_setopt($result, CURLOPT_CONNECTTIMEOUT, 60); 

    return $result;
  }

  /*
    Cleans the parameters.  
   */
  protected static function parametersClean($parameters)
  {
    $result = array();

    foreach ($parameters as $key => &$val)
    {
      // Convert true/false to "1" and "0".  
      if(is_bool($val))
      {
        $val = $val ? "1" : "0";
      }
      else
      {
        // Remove empty values
        if(!empty($val)) {
          // Handle file uploads via a HTTP post operation
          if($key != 'upload' && substr($val, 0, 1) == "@") {
            $val = chr(32).$val;
          }
        }
      }

      $result[$key] = $val;
    }

    return $result;
  }

  /*
    Changes an array (hash table) of parameters to a url. 
  */
  protected static function parametersToUrl($resource, $params, $id = null, 
                                            $extension = 'xml')
  {
    $params = self::parametersClean($params);

    $result = Vuzit_Service::getServiceUrl() . "/" . $resource;
    if($id != null) {
      $result .= "/" . $id;
    }
    $result .= "." . $extension . "?";

    foreach ($params as $key => &$val) {
      $result .= ($key . '=' . rawurlencode($val) . '&');
    }

    return $result;
  }

  /*
    Returns the default HTTP post parameters array.  
  */
  protected static function postParameters($method, $params, $id = '')
  {
    if($params == null) {
      $params = array();
    }
    $params['method'] = $method;
    $params['key'] = Vuzit_Service::getPublicKey();

    // Signature variables
    $timestamp = time();
    $params['timestamp'] = sprintf("%d", $timestamp);

    $sig = Vuzit_Service::signature($method, $id, $timestamp, $params);
    $params['signature'] = $sig;

    return $params;
  }
}
?>
