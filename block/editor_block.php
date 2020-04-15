<?php

function astrometry_01_register_block() {
    wp_register_script(
        'astrometry',
        plugins_url('astrometryBlock.js', __FILE__),
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-data', 'wp-i18n' )
    );
 
    register_block_type( 'astrometry/photodata', array(
        'editor_script' => 'astrometry',
        'render_callback' => 'astrometry_render'
    ) );
    wp_set_script_translations( 'astrometry', 'astrometry', ASTROMETRY_PLUGIN_BASE . 'languages' );
}
add_action( 'init', 'astrometry_01_register_block' );

function astrometry_render($attributes, $content) {
    global $post;

    $postId = $post->ID;
    $mediaId = $attributes['mediaID'];

    $data = new AstrometryData($postId, $mediaId);

    if($mediaId > 0 && $data->Get("annotations") == null) {
        add_action( 'wp_enqueue_scripts', 'astrometry_ajax_script' );	
        $content=str_replace("{solvingState}","",$content);
    } else {
        $content=str_replace("{solvingState}","solved",$content);
    }

//print_r($attributes);

    $solvingDataUrl = plugins_url('annotation/annotations.php', dirname(__FILE__));
    $solvingDataUrl .= "?mediaid=". $mediaId . "&postid=" . $postId;
    if(isset($attributes["showHdCatalogue"])) $solvingDataUrl .= "&showHdCatalogue=true";

    $content=str_replace("{solvingData}", $solvingDataUrl, $content);    

    $submission = $data->Get("submission");
    $info = $data->Get("info");

    if($info != null) {

        $tags = array();
        foreach($info["machine_tags"] as $t)
        {
            $text = preg_replace("/u([0-9a-f]{2,4})/", "&#x\\1;", $t);
            array_push($tags, "<a href='/?s=".$text."'>".$text."</a>");
        }

        $content = str_replace("{OBJECTS}", join($tags,", "), $content);
        $content = str_replace("{RA}", $info["calibration"]["ra"], $content);
        $content = str_replace("{DEC}", $info["calibration"]["dec"], $content);        
        $content = str_replace("{JOB}", "<a href='http://nova.astrometry.net/status/".$data->Get("subid")."' target='_blank'>".$data->Get("subid")."</a>", $content);
        $content = str_replace("{SKYPLOT}", "<img src='//nova.astrometry.net/sky_plot/zoom1/" . $submission["job_calibrations"][0][1] . "'>", $content);

    } else {
		$content = str_replace("{OBJECTS}", "", $content);
        $content = str_replace("{RA}", "", $content);
        $content = str_replace("{DEC}", "", $content);
        $content = str_replace("{JOB}", "", $content);
        $content = str_replace("{SKYPLOT}", "", $content);
	}

    return $content;
}
?>