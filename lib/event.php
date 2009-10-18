<?php
/*
  Class for loading events. To use this class you need to sign up 
  for Vuzit first: http://vuzit.com/signup
*/
class Vuzit_Event extends Vuzit_Base
{
  /*
    Constructor.  Creates an empty event object.  This is not called directly.  
    Use finaAll to load an instance. 
  */
  public function __construct() {
    $this->web_id = -1;
    $this->event = null;
    $this->remoteHost = null;
    $this->referer = null;
    $this->userAgent = null;
    $this->valueType = null;
    $this->requestedAt = null;
    $this->page = -1;
    $this->duration = -1;
  }

  /*
    Returns the duration on the page. 
  */
  public function getDuration() {
    return $this->duration;
  }

  /*
    Returns the document web ID.  
  */
  public function getWebId() {
    return $this->web_id;
  }

  /*
    Returns the event.  
  */
  public function getEvent() {
    return $this->event;
  }

  /*
    Returns the remote host.  
  */
  public function getRemoteHost() {
    return $this->remoteHost;
  }

  /*
    Returns the referer.
  */
  public function getReferer() {
    return $this->referer;
  }

  /*
    Returns the browser user agent.  
  */
  public function getUserAgent() {
    return $this->userAgent;
  }

  /*
    Returns the value type.  
  */
  public function getValue() {
    return $this->valueType;
  }

  /*
    Returns the time of the request.  
  */
  public function getRequestedAt() {
    return $this->requestedAt;
  }

  /*
    Returns the page number.  Returns -1 if not set.  
  */
  public function getPage() {
    return $this->page;
  }

  /*
    Loads an array of events.  It throws a <Vuzit_ClientException> on failure.  
  */
  public static function findAll($webId, $options = null)
  {
    $method = "show";

    if(!$webId) {
      throw new Vuzit_ClientException("No webId parameter specified");
    }

    $post_params = self::postParameters($method, $options, $webId);
    $post_params["web_id"] = $webId;

    $ch = self::curlRequest();
    $url = self::parametersToUrl("events", $post_params);

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

    if(!$xml->event) {
      throw new Vuzit_ClientException("Unknown error occurred");
    }

    $result = array();

    foreach($xml->event as $node)
    {
      $event = new Vuzit_Event();
      $event->web_id = (string)$node->web_id; 
      $event->event = (string)$node->event; 
      $event->remoteHost = (string)$node->remote_host;
      $event->referer = (string)$node->referer;
      $event->userAgent = (string)$node->user_agent;
      $event->valueType = (string)$node->value;
      $event->requestedAt = (int)$node->requested_at;
      $event->page = $node->page != null ? (int)$node->page : -1;
      $event->duration = $node->duration != null ? (int)$node->duration : -1;

      $result[] = $event;
    }

    return $result;
  }
}
?>
