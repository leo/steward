	


<?php

/*
 * Plugin Name: Steward
 * Plugin URI: http://steward.im/?ref=plugin
 * Description: This plugin allows you to control all your sites from one place.
 * Version: 0.0.1
 * Author: Leonard
 * Author URI: http://leo.im
 * License: GPL2
 */


add_action('init', 'ap_action_init');

function ap_action_init() {
	load_plugin_textdomain('steward', false, basename( dirname( __FILE__ ) ) . '/lang' );
}


register_activation_hook( __FILE__, 'steward_on' );

function steward_on( $type ) {
	
	if( get_option('steward_key') == false ) {
		
		function gen_auth_key() {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $auth_key = '';

		    for ($i = 0; $i < 20; $i++) {
		        $auth_key .= $characters[mt_rand(0, strlen($characters) - 1)];
		    }

		    return $auth_key;
		}
	
		$auth_key = gen_auth_key();
		$pass = md5( $auth_key );

		add_option( 'steward_key', $pass );
		setcookie( 'steward_key', $auth_key );
		
	}
	
	if( get_option('steward_opt_edit') == false ) {
		add_option('steward_opt_edit', 1);
	}
	
	if( get_option('steward_opt_deactivate') == false ) {
		add_option('steward_opt_deactivate', 1);
	}
	
	add_option('steward_notification', 0);

}


register_deactivation_hook( __FILE__, 'steward_off' );

function steward_off() {
	delete_option('steward_key');
	delete_option('steward_notification');
}


add_action( 'admin_init', 'admin_initializion' );

function admin_initializion() {
	
	if ( isset( $_GET['activate'] ) && is_plugin_active( 'steward/steward.php' ) ) {
		
		function admin_scripts($hook) {
		    if( 'plugins.php' != $hook ) {
		    	return;
		    }
	
			wp_enqueue_script( 'core', plugin_dir_url( __FILE__ ) . 'assets/admin.js' );
			wp_enqueue_style( 'main', plugin_dir_url( __FILE__ ) . 'assets/admin.css' );
		}

		add_action( 'admin_enqueue_scripts', 'admin_scripts' );
		
		if( get_option( 'steward_notification' ) == 0 ) {
			
			function admin_notice() {
				echo '<div class="updated">';
				echo '<h3>'. __( 'Steward is now activated!', 'steward' ) .'</h3>';
				echo '<p>'. __( 'Use the following key to add this wordpress-installation to your account', 'steward' ) .':';
				echo '<b class="steward-key" contenteditable="true" autocapitalize="off" autocorrect="off" autocomplete="off" spellcheck="false">'. $_COOKIE['steward_key'] .'</b></p>';
				echo '</div>';
			}
		
			update_option('steward_notification', 1);
			add_action( 'admin_notices', 'admin_notice' );
		
			unset( $_GET['activate'] );
		
		}
	}
	
}


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'disable_plugin_options', 10, 4 );

function disable_plugin_options( $actions, $plugin_file, $plugin_data, $context ) {
	
	$config = array(
		'edit' => get_option('steward_opt_edit'),
		'deactivate' => get_option('steward_opt_deactivate')
	);
	
	if( $config['edit'] == 0 || $config['deactivate'] == 0) {

		if( $config['edit'] == 0 && $config['deactivate'] == 0 ) {

			$actions['disabled'] = __( 'Settings are disabled.', 'steward' );
			unset( $actions['edit'], $actions['deactivate'] );

		} else {
	
			foreach ($config as $option => $state) {
				if ( $state == 0 ) {
					unset( $actions[$option] );
				}
			}

		}
	
	}

	return $actions;

}


?>