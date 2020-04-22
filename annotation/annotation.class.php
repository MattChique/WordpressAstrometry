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

class Annotation
{
    private $imageUrl = "";
    protected $displayWidth = 1120;
    protected $displayHeight = 1120;
    protected $displayRatio = 0;
    protected $annotations = null;

    protected $fontPath = "";
    protected $fontSize = 10;

    protected $textBoxPadding = 4;
    protected $textOffsetToObject = 10;

    protected $showHD = false;

    public function __construct($imageUrl, $displayWidth, $annotations)
    {
        $this->imageUrl = $imageUrl;
        $imageSize = getimagesize($imageUrl[0]);

        if($displayWidth < 1)
            $this->displayWidth = $imageSize[0];
        else
            $this->displayWidth = $displayWidth;

        $this->displayRatio = $this->displayWidth / $imageSize[0];
        $this->displayHeight =  floor($imageSize[1]*$this->displayRatio);
        $this->annotations = $annotations;
    }

    public function getMinMaxRadius($radius)
    {
        if($radius < 11)
            $radius = 11;

        if($radius > $this->displayWidth)
            $radius =  $this->displayWidth;	

        if($radius > $this->displayHeight)
            $radius = $this->displayHeight;	
        
        return $radius;
    }

    public function SetFont($fontPath, $fontSize)
    {
        $this->fontPath = $fontPath;
        $this->fontSize = $fontSize;
    }

    public function ShowHD($show = true)
    {
        $this->showHD = $show;
    }

    public function RV($value) {
        return floor($value*$this->displayRatio);
    }

    public function GetNames($names)
    {
        $text = "";
        if(sizeof($names) > 1) 
        {
            foreach($names as $name)
                $text = $text . ", " . $name;

            $text = ltrim($text, ", ");
        }
        else
        {
            $text = $names[0];
        }

        $text = mb_convert_encoding($text, "HTML-ENTITIES", "UTF-8");
        return preg_replace("/\b^u([0-9a-f]{2,4})\b/", "&#x\\1;", $text);
    }
}
?>