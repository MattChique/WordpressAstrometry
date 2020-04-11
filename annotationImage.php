<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

//Read original image annotations
$jsonAnnotations = json_decode(get_post_meta($_GET["postid"], "astrometry_annotations", true));
$imageUrl = wp_get_attachment_image_src($_GET["mediaid"], 'original');

//Imagesize
if(isset($_GET["w"])){
	$displayWidth = $_GET["w"];
} else {
	$displayWidth = 1120;
}
$imageSize = getimagesize($imageUrl[0]);
$ratio = $displayWidth / $imageSize[0];
$displayHeight =  $imageSize[1]*$ratio;

//Header
header("Content-type: image/png");

//Create image
$img = imagecreatetruecolor($displayWidth, $displayHeight);
imagesavealpha($img, true);
imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));

//Define Colors
$white = imagecolorallocate($img, 254, 254, 254);	
$darkgrey = imagecolorallocate($img, 33, 33, 33);	
$lightgrey = imagecolorallocate($img, 120, 120, 120);	
$black = imagecolorallocate($img, 0, 0, 0);	
$red = imagecolorallocate($img, 255, 0, 0);	
$blackTrans = imagecolorallocatealpha($img, 0, 0, 0, 80);

//Define settings
$font = dirname(__FILE__) . '\assets\font\OpenSans-Regular.ttf';
$fontsize = 10;
$textBoxPadding = 4;
$textOffsetToObject = 10;

//Draw circles
foreach($jsonAnnotations->annotations as $a)
{	
	if($a->type == "hd")
		continue;

	$radius = getMinMaxRadius($a->radius);

	drawCircle($img, ($radius-2)*$ratio*2, $a->pixelx*$ratio, $a->pixely*$ratio, $black);
	drawCircle($img, $radius*$ratio*2, $a->pixelx*$ratio, $a->pixely*$ratio, $white);
}

//Draw annotations
foreach($jsonAnnotations->annotations as $a)
{
	if($a->type == "hd")
		continue;

	$text = "";
	if(sizeof($a->names) > 1) 
	{
		foreach($a->names as $name)
			$text = $text . ", " . $name;

		$text = ltrim($text, ", ");
	}
	else
	{
		$text = $a->names[0];
	}

	$text = mb_convert_encoding($text, "HTML-ENTITIES", "UTF-8");
	$text = preg_replace("/\b^u([0-9a-f]{2,4})\b/", "&#x\\1;", $text);

	//Draw Text
	drawText($img, $a->pixelx*$ratio, $a->pixely*$ratio, $text, getMinMaxRadius($a->radius)*$ratio);
}

//Return and destroy
imagepng($img); 
imagedestroy($img);


function drawText($img, $x, $y, $text, $objectRadius, $line = true, $boxed = true)
{
	global $textOffsetToObject, $textBoxPadding, $fontsize, $font, $darkgrey, $white, $black, $lightgrey, $blackTrans;
	
	$x=floor($x);
	$y=floor($y);
	$objectRadius = floor($objectRadius);
	
	$textBox = imageftbbox($fontsize, 0, $font, $text);
	$textWidth = $textBox[2];
	$textHeight = $textBox[5]*-1 + $textBox[1];
	
	if($boxed == true) {
		imagefilledrectangle($img, 
						$x - floor($textWidth / 2) - $textBoxPadding, 
						$y - $textHeight - $textBoxPadding - $textOffsetToObject - $textBox[1] - $objectRadius, 
						$x + floor($textWidth / 2) + $textBoxPadding,
						$y + $textBoxPadding - $textOffsetToObject - $objectRadius, 
						$blackTrans);
		imagerectangle($img, 
						$x - floor($textWidth / 2) - $textBoxPadding, 
						$y - $textHeight - $textBoxPadding - $textOffsetToObject - $textBox[1] - $objectRadius, 
						$x + floor($textWidth / 2) + $textBoxPadding , 
						$y + $textBoxPadding - $textOffsetToObject - $objectRadius, 
						$lightgrey);
	}

	imagefttext($img, $fontsize, 0, 	$x - floor($textWidth / 2)+1, 	$y - $textOffsetToObject - $objectRadius + 1 - $textBox[1], 	$black, $font, $text);
	imagefttext($img, $fontsize, 0, 	$x - floor($textWidth / 2), 	$y - $textOffsetToObject - $objectRadius - $textBox[1], 		$white, $font, $text);

	if($line == true) {
		imageline($img, $x, $y - $objectRadius, $x , $y - $textOffsetToObject - $objectRadius + $textBoxPadding, $lightgrey);
	}
}

function drawCircle($img, $radius, $x, $y, $color)
{
	global $white, $darkgrey, $black;
	
	$x=floor($x);
	$y=floor($y);
	$cRatio = 4;
	$radius = floor($radius);
	
	if($radius > 1200)
		$cRatio = 2;	
	
	$imgArc = imagecreatetruecolor($radius*$cRatio+$cRatio, $radius*$cRatio+$cRatio);	
	$alphacolor = imagecolorallocatealpha($imgArc, 0, 0, 0, 127);
	imagesavealpha($imgArc, true);	
	imagefill($imgArc, 0, 0, $alphacolor);	
	imagesetthickness ( $imgArc , $cRatio );
	
	imagearc($imgArc, floor(floor($radius*$cRatio)/2)+floor($cRatio/2), floor(floor($radius*$cRatio)/2)+floor($cRatio/2), floor($radius*$cRatio), floor($radius*$cRatio), 0, 360, $color);
	
	imagecopyresampled($img, $imgArc, $x-floor($radius/2), $y-floor($radius/2), 0, 0, $radius, $radius, floor($radius*$cRatio+$cRatio), floor($radius*$cRatio+$cRatio));
	
	imagedestroy($imgArc);	
}

function getMinMaxRadius($radius)
{
	global $displayWidth, $displayHeight;

	if($radius < 11)
		$radius = 11;

	if($radius > $displayWidth)
		$radius =  $displayWidth;	

	if($radius > $displayHeight)
		$radius = $displayHeight;	
	
	return $radius;
}

?>