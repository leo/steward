<?php
	
	require_once('../../../wp-load.php');
	
	$key_db = get_option('steward_key');
	
	$allowed_requests = array(
		'stats', 'backup'
	);
	
	if( !empty($_GET['key']) && !empty($_GET['mode']) && in_array($_GET['mode'], $allowed_requests) ) {
		
		if ( md5($_GET['key']) == $key_db ) {
			
			if( $key_db !== false ) {
		
				header('Content-type: application/json');
				echo call_user_func( 'steward_'. $_GET['mode'] );
		
			} else { die( 'Steward is not activated.' ); }
			
		} else { die( 'False API-key.' ); }
		
	} else { die( 'Unallowed request.' ); }
	
	
	function steward_stats() {
		
		require( 'lib/class.info.php' );
		
		global $key_db;
		
		$get_info = new GetInfo();
	
		$data = json_encode(array(
			
			'core' => $get_info->core(),
			'plugins' => $get_info->plugins(),
			'themes' => $get_info->themes()
				
		), JSON_PRETTY_PRINT);
		
		/* $encrypted_data = base64_encode(
			mcrypt_encrypt(
				MCRYPT_RIJNDAEL_256, md5( $key_db ), $data, MCRYPT_MODE_CBC, md5( md5( $key_db ) )
			)
		);
		
		return $encrypted_data; */
		
		return $data;

	}
	
	function steward_backup() {
		
		$type = $_GET['type'];
		
		if( !empty( $type ) || $type == 'create' || $type == 'remove' ) {
			
			require( 'lib/class.backup.php' );
			$backup = new Backup();
			return $backup->$type();
			
		} else {
			die('Define a / another type!');
		}
		
	}
	
?>