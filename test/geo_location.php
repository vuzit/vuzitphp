<?php
/*
  Class for loading geo-location by IP address via the IPInfoDB web service.  
*/
class GeoLocation
{
  /*
    Constructor.  Creates an empty document.  This is not called directly.  
    Use upload or findById to load an instance. 
  */
  public function __construct() {
    $this->country_code = null;
    $this->country_name = null;
    $this->region_name = null;
    $this->city = null;
    $this->zippostalcode = null;
    $this->latitude = null;
    $this->longitude = null;
    $this->timezone = null;
    $this->gmtoffset = null;
    $this->dstoffset = null;
  }

  /*
    Returns the country code.  
  */
  public function getCountryCode() {
    return $this->country_code;
  }

  /*
    Returns the country name.  
  */
  public function getCountryName() {
    return $this->country_name;
  }

  /*
    Returns the region name.  
  */
  public function getRegionName() {
    return $this->region_name;
  }

  /*
    Returns the city.
  */
  public function getCity() {
    return $this->city;
  }

  /*
    Loads a new GeoLocation instance by an IP address.  
  */
  public static function find($ip)
  {
    $result = new GeoLocation();

    $url = "http://www.ipinfodb.com/ip_query.php?ip=$ip&output=xml";
    $request = file_get_contents($url);
    

    if ($request) {
      $response = new SimpleXMLElement($request);
    }
    else {
      throw new Exception("Could not load the XML");
    }

    $result->country_code = $response->CountryCode;
    $result->country_name = $response->CountryName;
    $result->region_name = $response->RegionName;
    $result->city = $response->City;
    $result->zippostalcode = $response->ZipPostalCode;
    $result->latitude = $response->Latitude;
    $result->longitude = $response->Longitude;
    $result->timezone = $response->Timezone;
    $result->gmtoffset = $response->Gmtoffset;
    $result->dstoffset = $response->Dstoffset;

    return $result;
  }
}
?>
