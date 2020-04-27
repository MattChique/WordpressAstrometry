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

class SvgAnnotation extends Annotation
{
    public function SetGrid($grid)
    {
        $this->grid = $grid;
    }
    private $grid = null;    

    public function Draw()
    {
        // Return SVG Mime
        header('Content-type:  "image/svg+xml"'); 

        // Read font for embeding
        $base64Font = base64_encode(file_get_contents(realpath($this->fontPath)));

        // Define Colors
        $settings = get_option('astrometry_settings');
        $color_ngc = isset($settings['color_ngc']) ? $settings['color_ngc'] : "#cc0000";
        $color_ic = isset($settings['color_ic']) ? $settings['color_ic'] : "#6699FF";
        $color_bright = isset($settings['color_bright']) ? $settings['color_bright'] : "#CCCCCC";
        $color_hd = isset($settings['color_hd']) ? $settings['color_hd'] : "#CCCCCC";
        $color_messier = isset($settings['color_messier']) ? $settings['color_messier'] : "#2266BB";
        $annotation_css = isset($settings['annotation_css']) ? $settings['annotation_css'] : "";
        $color_grid = isset($settings['color_celestialCoordinateGrid']) ? $settings['color_celestialCoordinateGrid'] : "#CCCCCC";

        // Draw SVG head
        echo <<<SVG
<?xml version="1.0" standalone="no"?>
        <!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
        <svg  version="1.1" viewBox="0 0 {$this->displayWidth} {$this->displayHeight}" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
        <defs xmlns="http://www.w3.org/2000/svg">
            <style type="text/css">
                @font-face {
                    font-family: 'Open Sans';
                    src: url(data:application/font-woff;charset=utf-8;base64,{$base64Font}) format('woff');
                    font-weight: normal;
                    font-style: normal;
                }

                text { fill:#CCC; stroke:none; font-family:Open Sans, Verdana, Arial; font-size:{$this->fontSize}px; }
                rect, line, ellipse { stroke:#CCC; }
                rect { fill:rgba(0,0,0,0.5); }

                rect.type-ic, line.type-ic, ellipse.type-ic { stroke: {$color_ic}; }
                rect.type-ngc, line.type-ngc, ellipse.type-ngc { stroke: {$color_ngc}; }
                rect.type-bright, line.type-bright, ellipse.type-bright { stroke: {$color_bright}; }
                rect.type-hd, line.type-hd, ellipse.type-hd { stroke: {$color_hd}; }
                rect.type-messier, line.type-messier, ellipse.type-messier { stroke: {$color_messier}; }

                .grid polyline  { stroke: {$color_grid}; stroke-dasharray: 2,4;}
                .grid ellipse { stroke-width:0; fill: {$color_grid}; }
                .grid text { fill: {$color_grid}; }

                {$annotation_css}

            </style>
            <filter id="dropShadow" x="0" y="0" width="200%" height="200%">
                <feOffset result="offOut" in="SourceAlpha" dx="1.5" dy="1.5"/>
                <feGaussianBlur result="blurOut" in="offOut" stdDeviation="2"/>
                <feBlend in="SourceGraphic" in2="blurOut" mode="normal"/>
            </filter>
        </defs>


        <g xmlns="http://www.w3.org/2000/svg" fill="none"  fill-rule="evenodd" stroke-linecap="square" stroke-linejoin="bevel" filter="url(#dropShadow)">
            <g fill="none" stroke-linecap="square" stroke-linejoin="bevel" transform="matrix(1,0,0,1,0,0)" >
        
SVG;

        // Draw celesial grid, if set
        if(isset($this->grid))
            $this->grid->Draw($this->displayRatio);

        // Draw annotations for each  object
        foreach($this->annotations as $object)
        {	
            if($object["type"] == "hd" && !$this->showHD)
                continue;

            //Draw Circle
            $this->DrawCircle($object);

            //Draw Text
            $this->DrawText($object);
        }

        echo <<<SVG

        </g>
    </g>
</svg>
SVG;
    }

    // Draw circle for an annotation object
    public function DrawCircle($object)
    {            
        $radius = $this->RV($this->getMinMaxRadius($object["radius"]));
        $x = $this->RV($object["pixelx"]);
        $y = $this->RV($object["pixely"]);   
        echo <<<SVG
        
            <ellipse rx="{$radius}" ry="{$radius}" cx="{$x}" cy="{$y}" class="type-{$object["type"]}"/>
SVG;
    }

    // Draw text for an annotation object
    public function DrawText($object, $line = true, $boxed = true)
    {
        $x = $this->RV($object["pixelx"]);
        $y = $this->RV($object["pixely"]);
        $text = $this->GetNames($object["names"]);
        $objectRadius = $this->RV($this->getMinMaxRadius($object["radius"]));

        // Calculate textsize
        $textBox = imagettfbbox($this->fontSize, 0, realpath($this->fontPath), $text);
        $textWidth = $textBox[2];
        $textHeight = $textBox[5]*-1 + $textBox[0];

        if($boxed == true) {

            // Calculate box
            $boxX = $x - floor($textWidth / 2) - 1; //1 for border
            $boxY = $y - $objectRadius - $this->textOffsetToObject - $this->textBoxPadding*2 - $textHeight;
            $boxW = $textWidth;
            $boxH = $textHeight + $this->textBoxPadding*2 - 2; //2 for border
            $textY = $y - $boxY - $this->textBoxPadding - $textHeight + 2; //2 for border
            
            // Draw text rectangle
            echo <<<SVG

            <rect width="{$boxW}" height="{$boxH}" x="{$boxX}.5" y="{$boxY}.5" class="type-{$object["type"]}" />
SVG;
        }

        // Draw text
        echo <<<SVG

            <text x="{$x}" y="{$y}" class="type-{$object["type"]}" text-anchor="middle" transform="translate(0 -{$textY})">{$text}</text>
SVG;

        // Draw connecting line between box and circle
        if($line == true) {
            $y1 = $y - $objectRadius;
            $y2 = $y - $this->textOffsetToObject - $objectRadius;
            echo <<<SVG

            <line x1="{$x}" y1="{$y1}" x2="{$x}" y2="{$y2}" class="type-{$object["type"]}" />
SVG;
        }
    }
}
?>