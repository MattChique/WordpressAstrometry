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

    public static function DEC($dec)
	{
		// Converts decimal format to DMS ( Degrees / minutes / seconds ) 
		$vars = explode(".",$dec);
		$deg = $vars[0];
		$tempma = "0.".$vars[1];

		$tempma = $tempma * 3600;
		$min = floor($tempma / 60);
		$sec = $tempma - ($min*60);

        $return = $deg . "° ";

        if($min > 0 || $sec > 0)
            $return .= $min . "' ";

        if($sec > 0)
            $return .= floor($sec) . "''";

		return $return;
	}    

	public static function RA($dec)
	{		
		// Converts decimal format to HMS ( Hour / minutes / seconds ) 
		$vars = explode(".",$dec);
		$deg = floor($vars[0]/(360/24));
		$tempma = "0.".$vars[1];

		$tempma = $tempma * 3600;
		$min = floor($tempma / 60);
		$sec = $tempma - ($min*60);

        $return = $deg . "h ";

        if($min > 0 || $sec > 0)
            $return .= $min . "' ";

        if($sec > 0)
            $return .= floor($sec) . "''";

		return $return;
    } 
}

?>