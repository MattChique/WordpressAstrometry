<?
class SvgAnnotation extends Annotation
{
    private $mime = "image/svg+xml";

    public function Draw()
    {
        header('Content-type: ' . $this->mime); 

        echo <<<SVG
<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg width="{$this->displayWidth}" height="{$this->displayHeight}" version="1.1" viewBox="0 0 {$this->displayWidth} {$this->displayHeight}" 
preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" overflow="hidden">

        <defs xmlns="http://www.w3.org/2000/svg">
            <style type="text/css">
                @font-face { font-family: Open Sans; src: url('{$this->fontPath}'); font-weight: normal; font-style: normal; }
                text { fill:#FFF; stroke: none; font-family:Open Sans; font-size:{$this->fontSize}px; }
                rect, line, ellipse { stroke: rgba(200,200,200,0.8); }
                rect { fill:rgba(0,0,0,0.5); }
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

        $textBox = imageftbbox($this->fontSize, 0, realpath($this->fontPath), $text);
        $textWidth = $textBox[2];
        $textHeight = $textBox[5]*-1 + $textBox[0];

        if($boxed == true) {
            $textWidth = $textWidth + $this->textBoxPadding*2 - 2;
            $textHeight = $textHeight + $this->textBoxPadding*2;

            $xbox = $x - floor($textWidth / 2);
            $ybox = $y - $textHeight - $this->textOffsetToObject - $objectRadius;

            $textx = $x;
            $texty = $ybox - 1 + $this->textBoxPadding/2;
            
            echo <<<SVG

            <rect width="{$textWidth}" height="{$textHeight}" x="{$xbox}.5" y="{$ybox}.5" class="type-{$circle["type"]}" />
SVG;
        }
        echo <<<SVG

            <text x="{$textx}" y="{$texty}" dominant-baseline="text-before-edge" class="type-{$circle["type"]}" text-anchor="middle">{$text}</text>
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