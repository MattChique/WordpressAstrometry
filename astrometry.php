<?php
/*
Plugin Name: Astrometry
Description: Bietet einen Gutenberg-Block an, der Bilder anhand der astrometry.net API astrometrisiert
Plugin URI: http://astromatt.morganslions.de/
Text Domain: astrometry
Domain Path: languages
Version: 0.5
Author: MatthiasG
Author URI: http://astromatt.morganslions.de/
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$astrometryPluginBase = plugin_basename(__FILE__);

include( plugin_dir_path( __FILE__ ) . 'settings.php');
include( plugin_dir_path( __FILE__ ) . 'solve.php');
include( plugin_dir_path( __FILE__ ) . 'block/editor_block.php');

//Einstellungen und Ergänzungen
add_filter('jpeg_quality', function($arg) { return 92; } );

//CSS
wp_register_style( 'astronomyCss', plugins_url( '/astrometry.css', __FILE__ ) );
wp_enqueue_style('astronomyCss');

//Init
function init_astrometry($bal) {
	global $wp_query;
	global $post;

	add_action( 'wp_enqueue_scripts', 'astrometry_javascript' );	

	if( get_post_meta($post->ID, "astrometry_annotations", true) == "" )
	{
		add_action( 'wp_enqueue_scripts', 'astrometry_solve_ajax_script' );		
	}	
}
add_filter('wp', 'init_astrometry');

function astrometry_solve_ajax_script() {
	global $post;

	wp_register_script( 'astrometry-solve-ajax-script', plugins_url( '/astrometrySolve.js', __FILE__ ) );
	wp_enqueue_script( 'astrometry-solve-ajax-script' );
	wp_localize_script( 'astrometry-solve-ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'postId' => $post->ID) );
}

function astrometry_javascript() {
	wp_enqueue_script('astrometry-javascript', plugins_url( '/astrometry.js', __FILE__ ), array('jquery'), '', false);
}

function astronomyImageAction_callback() {
	echo astrometrySolve($_POST['postId'], $_POST['mediaId']);
	wp_die(); 
}
add_action( 'wp_ajax_astronomyImageAction', 'astronomyImageAction_callback' );	

if ( is_admin() )
	$astrometry_settings = new AstrometrySettings();















































function cf_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {    
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
add_filter('posts_join', 'cf_search_join' );

function cf_search_where( $where ) {
    global $pagenow, $wpdb;

    if ( is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

    return $where;
}
add_filter( 'posts_where', 'cf_search_where' );

function cf_search_distinct( $where ) {
    global $wpdb;

    if ( is_search() ) {
        return "DISTINCT";
    }

    return $where;
}
add_filter( 'posts_distinct', 'cf_search_distinct' );

?>