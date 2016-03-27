<?php

	class GetInfo {
		
		public function core() {
			
			require ABSPATH . '/wp-includes/version.php';
			require_once ABSPATH . '/wp-admin/includes/update.php';
		
			$update_data = get_core_updates();
		
			if( $wp_version < $update_data[0]->current ) {
				$wp_version_state = 'update';
			} else {
				$wp_version_state = 'latest';
			}
		
			$core_version = array(
				'id' => $wp_version,
				'state' => $wp_version_state
			);
		
			if( $core_version['state'] == 'update' ) {
				$core_version['url'] = $update_data[0]->download;
			}
		
			$core_data = array(
				'site_name' => get_bloginfo('name'),
				'site_url' => get_bloginfo( 'url' ),
				'wp_version' => $core_version,
				'wp_lang' => $wp_local_package,
				'tinymce_version' => $tinymce_version
			);
		
			return $core_data;
		
		}
		
		public function plugins() {

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
	
			$all_plugins = get_plugins();
		
			$plugin_data_not = array( 'Description', 'Network', 'DomainPath', 'Title', 'TextDomain', 'AuthorName' );
		
			foreach( $all_plugins as $plugin_file => $plugin_data ) {
			
			    if( is_array( $plugin_data ) ) {
				
					foreach( $plugin_data as $data_key => $data_value ) {
	
						if( in_array( $data_key, $plugin_data_not ) ) {
							unset( $all_plugins[$plugin_file][$data_key] );
						}
					
						if( is_plugin_active($plugin_file) == true ) {
							$plugin_state = 'on';
						} else {
							$plugin_state = 'off';
						}
					
						$all_plugins[$plugin_file]['State'] = $plugin_state;
					
					}
				
			    }
			
				unset( $all_plugins['steward/steward.php'] );
			
			}
		
			if( empty( $all_plugins ) ) {
				$all_plugins = 'no';
			}
		
			return $all_plugins;
		}
		
		public function themes() {
			
			if ( ! function_exists( 'wp_get_themes' ) ) {
				require_once ABSPATH . 'wp-includes/theme.php';
			}

			$all_themes = wp_get_themes();

			if( empty( $all_themes ) ) {
				$all_themes = 'no';
			}
	
			foreach ( $all_themes as $theme_slug => $theme_info_wrap ) {

				if( get_template() == $theme_slug ) {
					$theme_status = 'on';
				} else {
					$theme_status = 'off';
				}
	
				$theme_info = wp_get_theme( $theme_slug );
				$screenshot = str_replace( get_bloginfo( 'url' ), '', $theme_info->get_screenshot() );

				$all_themes[$theme_slug] = array(
					'Name' => $theme_info->get( 'Name' ),
					'ThemeURI' => $theme_info->get( 'ThemeURI' ),
					'Version' => $theme_info->get( 'Version' ),
					'Author' => $theme_info->get( 'Author' ),
					'AuthorURI' => $theme_info->get( 'AuthorURI' ),
					'State' => $theme_status
				);
				
				if( !empty( $screenshot ) ) {
					$all_themes[$theme_slug]['Preview'] = $screenshot;
				}
	
			}
		
			return $all_themes;
	
		}
		
	}
	
?>