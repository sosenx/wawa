<?php 

require_once( '/wp-content/themes/royal/' . '/gaad-woo-mod/class/gaad_files_mng.php' );


$order_item_id = $_GET['id'];
$temp_file_path = $_FILES[0]["tmp_name"];
$file_name = str_replace( array(' '), array('-'), $_FILES[0]["name"] );

     
if( $_FILES[0]["error"] == 0 ){
	$fm = new gaad_files_mng( $order_item_id );
	$files = $fm->upload_temp( $file_name, $temp_file_path );
	
	$uploaded_file_mime_type = gaad_files_mng::get_mime_type( $files[ 'files' ][0] );
	if( $uploaded_file_mime_type === 'application-zip' ){
		
		$zip = new ZipArchive; 
		
		$files_table = array();
		if (@$zip->open( $files[ 'files' ][0]  ) == TRUE) {
			for ($i = 0; $i < @$zip->numFiles; $i++) {
				$files_table[] = @$zip->getNameIndex($i);
			}
		}
		
		if( count($files_table) > (int)$_GET[ 'mf' ] ){
			echo json_encode( array( 
				'id' => $_GET['id'], 
				'attachment_status' => 151 
			) );
			die();
		}
		
		
		$zip->extractTo( dirname( $files[ 'files' ][0] ) ); 
		$zip->close();
		
		/*
		*Kasowanie archiwum po wypakowaniu plików, nie jest juz potrzebne, zajmuje tylko miejsce na dysku
		*/
		unlink( $files[ 'files' ][0] );
		
		$max = count( $files_table );
		for( $i = 0; $i < $max; $i++){		
			
			$file_name = str_replace( array(' '), array('-'), $files_table[ $i ] );			
			$new_file_name = $order_item_id . uniqid('-'). '-' . str_replace( array(' '), array('-'), $files_table[ $i ] );			
			
			$temp_file_path = dirname( $files[ 'files' ][0] ) . '/' . $files_table[ $i ];
			
			rename( $temp_file_path, dirname($temp_file_path) . '/' . $new_file_name );
			
			$temp_file_path = dirname($temp_file_path) . '/' . $new_file_name;
			
			gaad_files_mng::new_thumbnail($temp_file_path);
			
			$files_table[ $i ] = $temp_file_path;
		}
		
		$files = array_merge( $files, array( 'files' => $files_table ) );
		$files['zip'] = true;
	}
	
	
	
	echo json_encode( $files );
	die();
}

