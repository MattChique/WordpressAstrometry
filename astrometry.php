<?php
/*
Plugin Name: Astrometry
Description: Bietet einen Gutenberg-Block an, der Bilder anhand der astrometry.net API astrometrisiert
Plugin URI: https://www.explorespace.de/
Text Domain: astrometry
Domain Path: languages
Version: 0.7
Author: MatthiasG
Author URI: https://www.explorespace.de/
*/

defined('ABSPATH') or die( 'No script kiddies please!' );
define('ASTROMETRY_PLUGIN_BASE', plugin_dir_path(__FILE__));

//Includes
require_once(ASTROMETRY_PLUGIN_BASE . "astrometryData.php");    
require_once(ASTROMETRY_PLUGIN_BASE . "settings.php");
require_once(ASTROMETRY_PLUGIN_BASE . "block/editor_block.php");

//Einstellungen und Ergänzungen
$astrometry_settings_options = get_option( 'astrometry_settings' );
add_filter('jpeg_quality', function($arg) { return $astrometry_settings_options['image_quality']; } );

//Init
function init_astrometry() {
	global $wp_query;
    global $post;

    if(!isset($post))
        return;

    addAstrometryCss();

    wp_enqueue_script('astrometry-javascript', plugins_url('/assets/js/astrometry.js', __FILE__), array('jquery'), '', false);
    wp_localize_script('astrometry-javascript', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'postId' => $post->ID) );
}
add_filter('wp', 'init_astrometry');

//CSS
function addAstrometryCss() {
    wp_register_style('astrometry-css', plugins_url('/assets/css/astrometry.css', __FILE__) );
    wp_enqueue_style('astrometry-css');
}
function addAstrometryEditorCss() {
    wp_enqueue_style('astrometry-editor-css', plugins_url('/assets/css/astrometry.editor.css', __FILE__), false );
    addAstrometryCss();
}
add_action('enqueue_block_editor_assets', 'addAstrometryEditorCss');

//Callback for Ajax Solving
function astronomyImageAction_callback() {       
    $astrometryData = new AstrometryData($_POST['postId'], $_POST['mediaId']);
    echo $astrometryData->Solve(get_option('astrometry_settings')['api_key']);

    wp_die();
}
add_action('wp_ajax_astronomyImageAction', 'astronomyImageAction_callback');	

//Settings
if ( is_admin() ) {
    $astrometry_settings = new AstrometrySettings();
}
function addSettingsAssets() {
    if ( is_admin() ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
    }
}
add_action( 'admin_enqueue_scripts', 'addSettingsAssets');














































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