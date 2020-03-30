<?
class AstrometrySettings {
    private $astrometry_settings_options;
    
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'astrometry_settings_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'astrometry_settings_page_init' ) );
        add_filter( 'plugin_action_links', array($this, 'my_add_action_links'), 10, 5 );
	}

	public function astrometry_settings_add_plugin_page() {
		add_plugins_page(
			'Astrometry Settings', 
			'Astrometry Settings', 
			'manage_options',
			'astrometry-settings', 
			array( $this, 'astrometry_settings_create_admin_page' )
        );
    }

    public function my_add_action_links( $links, $plugin_file ) {
        if($plugin_file != ASTROMETRY_PLUGIN_BASE)
            return $links;

        return array_merge(
        array(
            'settings' => '<a href="' . admin_url( 'plugins.php?page=astrometry-settings' ) . '">' . __( 'Settings', 'astrometry-settings' ) . '</a>'
        ),
        $links
        );
    }

	public function astrometry_settings_create_admin_page() {
		$this->astrometry_settings_options = get_option( 'astrometry_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Astrometry Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'astrometry_settings_option_group' );
					do_settings_sections( 'astrometry-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function astrometry_settings_page_init() {
		register_setting(
			'astrometry_settings_option_group',
			'astrometry_settings_option_name', 
			array( $this, 'astrometry_settings_sanitize' )
		);

		add_settings_section(
			'astrometry_settings_setting_section',
			'Settings', 
			array( $this, 'astrometry_settings_section_info' ),
			'astrometry-settings-admin' 
		);

		add_settings_field(
            'api_key', 
            'API-KEY', 
			array( $this, 'api_key_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_setting_section'
		);

		add_settings_field(
            'image_quality', 
            'Bild-Qualität', 
			array( $this, 'image_quality_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_setting_section'
		);
	}

	public function astrometry_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['api_key'] ) ) {
			$sanitary_values['api_key'] = sanitize_text_field( $input['api_key'] );
		}
		if ( isset( $input['image_quality'] ) ) {
			$sanitary_values['image_quality'] = sanitize_text_field( $input['image_quality'] );
		}

		return $sanitary_values;
	}

	public function astrometry_settings_section_info() {
		
	}

	public function api_key_callback() {
		printf(
			'<input class="regular-text" type="text" name="astrometry_settings_option_name[api_key]" id="api_key" value="%s">',
			isset( $this->astrometry_settings_options['api_key'] ) ? esc_attr( $this->astrometry_settings_options['api_key']) : ''
        );
        echo "<br><a href='http://nova.astrometry.net' target='_blank'>Get an API Key</a>";
	}

	public function image_quality_callback() {
		printf(
			'<input type="number" name="astrometry_settings_option_name[image_quality]" id="image_quality" value="%s">',
			isset( $this->astrometry_settings_options['image_quality'] ) ? esc_attr( $this->astrometry_settings_options['image_quality']) : '82'
        );
        echo "<br>Bildqualität 1-100. Überschreibt die Wordpress Grundeinstellung (82) global.";
	}
}