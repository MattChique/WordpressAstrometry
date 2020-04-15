<?php
//require wp-load
require_once(explode('wp-content', $_SERVER['SCRIPT_FILENAME'])[0] . 'wp-load.php');

//require Annotator Class
require_once(__DIR__."\annotator.php");
require_once(dirname(__DIR__)."\astrometryData.php");

//Query
if(isset($_GET["showHdCatalogue"]))
    $hd = $_GET["showHdCatalogue"];
else
    $hd = false;

$postId = $_GET["postid"];
$mediaID = $_GET["mediaid"];
$displayWidth = $_GET["w"];

//Read original image annotations
$data = new AstrometryData($postId, $mediaID);
$imageUrl = wp_get_attachment_image_src($_GET["mediaid"], 'original');

//Create Annotator and draw
$annotator = Annotator::Png($imageUrl, $displayWidth, $data->Get("annotations")["annotations"]);
$annotator->SetFont("../assets/font/OpenSans-Regular.ttf",10);
$annotator->ShowHD($hd);
$annotator->Draw();

?>