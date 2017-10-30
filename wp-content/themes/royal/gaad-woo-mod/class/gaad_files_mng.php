<?php 

chdir('/');
require_once( '/wp-content/themes/royal' . '/gaad-woo-mod/vendors/ftp-php/src/Ftp.php' );
require_once( '/wp-content/themes/royal' . '/gaad-woo-mod/class/gaad_db.php' );
require_once( '/wp-content/themes/royal' . '/gaad-woo-mod/class/gaad_ajax.php' );
//require_once( '/wp-includes/functions.php' );
//require_once( '/wp-includes/meta.php' );
//require_once( '/wp-content/plugins/woocommerce/includes/class-wc-cache-helper.php' );

class gaad_files_mng {
	
	private $ftp;
	public $status = -1;
	private $order_item_id = -1;
	
	
	
	
	/**
	* Ładuje plik do tmp
	*
	* @return void
	*/
	function upload_temp ( $file_name, $source = NULL ) {
		$source = is_null( $source ) ? $_FILES[ 0 ][ 'tmp_name' ] : $source;		
		$attachment_id = uniqid('-');
		$file_name = $this->order_item_id . $attachment_id . '-' . $file_name;
		
		//no src file, escape
		if( !is_file( $source ) ){
			echo "upload_temp: source is NULL";
			
			return;
		} else {
			
			$db = new gaad_db();		
			$tmp = $db->file_uploads[ 'paths' ][ 'temp' ] . '/' . $this->order_item_id;
			
			/*
			Czyszzcenie katalogu tymczasowego
			*/
			if( $this->ftp->isDir( $tmp ) ){
				//$this->emptyDir( $tmp );
			} else {				
				/*
				* Tworzenie nowego katalogu tymczasowego
				*/
				mkdir( $tmp , 0777, true );	
			}
			
			
			try {		
				if( move_uploaded_file( $source, $tmp . '/' . $file_name ) ){		
					chmod( $tmp . '/' . $file_name, 0777 );					
					return array_merge( $this->setup_uploaded_file( $tmp . '/' . $file_name ), array( 'id' => $this->order_item_id ) );					
				}
			} catch ( Exception $e) { echo 'Error: ', $e->getMessage(); }
			
		}
	}
	
	
	/**
	* Tworzy miniatury z roznych rodzajow plikow i zapisuje je w tej samej lokalizacji co plik zródło
	*
	* @return void
	*/
	static function new_thumbnail ( $tmp_file, $mime_type = null ) {
		$db = new gaad_db();
		$mime_type = is_null ($mime_type) ? gaad_files_mng::get_mime_type($tmp_file) : $mime_type;
		$thumb_max_width = $db->file_uploads[ 'thumbnails' ][ 'cart' ][ 'max_width' ];
		$thumb_max_height = $db->file_uploads[ 'thumbnails' ][ 'cart' ][ 'max_height' ];
		$thimg = dirname($tmp_file) .'/th_' .basename( $tmp_file ) ;
		switch( $mime_type ){
			case 'image-png' :
			case 'image-jpg' : 
			case 'image-jpeg' : 
				
				$imagick = new \Imagick( $tmp_file );
				$imagick->setbackgroundcolor('rgb(64, 64, 64)');				
				$imagick->thumbnailImage($thumb_max_width, $thumb_max_height, true, false);
				$imagick->writeImage( $thimg );
				$imagick->destroy();
				
				
				break;
			
			case 'application-pdf' : 
				$thimg = str_replace( '.pdf' , '.jpg', $thimg );
				$imagick = new \Imagick( $tmp_file . '[0]' );
				$imagick->setImageFormat('jpg');
				 
				$imagick->writeImage( $thimg );
				$imagick->destroy();
				
			break;
		}
	return $thimg;	
	}
	
    /**
	* 
	*
	* @return void
	*/
	static function get_mime_type ( $file ) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension					
		$mime_type = str_replace( array('/'), array('-'), finfo_file($finfo, $file) );
		finfo_close( $finfo );
		return $mime_type;
	}
	
	/**
	* 
	*
	* @return void
	*/
	function setup_uploaded_file ( $tmp_file ) {
		$r = array( 'thumbs' => array(), 'files' => array() );
		/*
		* Odczytywanie typu mime pliku tymczasowego
		*/
		$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension					
		$mime_type = str_replace( array('/'), array('-'), finfo_file($finfo, $tmp_file) );
		finfo_close( $finfo );
		
		switch( $mime_type ){
			/* default is image*/	
			default : 
				
				$r[ 'files' ][] = $tmp_file;
				$r[ 'thumbs' ][] = $this->new_thumbnail( $tmp_file, $mime_type );
				
				
				break;
		}
		return $r;
	}
	
	/**
	* 
	*
	* @return void
	*/
	function gaad_files_mng ( $order_item_id ) {		
		$this->order_item_id = !isset( $order_item_id ) ? $_GET['id'] : $order_item_id;
		/*
		tutaj sprawdzenie, czy taki item należy do zalogowanego usera, ale to potem
		*/
		$db = new gaad_db();
		
		try {
		
			$this->ftp = new Ftp;
			$this->status = 1;
			$this->ftp->connect($db->file_uploads[ 'ftp' ][ 'host' ]);
			$this->ftp->login($db->file_uploads[ 'ftp' ][ 'username' ], $db->file_uploads[ 'ftp' ][ 'password' ]);
			$this->status = 2;
		
		} catch (FtpException $e) {
			echo 'Error: ', $e->getMessage();
		}
	}

	
	/**
	* jednopoziomowe czyszczenie katalogu z plików
	*
	* @return void
	*/
	function emptyDir ( $dir ) {
		$ls = $this->ftp->nlist( $dir );
		$max = count( $ls );
		for( $i=0; $i<$max; $i++ ){
			$this->ftp->delete( $ls[ $i ] );
		}
		
		return count( $this->ftp->nlist( $dir ) ) == 0 ? true : false;		
	}
	
	
	
	

}


?>