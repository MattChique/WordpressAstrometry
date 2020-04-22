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
require_once("annotator.class.php");

//Query
$hd = isset($_GET["showHdCatalogue"]) ? $_GET["showHdCatalogue"] : false;
$postId = $_GET["postid"];
$mediaID = $_GET["mediaid"];
$displayWidth = $_GET["w"];

//Read original image annotations
$settings = get_option('astrometry_settings');
$data = new AstrometryData($postId, $mediaID);
$imageUrl = wp_get_attachment_image_src($_GET["mediaid"], 'original');
$annotations = $data->Get("annotations")["annotations"];

//Check for Alternate Catalogues
if(isset($settings["additionalCatalogues"]))
    $annotations = AlternateCatalogues::CheckAlternate($annotations);            

//Create Annotator and draw
$annotator = Annotator::Svg($imageUrl, $displayWidth, $annotations);
$annotator->SetFont("../assets/font/OpenSans-Regular.ttf",10);
$annotator->ShowHD($hd);

//Use grid, if checked
if(isset($settings["celestialCoordinateGrid"]))
    $annotator->SetGrid(new CelestialGrid($data->Get("info")["calibration"]));

//Draw SVG
$annotator->Draw();

?>