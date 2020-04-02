<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );


if(isset($_GET["w"])){
	$displayWidth = $_GET["w"];
} else {
	$displayWidth = 1120;
}

header("Content-type: image/png");	

//Init
$jsonAnnotations = json_decode(get_post_meta($_GET["postid"], "astrometry_annotations", true));
$imageUrl = wp_get_attachment_image_src($_GET["mediaid"], 'original');
$imageSize = getimagesize($imageUrl[0]);
$ratio = $displayWidth / $imageSize[0];

//Image bauen
$img = imagecreatetruecolor($displayWidth, $imageSize[1]*$ratio);
imagesavealpha($img, true);

$color = imagecolorallocatealpha($img, 0, 0, 0, 127);
imagefill($img, 0, 0, $color);
imageantialias($img, true);

$white = imagecolorallocate($img, 254, 254, 254);	
$darkgrey = imagecolorallocate($img, 100, 100, 100);	
$lightgrey = imagecolorallocate($img, 170, 170, 170);	
$black = imagecolorallocate($img, 0, 0, 0);	
$font = dirname(__FILE__) . '\assets\font\OpenSans-Regular.ttf';
$fontsize = 10;

//Ellipsen zeichnen
foreach($jsonAnnotations->annotations as $a)
{
	if($a->type == "hd")
		continue;

	$text = mb_convert_encoding($a->names[0], "HTML-ENTITIES", "UTF-8");
	$text = preg_replace("/\b^u([0-9a-f]{2,4})\b/", "&#x\\1;", $text);

	if($a->radius < 11)
		$radius = 11;
	else
		$radius = $a->radius;

	ImageEllipse($img, $a->pixelx*$ratio+1, $a->pixely*$ratio+1, $radius*$ratio*2, $radius*$ratio*2, $darkgrey);
	
	ImageEllipse($img, $a->pixelx*$ratio, $a->pixely*$ratio, $radius*$ratio*2, $radius*$ratio*2, $white);
}

//Bezeichnungen zeichnen
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

	if($a->radius < 10)
		$radius = 10;
	else
		$radius = $a->radius;

	if($a->type == "ngc")
	{
		$textsize = imageftbbox( $fontsize, 0, $font, $text);

		if($a->pixely - ($radius/2) - 10 + 1 > 0)
		{
			imagefttext($img,$fontsize, 0, $a->pixelx*$ratio - ($textsize[2] / 2) + 1, $a->pixely*$ratio - ($radius*$ratio*2/2) - 10 + 1, $darkgrey, $font, $text);
			imagefttext($img,$fontsize, 0, $a->pixelx*$ratio - ($textsize[2] / 2), $a->pixely*$ratio - ($radius*$ratio*2/2) - 10, $white, $font, $text);
			
			imageline($img,$a->pixelx*$ratio, $a->pixely*$ratio - ($radius*$ratio*2/2), $a->pixelx*$ratio , $a->pixely*$ratio - ($radius*$ratio*2/2) - 7, $white);
		}
		else	
		{
			imagefttext($img,$fontsize, 0, $a->pixelx*$ratio - ($textsize[2] / 2) + 1, $a->pixely*$ratio + ($radius*$ratio*2/2) + 25 + 1, $darkgrey, $font, $text);
			imagefttext($img,$fontsize, 0, $a->pixelx*$ratio - ($textsize[2] / 2), $a->pixely*$ratio + ($radius*$ratio*2/2) + 25, $white, $font, $text);
			
			imageline($img,$a->pixelx*$ratio, $a->pixely*$ratio + ($radius*$ratio*2/2), $a->pixelx*$ratio , $a->pixely*$ratio + ($radius*$ratio*2/2) + 7, $white);
		}

		
	}
	else
	{
		imagettftext($img, $fontsize, 0, $a->pixelx*$ratio + 10 + 1, $a->pixely*$ratio + 1, $darkgrey, $font, $text);
		imagettftext($img, $fontsize, 0, $a->pixelx*$ratio + 10, $a->pixely*$ratio, $white, $font, $text);
	}
}

imagepng($img); 
imagedestroy($img);

?>