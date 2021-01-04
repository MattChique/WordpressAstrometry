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

require_once(ASTROMETRY_PLUGIN_BASE . "annotation/astrometryData.class.php"); 
require_once(ASTROMETRY_PLUGIN_BASE . "annotation/coordinate.class.php"); 

//Global array astrometry images registered for this page
$astrometryImages = array();

//Register Astrometry Image Block
function astrometry_01_register_block() {

    //Register script
    wp_register_script(
        'astrometry',
        plugins_url('astrometryBlock.js', __FILE__),
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-data', 'wp-i18n' )
    );
 
    //Register block type
    register_block_type( 'astrometry/photodata', array(
        'editor_script' => 'astrometry',
        'render_callback' => 'astrometry_render'
    ) );

    //Register block translations
    wp_set_script_translations( 'astrometry', 'astrometry', ASTROMETRY_PLUGIN_BASE . 'languages' );
}
add_action( 'init', 'astrometry_01_register_block' );

//Render Astrometry Image Block
function astrometry_render($attributes, $content) {
    global $post;
    $postId = $post->ID;
    $mediaId = $attributes['mediaID'];
    $data = new AstrometryData($postId, $mediaId);
    
    //Photon/Jetpack queries images with width(w) param, we register it in an array to skip the images
    preg_match_all('/img[^>]*src=["]+((?!http:\/\/)\s*[^"]*)/i', $content, $matches, PREG_OFFSET_CAPTURE);
    foreach($matches[1] as $match)
        array_push($GLOBALS["astrometryImages"], $match[0]);
    
    //Set solving state of image
    if($mediaId > 0 && $data->Get("annotations") == null) {
        add_action( 'wp_enqueue_scripts', 'astrometry_ajax_script' );	
        $content=str_replace("{solvingState}","",$content);
    } else {
        $content=str_replace("{solvingState}","solved",$content);
    }

    //Set solving data of image
    $solvingDataUrl = plugins_url('annotation/annotations.php', dirname(__FILE__));
    $solvingDataUrl .= "?mediaid=". $mediaId . "&postid=" . $postId;
    if(isset($attributes["showHdCatalogue"]) && $attributes["showHdCatalogue"] == true) $solvingDataUrl .= "&showHdCatalogue=true";
    $content=str_replace("{solvingData}", $solvingDataUrl, $content);    

    //Build astrometry data, if turned on an image is solved
    $submission = $data->Get("submission");
    $info = $data->Get("info");

    $add = "";
    if(isset($attributes["showAstrometryMetaData"]) && $info != null)
    {
        $add .= '<label>' . __('RA', 'astrometry') . '</label><p class="col2"><abbr title="'.$info["calibration"]["ra"].'">' . Coord::DegToHms($info["calibration"]["ra"]) . '</abbr></p>';
        $add .= '<label class="col3">' . __('DEC', 'astrometry') . '</label><p class="col4"><abbr title="'.$info["calibration"]["dec"].'">' . Coord::DegToDms($info["calibration"]["dec"]) . '</abbr></p>';
        $add .= '<label>' . __('Fieldradius', 'astrometry') . '</label><p class="col2">' . Coord::DegToDms($info["calibration"]["radius"]) . '</p>';
        $add .= '<label class="col3">' . __('Pixelscale', 'astrometry') . '</label><p class="col4">' . round($info["calibration"]["pixscale"],4) . ' <span class="unit">px/arcsec</span></p>';
        $add .= '<label>' . __('Job', 'astrometry') . '</label><p><a href="http://nova.astrometry.net/status/'.$data->Get("subid").'" target="_blank">'.$data->Get("subid").'</a></p>';
        $add .= '<label>';
        if ( current_user_can('administrator') ) { $add .= '<a href="javascript:void(0);" class="refreshAstrometry refreshInfo" title="' . __('Reload object tags', 'astrometry') . '"></a>'; }
        $add .= __('Objects', 'astrometry') . '</label><p class="objects">' . join($data->GetTagLinks(),", ") . '</p>';

        $content = str_replace("{SKYPLOT}", "<img src='//nova.astrometry.net/sky_plot/zoom1/" . $submission["job_calibrations"][0][1] . "'>", $content);
    }
    else
    {        
        $content = str_replace("{SKYPLOT}", "", $content);
    }

    $content = str_replace("<p>{ASTROMETRYDATA}</p>", $add, $content);

    return $content;
}

//Prevent registered images from being loaded by jetpack/photon
function astrometry_photon_exception($val, $src, $tag) {
    if (in_array($src, $GLOBALS["astrometryImages"])) 
        return true;

    return $val;
}
add_filter('jetpack_photon_skip_image', 'astrometry_photon_exception', 10, 3);

?>