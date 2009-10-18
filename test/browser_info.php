<?php

/*
  Loads up browser information.  
 */
class BrowserInfo
{
    private $name = null;
    private $agent = null;
    private $version = null;

    /*
      Returns the name of the browser.  
     */
    public function getName()
    {
      return $this->name;
    }

    /*
      Returns the version of the browser.  
     */
    public function getVersion()
    {
      return $this->version;
    }

    /*
      Constructor.  Loads a user agent or grabs the local version. 
     */
    public function __construct($userAgent = null)
    {
      $browsers = array("firefox", "msie", "opera", "chrome", "safari",
                        "mozilla", "seamonkey", "konqueror", "netscape",
                        "gecko", "navigator", "mosaic", "lynx", "amaya",
                        "omniweb", "avant", "camino", "flock", "aol");

      $this->agent = strtolower($userAgent);

      foreach($browsers as $browser)
      {
          if (preg_match("#($browser)[/ ]?([0-9.]*)#", $this->agent, $match))
          {
              $this->name = $match[1];
              $this->version = $match[2];
              break;
          }
      }
      $this->AllowsHeaderRedirect = !($this->name == "msie" && $this->version < 7);
    }
}
?>
