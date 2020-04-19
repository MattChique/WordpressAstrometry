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

//require wp-load
require_once(explode('wp-content', $_SERVER['SCRIPT_FILENAME'])[0] . 'wp-load.php');

//require Annotator Class
require_once(__DIR__."\annotator.php");
require_once(dirname(__DIR__)."\astrometryData.php");

//Query
$hd = isset($_GET["showHdCatalogue"]) ? $_GET["showHdCatalogue"] : false;
$postId = $_GET["postid"];
$mediaID = $_GET["mediaid"];
$displayWidth = $_GET["w"];

//Read original image annotations
$data = new AstrometryData($postId, $mediaID);
$imageUrl = wp_get_attachment_image_src($_GET["mediaid"], 'original');

//Create Annotator and draw
$annotator = Annotator::Svg($imageUrl, $displayWidth, $data->Get("annotations")["annotations"]);
$annotator->SetFont("../assets/font/OpenSans-Regular.ttf",10);
$annotator->ShowHD($hd);
$annotator->Draw();

?>