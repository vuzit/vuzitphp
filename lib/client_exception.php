<?php
/*
  Vuzit library exception handler class. 
*/
class Vuzit_ClientException extends Exception
{
  /*
    Constructor
  */
  public function __construct($message, $code = 0) {
    parent::__construct($message, $code);
  }

  /*
    Returns the error in a string format.  
  */
  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }
}
?>
