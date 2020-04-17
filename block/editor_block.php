<?php

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

//print_r($attributes);


    $postId = $post->ID;
    $mediaId = $attributes['mediaID'];
    $data = new AstrometryData($postId, $mediaId);

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
        $tags = array();
        foreach($info["machine_tags"] as $t)
        {
            $text = preg_replace("/u([0-9a-f]{2,4})/", "&#x\\1;", $t);
            array_push($tags, "<a href='/?s=".$text."'>".$text."</a>");
        }        
        
        $add .= '<label>' . __('RA') . '</label><p class="col2">' . AstrometryData::RA($info["calibration"]["ra"]) . '</p>';
        $add .= '<label class="col3">' . __('DEC') . '</label><p class="col4">' . AstrometryData::DEC($info["calibration"]["dec"]) . '</p>';
        $add .= '<label>' . __('Fieldradius') . '</label><p class="col2">' . AstrometryData::DEC($info["calibration"]["radius"]) . '</p>';
        $add .= '<label class="col3">' . __('Pixelscale') . '</label><p class="col4">' . round($info["calibration"]["pixscale"],4) . '</p>';
        $add .= '<label>' . __('Job') . '</label><p><a href="http://nova.astrometry.net/status/'.$data->Get("subid").'" target="_blank">'.$data->Get("subid").'</a></p>';
        $add .= '<label>' . __('Objects') . '</label><p class="objects">' . join($tags,", ") . '</p>';

        $content = str_replace("{SKYPLOT}", "<img src='//nova.astrometry.net/sky_plot/zoom1/" . $submission["job_calibrations"][0][1] . "'>", $content);
    }
    else
    {        
        $content = str_replace("{SKYPLOT}", "", $content);
    }

    $content = str_replace("<p>{ASTROMETRYDATA}</p>", $add, $content);

    return $content;
}
?>