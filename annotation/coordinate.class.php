<? 

class Coord
{
    public $lat;
    public $lon;
    public $x = 0;
    public $y = 0;

    //Latitude/Declination, Longitude/Right Ascension
    public function __construct($lat,$lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }

    // Converts decimal format to DMS ( Degrees / minutes / seconds ) 
    public static function DegToDms($dec)
	{
		
		$vars = explode(".",$dec);
		$deg = $vars[0];
		$tempma = "0.".$vars[1];

		$tempma = $tempma * 3600;
		$min = floor($tempma / 60);
		$sec = $tempma - ($min*60);

        $return = $deg . "° ";
        $return .= $min . "' ";
        $return .= floor($sec) . "''";

		return $return;
	}    

    // Converts decimal format to HMS ( Hour / minutes / seconds ) 
	public static function DegToHms($dec)
	{	
        $hour = floor($dec/15);
        $min = floor((($dec/15)-$hour)*60);
        $sec = floor((((($dec/15)-$hour)*60)-$min)*60);

        $return = $hour . "h ";
        $return .= $min . "' ";
        $return .= $sec . "''";

		return $return;
    } 
}

?>