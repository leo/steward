<?php
	
	class Backup {
		
		public function create() {
			
			require_once ABSPATH . '/wp-admin/includes/file.php';
			
			function create_zip($files = array(),$destination = '',$overwrite = false) {
			
				if(file_exists($destination) && !$overwrite) { return false; }
			
				$valid_files = array();
			
				if(is_array($files)) {
				
					foreach($files as $file) {
						
						if(file_exists($file)) {
							$valid_files[] = $file;
						}
					}
				}
			
				if(count($valid_files)) {
					
					$zip = new ZipArchive();
					
					if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
						return false;
					}
				
					foreach($valid_files as $file) {
						$zip->addFile($file,$file);
					}
	
					$zip->close();
		
					return file_exists($destination);
					
				} else {
					return false;
				}
				
			}
			
			$wp_root = get_home_path();
			$wp_files = array();
			
			$fileinfos = new RecursiveIteratorIterator(
			    new RecursiveDirectoryIterator($wp_root)
			);
			
			foreach($fileinfos as $pathname => $fileinfo) {
				
			    if (!$fileinfo->isFile()) continue;
				array_push( $wp_files, $pathname);
				
			}
			
			$backup_root = $wp_root .'/wp-content/uploads/backups';
			
			
			if( file_exists( $backup_root ) == false ) {
				mkdir( $backup_root );
			}
			
		
			$backup_file = $backup_root .'/'. uniqid() .'.zip';
			$zip_backup = create_zip( $wp_files, $backup_file );
			
			
			$backup_data = json_encode(array(
				'backup' => array(
					'state' => true,
					'file' => str_replace( get_home_path(), get_site_url(), $backup_file )
				)
			), JSON_PRETTY_PRINT);;
			
			if( $zip_backup == 1 ) {
				return $backup_data;
			}
			
		}
		
		public function remove($backup_id) {
			
			$del_backup = unlink( get_home_path() .'/wp-content/uploads/backups/'. $backup_id .'.zip' );
			
			if ( $del_backup == true ) {
				$state = 'success';
			} else {
				$state = 'error';
			}
			
			return $state;
			
		}

	}
	
?>