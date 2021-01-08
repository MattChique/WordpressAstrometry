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

    // Draws a coordinate as a dot on coords position, opional with text
    public function Draw($text = "")
    {
        echo '<ellipse cx="'.$this->X().'" cy="'.$this->Y().'" rx="2" ry="2" />';     
        echo '<text style="transform:translate('.($this->X()+5).'px, '.($this->Y()+13).'px)">'.$text.'</text>';    
    }

    // Return rouned X for SVG position, we don't need decimal places
    public function X()
    {
        return round($this->x,0);
    }

    // Return rouned Y for SVG position, we don't need decimal places
    public function Y()
    {
        return round($this->y,0);
    }

    // Converts decimal format to DMS ( Degrees / minutes / seconds ) 
    public static function DegToDms($dec)
    {        
        $deg = floor($dec);
        $tempma = $dec - $deg;
        $tempma = $tempma * 3600;
        $min = floor($tempma / 60);
        $sec = round($tempma - ($min*60));

        $return = $deg . "° ";
        $return .= $min . "' ";
        if($sec > 0) $return .= $sec . "''";

        return $return;
    }    

    // Converts decimal format to HMS ( Hour / minutes / seconds ) 
    public static function DegToHms($dec)
    {	
        $sectot = $dec * 3600 / 15;

        $hour = floor($sectot / 60 / 60);
        $sectot = $sectot - $hour *60 *60;

        $min = floor($sectot / 60);
        $sectot = $sectot - $min * 60;

        $sec = round($sectot);

        $return                  = $hour   . "h ";
        $return                 .= $min    . "' ";
        if($sec > 0) $return    .= $sec    . "''";

        return $return;
    } 
}
?>
