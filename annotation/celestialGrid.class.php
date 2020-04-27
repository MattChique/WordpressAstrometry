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

require_once("coordinate.class.php");

class CelestialGrid 
{
    private $cOrientation;
    private $cCenterRa;
    private $cCenterDec;
    private $cRadius;
    private $cRadiusPx;

    private $isPole;
    private $steps;
    private $scale;

    private $gridArray;

    public function __construct($c)
    {
        //Calibration data
        $this->cOrientation     = $c["orientation"];    //rotation
        $this->cCenterRa        = $c["ra"];             //center ra
        $this->cCenterDec       = $c["dec"];            //center dec
        $this->cRadius          = $c["radius"];         //fieldradius
        $this->cRadiusPx        = (360 * 60 * 60 / $c["pixscale"]) / (2 * M_PI); //Radius in pixels

        //Calculate scale and steps
        $this->scale = $this->GetScale();
        $this->steps = $this->GetSteps();   
        $this->isPole = $this->IsPole();
    }

    public function CalculateGridPole($imageRatio)
    {    
        $cLat = $this->cCenterDec;
        $cLon = $this->cCenterRa;

        //Coord of real center
        $center = new Coord($cLat,$cLon);

        //Nearest coord on scale
        $centerOffset = new Coord(90,0);
        $centerOffset->x = $this->GetDistance($center, new Coord($cLat,0)) * $imageRatio;
        $centerOffset->y = -$this->GetDistance($center, new Coord(90,$cLon)) * $imageRatio;

        //Draw pole
        $centerOffset->Draw("Pole");

        //Calculate coords in grid for x and y axes
        for($x = 0; $x < 37; $x++)
        {
            for($y = 1; $y < $this->steps; $y++) //y=1 for not drawing lines to Pole 
            {
                //New coord in grid
                $coord = new Coord($centerOffset->lat - ($y*$this->scale), $centerOffset->lon + ($x*$this->scale*10));

                //Calculate distance to offset coords
                $xOffsetCoord = new Coord($centerOffset->lat, $coord->lon);                
                $xc = $this->GetDistance($coord, $xOffsetCoord) * $imageRatio;
                $yc = 0;

                //Rotate Orientation and RA
                $angle = deg2rad( ($this->cOrientation - $this->cCenterRa - $x*10)  ) ; 
                $coord->y = -($xc*sin($angle) - $yc*cos($angle)) + $centerOffset->y;
                $coord->x = ($xc*cos($angle) +  $yc*sin($angle)) + $centerOffset->x;

                //Put in array
                $this->gridArray[$x][$y] = $coord;
            }
        }   

        //Update Steps for printing all coords
        $this->steps = 36 * $this->steps;
    }

    public function CalculateGridPole($imageRatio)
    {    
        $cLat = $this->cCenterDec;
        $cLon = $this->cCenterRa;
        $this->scale = 10;
        $this->steps = 18;

        //Coord of real center
        $center = new Coord($cLat,$cLon);
        $center->x = "0px";
        $center->y = "0px";

        //Nearest coord on scale
        $centerOffset = new Coord(90,0);
        $centerOffset->x = $this->GetDistance($center, new Coord($cLat,0)) * $imageRatio;
        $centerOffset->y = -$this->GetDistance($center, new Coord(90,$cLon)) * $imageRatio;

        $centerOffset->Draw("pole");

print_r($centerOffset);

        //Calculate coords in grid for x and y axes
        for($x = -$this->steps; $x < $this->steps; $x++)
        {
            for($y = -$this->steps; $y < $this->steps; $y++)
            {
                //New coord in grid
                $lat = 90 - ($y*$this->scale/10);
                $lon = ($x*$this->scale);

                

                $coord = new Coord($lat, $lon);

                $yOffsetCoord = new Coord($centerOffset->lat, $coord->lon);
                $xOffsetCoord = new Coord($coord->lat, $centerOffset->lon);

                //Calculate distance to offset coords
                $xc = $this->GetDistance($coord, $xOffsetCoord) * $imageRatio;
                $yc = $this->GetDistance($coord, $yOffsetCoord) * $imageRatio;

                if($x < 0) 
                    $xc = ($xc )*-1 + $centerOffset->x;
                else
                    $xc = $xc + $centerOffset->x;

                if($y < 0) 
                    $yc = ($yc)*-1 + $centerOffset->y;
                else
                    $yc = $yc + $centerOffset->y;

                //Rotate
                //$angle = deg2rad($this->cOrientation);
                //$coord->y = -($xc*sin($angle) - $yc*cos($angle));
                //$coord->x = ($xc*cos($angle) +  $yc*sin($angle));

                if($lon == 0)
                {
                    $yc = $centerOffset->y;
                    $xc = $centerOffset->x;
                }
                $coord->y = $yc;
                $coord->x = $xc;

                //Put in array
                $this->gridArray[$x][$y] = $coord;
            }
        }   
        print_r($this->gridArray);
    }

