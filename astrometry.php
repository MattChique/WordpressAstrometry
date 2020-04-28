<?php
/*
Plugin Name: Astrometry
Description: Bietet einen Gutenberg-Block an, der Bilder anhand der astrometry.net API astrometrisiert
Plugin URI: https://www.explorespace.de/
Text Domain: astrometry
Domain Path: languages
Version: 0.8.0
Author: Matthias Guttmann
Author URI: https://www.explorespace.de/
*/

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

defined('ABSPATH') or die( 'No script kiddies please!' );
define('ASTROMETRY_PLUGIN_BASE', plugin_dir_path(__FILE__));

//Includes
require_once(ASTROMETRY_PLUGIN_BASE . "astrometrySearch.php");    
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

    //CSS
    addAstrometryCss();

    //Scripts
    wp_enqueue_script('astrometry-javascript', plugins_url('/assets/js/astrometry.js', __FILE__), array('jquery'), '', false);
    wp_localize_script('astrometry-javascript', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'postId' => $post->ID) );

    //Localisation
    load_plugin_textdomain( 'astrometry', false, dirname(plugin_basename(__FILE__)) . '/languages' );
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
  
    require_once(ASTROMETRY_PLUGIN_BASE . "annotation/astrometryData.class.php"); 

    $astrometryData = new AstrometryData($_POST['postId'], $_POST['mediaId']);
    echo $astrometryData->Solve(get_option('astrometry_settings')['api_key']);

    wp_die();
}
add_action('wp_ajax_astronomyImageAction', 'astronomyImageAction_callback');	

//Settings
if ( is_admin() ) {
    $astrometry_settings = new AstrometrySettings();

    //Localisation
    load_plugin_textdomain( 'astrometry', false, dirname(plugin_basename(__FILE__)) . '/languages' );
}
function addSettingsAssets() {
    if ( is_admin() ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
    }
}
add_action( 'admin_enqueue_scripts', 'addSettingsAssets');

?>