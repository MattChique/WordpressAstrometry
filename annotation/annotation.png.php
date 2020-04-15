<?
class PngAnnotation extends Annotation
{
    private $mime = "image/png";

	private $white;
	private $black; 
	private $lightgrey;
	private $blackTrans;

    public function Draw()
    {
		header('Content-type: ' . $this->mime); 

		//Create image
		$img = imagecreatetruecolor($this->displayWidth, $this->displayHeight);
		imagesavealpha($img, true);
		imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));

		//Define Colors
		$this->white = imagecolorallocate($img, 254, 254, 254);	
		$this->lightgrey = imagecolorallocate($img, 120, 120, 120);	
		$this->black = imagecolorallocate($img, 0, 0, 0);	
		$this->blackTrans = imagecolorallocatealpha($img, 0, 0, 0, 80);

		foreach($this->annotations as $a)
		{	
            if($a["type"] == "hd" && !$this->showHD)
                continue;

			//Draw Circle
			$this->DrawCircle($img, $a, $this->black);
			$this->DrawCircle($img, $a, $this->white);

			//Draw Text
			$this->DrawText($img, $a);
		}

		//Return and destroy
		imagepng($img); 
		imagedestroy($img);
	}

    public function DrawCircle($img, $circle, $color)
    {            
        $radius = $this->RV($this->getMinMaxRadius($circle["radius"]))*2;
        $x = $this->RV($circle["pixelx"]);
        $y = $this->RV($circle["pixely"]);   

		$cRatio = 4;
		$radius = floor($radius);
		
		if($radius > 1200)
			$cRatio = 2;	
		
		$imgArc = imagecreatetruecolor($radius*$cRatio+$cRatio, $radius*$cRatio+$cRatio);	
		imagesavealpha($imgArc, true);	
		imagefill($imgArc, 0, 0, imagecolorallocatealpha($imgArc, 0, 0, 0, 127));	
		imagesetthickness ( $imgArc , $cRatio );
		
		imagearc($imgArc, floor(floor($radius*$cRatio)/2)+floor($cRatio/2), floor(floor($radius*$cRatio)/2)+floor($cRatio/2), floor($radius*$cRatio), floor($radius*$cRatio), 0, 360, $color);
		
		imagecopyresampled($img, $imgArc, $x-floor($radius/2), $y-floor($radius/2), 0, 0, $radius, $radius, floor($radius*$cRatio+$cRatio), floor($radius*$cRatio+$cRatio));
		
		imagedestroy($imgArc);
    }

    public function DrawText($img, $circle, $line = true, $boxed = true)
    {
	    $objectRadius = $this->RV($this->getMinMaxRadius($circle["radius"]));
        $x = $this->RV($circle["pixelx"]);
		$y = $this->RV($circle["pixely"]); 
		$text = $this->GetNames($circle["names"]);
		$textBoxPadding = $this->textBoxPadding;
		$textOffsetToObject = $this->textOffsetToObject;

		$textBox = imageftbbox($this->fontSize, 0, realpath($this->fontPath), $text);
		$textWidth = $textBox[2];
		$textHeight = $textBox[5]*-1 + $textBox[1];
		
		if($boxed == true) {
			imagefilledrectangle($img, 
							$x - floor($textWidth / 2) - $textBoxPadding, 
							$y - $textHeight - $textBoxPadding - $textOffsetToObject - $textBox[1] - $objectRadius, 
							$x + floor($textWidth / 2) + $textBoxPadding,
							$y + $textBoxPadding - $textOffsetToObject - $objectRadius, 
							$this->blackTrans);
			imagerectangle($img, 
							$x - floor($textWidth / 2) - $textBoxPadding, 
							$y - $textHeight - $textBoxPadding - $textOffsetToObject - $textBox[1] - $objectRadius, 
							$x + floor($textWidth / 2) + $textBoxPadding , 
							$y + $textBoxPadding - $textOffsetToObject - $objectRadius, 
							$this->lightgrey);
		}
	
		imagefttext($img, $this->fontSize, 0, 	$x - floor($textWidth / 2)+1, 	$y - $textOffsetToObject - $objectRadius + 1 - $textBox[1], 	$this->black, realpath($this->fontPath), $text);
		imagefttext($img, $this->fontSize, 0, 	$x - floor($textWidth / 2), 	$y - $textOffsetToObject - $objectRadius - $textBox[1], 		$this->white, realpath($this->fontPath), $text);
	
		if($line == true) {
			imageline($img, $x, $y - $objectRadius, $x , $y - $textOffsetToObject - $objectRadius + $textBoxPadding, $this->lightgrey);
		}
    }
}

?>