    public function CalculateGrid($imageRatio)
    {    
        $cLat = $this->cCenterDec;
        $cLon = $this->cCenterRa;

        //Coord of real center
        $center = new Coord($cLat,$cLon);
        $center->x = "0px";
        $center->y = "0px";

        //Nearest coord on scale
        $centerOffset = new Coord($this->RoundC($cLat),$this->RoundC($cLon));
        $centerOffset->x = $this->GetDistance($center, new Coord($cLat,$this->RoundC($cLon))) * $imageRatio;
        $centerOffset->y = $this->GetDistance($center, new Coord($this->RoundC($cLat),$cLon)) * $imageRatio;

        //Calculate coords in grid for x and y axes
        for($x = -$this->steps; $x <= $this->steps; $x++)
        {
            for($y = -$this->steps; $y <= $this->steps; $y++)
            {
                //New coord in grid
                $coord = new Coord($centerOffset->lat - ($y*$this->scale), $centerOffset->lon - ($x*$this->scale));

                //Offset coords for both axes
                $yOffsetCoord = new Coord($centerOffset->lat, $coord->lon);
                $xOffsetCoord = new Coord($coord->lat, $centerOffset->lon);

                //Calculate distance to offset coords
                $xc = $this->GetDistance($coord, $xOffsetCoord) * $imageRatio;
                $yc = $this->GetDistance($coord, $yOffsetCoord) * $imageRatio;

                if($x < 0) 
                    $xc = ($xc )*-1 + $centerOffset->x;
                else
                    $xc = $xc + $centerOffset->x;

                if($y < 0) 
                    $yc = ($yc)*-1 + $centerOffset->y;
                else
                    $yc = $yc + $centerOffset->y;

                //Rotate
                $angle = deg2rad($this->cOrientation);
                $coord->y = -($xc*sin($angle) - $yc*cos($angle));
                $coord->x = ($xc*cos($angle) +  $yc*sin($angle));

                //Put in array
                $this->gridArray[$x][$y] = $coord;
            }
        }
    }

    public function Draw($imageRatio)
    {    
        //Draw grid group
        echo <<<SVG
        
        <g class="grid" fill="none" stroke-linecap="square" stroke-linejoin="bevel" transform="matrix(1,0,0,1,0,0)" style="transform: translate(50%,50%)" >

SVG;

        if($this->cCenterDec + $this->cRadius > 90)
        {
            $this->CalculateGridPole($imageRatio);
        }
        else
        {
            $this->CalculateGrid($imageRatio);
        }


        //Calculate Grid Array
        if($this->isPole)
            $this->CalculateGridPole($imageRatio);
        else
            $this->CalculateGrid($imageRatio);

        //Draw lines for Dec
        for($x = -$this->steps; $x <= $this->steps; $x++)
        {
            $coords = "";
            for($y = -$this->steps; $y <= $this->steps; $y++)
            {
                if(!isset($this->gridArray[$x][$y]))
                    continue;

                //Get coord
                $coord = $this->gridArray[$x][$y];

                //Set polyline position
                $coords .= $coord->x . "," .$coord->y . " ";
            }

            //Print coords as polyline
            if($coords != "") 
            {
                echo '<polyline points="'.$coords.'" />';
                echo "\n";
            }
        }
        
        //Draw lines for Ra
        for($x = -$this->steps; $x <= $this->steps; $x++)
        {
            $coords = "";
            for($y = -$this->steps; $y <= $this->steps; $y++)
            {
                if(!isset($this->gridArray[$y][$x]))
                    continue;

                //Get coord
                $coord = $this->gridArray[$y][$x];

                //Set polyline position
                $coords .= $coord->x . "," .$coord->y . " ";
            }

            //Print coords as polyline
            if($coords != "") 
            {
                echo '<polyline points="'.$coords.'" />';
                echo "\n";
            }
        }

        //Draw Text
        for($x = -$this->steps; $x <= $this->steps; $x++)
        {
            for($y = -$this->steps; $y <= $this->steps; $y++)
            {
                if(!isset($this->gridArray[$y][$x]))
                    continue;

                $coord = $this->gridArray[$y][$x];

                if($y == -1 || ($this->isPole && $y == 0))
                {
                    echo '<ellipse cx="'.$coord->x.'" cy="'.$coord->y.'" rx="2" ry="2" />';    
                    echo '<text style="transform:translate('.($coord->x+5).'px, '.($coord->y-5).'px)">'.Coord::DegToDms($coord->lat).'</text>';               
                }

                if($x == -1 || ($this->isPole && ($x == 3 && $y != 0)))
                {
                    echo '<ellipse cx="'.$coord->x.'" cy="'.$coord->y.'" rx="2" ry="2" />';     
                    echo '<text style="transform:translate('.($coord->x+5).'px, '.($coord->y+13).'px)">'.Coord::DegToHms($coord->lon).'</text>';                             
                }
            }
        }

        //Draw grid group end
        echo <<<SVG

        </g>
SVG;
    }

    //Calculate how many steps it needs to fill out the complete image area
    public function GetSteps()
    {
        $steps = floor(($this->cRadius*2) / $this->scale) + 2 ;

        return $steps;        
    }

    //Calculate the scale to show for a minimum required lines; return distances in deg.
    public function GetScale($minLine = 3)
    {
        $pWidth = $this->cRadius * 2;

        if($pWidth / 1 > $minLine)
            return 1;

        if($pWidth / 0.5 > $minLine)
            return 0.5;
            
        if($pWidth / 0.2 > $minLine)
            return 0.2;

        if($pWidth / 0.1 > $minLine)
            return 0.1;
    }

    //Rounds a number scale-dependent
    public function RoundC($coord)
    {
        if($this->scale >= 0.5)
        {
            return floor($coord * 2) / 2;
        }

        if($this->scale == 0.2)
        {
            return floor($coord * 5) / 5;
        }

        return round($coord,1);
    }

    public function IsPole()
    {
        if($this->cCenterDec + $this->cRadius > 90)
            return true;

        return false;
    }

    //Vincenty Formula
    public function GetDistance($coord1, $coord2)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($coord1->lat);
        $lonFrom = deg2rad($coord1->lon);
        $latTo = deg2rad($coord2->lat);
        $lonTo = deg2rad($coord2->lon);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $this->cRadiusPx;
    }
}


?>