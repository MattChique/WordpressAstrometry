<?php
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class Coord
{
    public $lat;
    public $lon;
    public $x = 0;
    public $y = 0;

    // Latitude/Declination, Longitude/Right Ascension
    public function __construct($lat,$lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }

    public function Draw($text = "")
    {
        echo '<ellipse cx="'.$this->x.'" cy="'.$this->y.'" rx="2" ry="2" />';     
        echo '<text style="transform:translate('.($this->x+5).'px, '.($this->y+13).'px)">'.$text.'</text>';    
    }

    // Converts decimal format to DMS ( Degrees / minutes / seconds ) 
    public static function DegToDms($dec)
    {        
        $deg = floor($dec);
        $tempma = $dec - $deg;
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
