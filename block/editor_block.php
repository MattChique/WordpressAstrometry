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

    if($attributes["mediaID"] > 0 && get_post_meta($post->ID, "astrometry_annotations", true) == "") {
        add_action( 'wp_enqueue_scripts', 'astrometry_ajax_script' );	
        $content=str_replace("{solvingState}","",$content);
    } else {
        $content=str_replace("{solvingState}","solved",$content);
    }
	$content=str_replace("{solvingData}", plugins_url('annotationImage.php', dirname(__FILE__)) . "?mediaid=". $attributes["mediaID"] . "&postid=" . $post->ID,$content);
    
    $jsonInfo = json_decode(get_post_meta($post->ID, "astrometry_info", true));    
    if($jsonInfo != "") {
        $tags = array();
        foreach($jsonInfo->machine_tags as $t)
        {
            $text = preg_replace("/u([0-9a-f]{2,4})/", "&#x\\1;", $t);
            array_push($tags, "<a href='/?s=".$text."'>".$text."</a>");
        }
		$job = get_post_meta($post->ID, "astrometry_subid", true);
		
        $content = str_replace("{OBJECTS}", join($tags,", "), $content);
        $content = str_replace("{RA}", $jsonInfo->calibration->ra, $content);
        $content = str_replace("{DEC}", $jsonInfo->calibration->dec, $content);        
        $content = str_replace("{JOB}", "<a href='http://nova.astrometry.net/status/".$job."' target='_blank'>".$job."</a>", $content);
    } else {
		$content = str_replace("{OBJECTS}", "", $content);
        $content = str_replace("{RA}", "", $content);
        $content = str_replace("{DEC}", "", $content);
        $content = str_replace("{JOB}", "", $content);
	}

	$jsonCalibration = json_decode(get_post_meta($post->ID, "astrometry_jobcalibrations", true));
	if($jsonCalibration != "") {
		$jsonCalibration = json_decode(get_post_meta($post->ID, "astrometry_jobcalibrations", true));
		$content = str_replace("{SKYPLOT}", "<img src='http://nova.astrometry.net/sky_plot/zoom1/" . $jsonCalibration[0][1] . "'>", $content);
	} else {
		$content = str_replace("{SKYPLOT}", "", $content);
    }
    
    return $content;
}
?>