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

class AstrometrySettings {
    private $astrometry_settings_options;
    
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'astrometry_settings_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'astrometry_settings_page_init' ) );
		add_filter( 'plugin_action_links', array($this, 'my_add_action_links'), 10, 5 );
	}

	public function astrometry_settings_add_plugin_page() {
		add_plugins_page(
			__('Astrometry Settings'), 
			__('Astrometry Settings'), 
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
		$this->astrometry_settings_options = get_option( 'astrometry_settings' ); ?>

		<div class="wrap">
			<h2><?= __('Astrometry Settings') ?></h2>
			<p></p>
			<?php settings_errors(); ?>

			<script>
				jQuery(document).ready(function($){
					jQuery('.color-picker').iris({
						defaultColor: true,
						change: function(event, ui){},
						clear: function() {},
						hide: true,
						palettes: true
					});
				});
			</script>

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
			'astrometry_settings', 
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
            __('API-KEY'), 
			array( $this, 'api_key_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_setting_section'
		);

		add_settings_field(
            'image_quality', 
            __('Image Quality'), 
			array( $this, 'image_quality_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_setting_section'
		);

		add_settings_section(
			'astrometry_settings_annotation_section',
			__('Annotation'),
			array( $this, 'astrometry_settings_section_info' ),
			'astrometry-settings-admin' 
		);

		add_settings_field(
            'color_ngc', 
            __('NGC catalogue color'), 
			array( $this, 'ngc_color_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_annotation_section'
		);

		add_settings_field(
            'color_ic', 
            __('IC catalogue color'), 
			array( $this, 'ic_color_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_annotation_section'
		);

		add_settings_field(
            'color_bright', 
            __('Bright stars color'), 
			array( $this, 'bright_color_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_annotation_section'
		);

		add_settings_field(
            'color_hd', 
            __('HD catalogue color'), 
			array( $this, 'hd_color_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_annotation_section'
		);

		add_settings_field(
            'annotation_css', 
            __('Additional CSS'), 
			array( $this, 'annotation_css_callback' ),
			'astrometry-settings-admin',
			'astrometry_settings_annotation_section'
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
		if ( isset( $input['color_ngc'] ) ) {
			$sanitary_values['color_ngc'] = sanitize_text_field( $input['color_ngc'] );
		}
		if ( isset( $input['color_ic'] ) ) {
			$sanitary_values['color_ic'] = sanitize_text_field( $input['color_ic'] );
		}
		if ( isset( $input['color_bright'] ) ) {
			$sanitary_values['color_bright'] = sanitize_text_field( $input['color_bright'] );
		}
		if ( isset( $input['color_hd'] ) ) {
			$sanitary_values['color_hd'] = sanitize_text_field( $input['color_hd'] );
		}
		if ( isset( $input['annotation_css'] ) ) {
			$sanitary_values['annotation_css'] = sanitize_text_field( $input['annotation_css'] );
		}
		return $sanitary_values;
	}

	public function astrometry_settings_section_info() {
		
	}

	public function api_key_callback() {
		printf(
			'<input class="regular-text" type="text" name="astrometry_settings[api_key]" id="api_key" value="%s">',
			isset( $this->astrometry_settings_options['api_key'] ) ? esc_attr( $this->astrometry_settings_options['api_key']) : ''
        );
        echo "<br><a href='http://nova.astrometry.net' target='_blank'>" . __('Get an API Key') . "</a>";
	}

	public function image_quality_callback() {
		printf(
			'<input type="number" name="astrometry_settings[image_quality]" id="image_quality" value="%s">',
			isset( $this->astrometry_settings_options['image_quality'] ) ? esc_attr( $this->astrometry_settings_options['image_quality']) : '82'
        );
        echo "<br>" . __('Imagequality 1-100. Overrides the global Wordpress setting (82).');
	}

	public function ngc_color_callback() {
		printf(
			'<input class="color-picker" type="text" data-default-color="#cc0000" name="astrometry_settings[color_ngc]" id="color_ngc" value="%s">',
			isset( $this->astrometry_settings_options['color_ngc'] ) ? esc_attr( $this->astrometry_settings_options['color_ngc']) : '#cc0000'
        );
        echo "<br>" . __('Color for NGC catalogue annotation');
	}

	public function ic_color_callback() {
		printf(
			'<input class="color-picker" type="text" data-default-color="#6699ff" name="astrometry_settings[color_ic]" id="color_ic" value="%s">',
			isset( $this->astrometry_settings_options['color_ic'] ) ? esc_attr( $this->astrometry_settings_options['color_ic']) : '#6699ff'
        );
        echo "<br>" . __('Color for IC catalogue annotation');
	}

	public function bright_color_callback() {
		printf(
			'<input class="color-picker" type="text" data-default-color="#CCC" name="astrometry_settings[color_bright]" id="color_bright" value="%s">',
			isset( $this->astrometry_settings_options['color_bright'] ) ? esc_attr( $this->astrometry_settings_options['color_bright']) : '#CCC'
        );
        echo "<br>" . __('Color for bright stars (named stars) annotation');
	}

	public function hd_color_callback() {
		printf(
			'<input class="color-picker" type="text" data-default-color="#CCC" name="astrometry_settings[color_hd]" id="color_hd" value="%s">',
			isset( $this->astrometry_settings_options['color_hd'] ) ? esc_attr( $this->astrometry_settings_options['color_hd']) : '#CCC'
        );
        echo "<br>" . __('Color for HD annotation');
	}

	public function annotation_css_callback() {
		printf(
			'<textarea name="astrometry_settings[annotation_css]" id="annotation_css" class="regular-text">%s</textarea>',
			isset( $this->astrometry_settings_options['annotation_css'] ) ? esc_attr( $this->astrometry_settings_options['annotation_css']) : ''
        );
        echo "<br>" . __('Additional CSS for SVG annotation styling');
	}
}