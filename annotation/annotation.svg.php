<?
class SvgAnnotation extends Annotation
{
    private $mime = "image/svg+xml";

    public function Draw()
    {
        header('Content-type: ' . $this->mime); 

        $base64Font = base64_encode(file_get_contents(realpath($this->fontPath)));

        $settings = get_option('astrometry_settings');
        $color_ngc = $settings['color_ngc'];
        $color_ic = $settings['color_ic'];
        $color_bright = $settings['color_bright'];
        $color_hd = $settings['color_hd'];
        $annotation_css = $settings['annotation_css'];

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

        foreach($this->annotations as $a)
        {	
            if($a["type"] == "hd" && !$this->showHD)
                continue;

            //Draw Circle
            $this->DrawCircle($a);

            //Draw Text
            $this->DrawText($a);
        }

        echo <<<SVG

        </g>
    </g>
</svg>
SVG;
    }

    public function DrawCircle($circle)
    {            
        $radius = $this->RV($this->getMinMaxRadius($circle["radius"]));
        $x = $this->RV($circle["pixelx"]);
        $y = $this->RV($circle["pixely"]);   
        echo <<<SVG
        
            <ellipse rx="{$radius}" ry="{$radius}" cx="{$x}" cy="{$y}" class="type-{$circle["type"]}"/>
SVG;
    }

    public function DrawText($circle, $line = true, $boxed = true)
    {
        $x = $this->RV($circle["pixelx"]);
        $y = $this->RV($circle["pixely"]);
        $text = $this->GetNames($circle["names"]);
        $objectRadius = $this->RV($this->getMinMaxRadius($circle["radius"]));

        $textBox = imagettfbbox($this->fontSize, 0, realpath($this->fontPath), $text);
        $textWidth = $textBox[2];
        $textHeight = $textBox[5]*-1 + $textBox[0];

        if($boxed == true) {

            $boxX = $x - floor($textWidth / 2);
            $boxY = $y - $objectRadius - $this->textOffsetToObject - $this->textBoxPadding*2 - $textHeight;
            $boxW = $textWidth;
            $boxH = $textHeight + $this->textBoxPadding*2 - 2; //2 for border
            $textY = $y - $boxY - $this->textBoxPadding - $textHeight + 2; //2 for border
            
            echo <<<SVG

            <rect width="{$boxW}" height="{$boxH}" x="{$boxX}.5" y="{$boxY}.5" class="type-{$circle["type"]}" />
SVG;
        }
        echo <<<SVG

            <text x="{$x}" y="{$y}" class="type-{$circle["type"]}" text-anchor="middle" transform="translate(0 -{$textY})">{$text}</text>
SVG;

        if($line == true) {
            $y1 = $y - $objectRadius;
            $y2 = $y - $this->textOffsetToObject - $objectRadius;
            echo <<<SVG

            <line x1="{$x}" y1="{$y1}" x2="{$x}" y2="{$y2}" class="type-{$circle["type"]}" />
SVG;
        }
    }
}
?>