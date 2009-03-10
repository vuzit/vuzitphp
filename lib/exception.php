<?php
/*
Class: Vuzit_Exception
  Vuzit library exception handler class. 
*/
class Vuzit_Exception extends Exception
{
  /*
  Function: __construct
    Constructor

  Parameters:
    message - Error message sent from the web service.  
    code - Error code from the web service.  

  Returns: 
    Vuzit_Exception
    
  Example:
    >try {
    >  $doc = Vuzit_Document::findById("DOCUMENT_ID");
    >}
    >catch(Vuzit_Exception $ex) {
    >  echo "Error code: " . $ex->getCode() . ", message: " . $ex->getMessage();
    >}
  */
  public function __construct($message, $code = 0) {
    parent::__construct($message, $code);
  }

  /*
  Function: __toString
    Returns the error in a string format.  
  */
  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }
}
?>