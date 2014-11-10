<?php

class Upload {
  private $fileInfo;
  private $fileLocation;
  private $error;
  private $direct;
  
	/* Class Construct */
	function __construct( $dir ){
	  $location = Func::cleanDirPath( $dir );
	  if( !is_dir( $location ) ){
		  die('Supplied directory is not valid: ['.$location.']');	
	  }
	}
	//$msg, $success = 1,  $redirect = false, $remove = false, $json = true
	/* Upload File */
	static function upload(){
	  $theDir = ( !empty( $_POST['theDir'] ) && is_dir( Func::cleanDirPath( $_POST['theDir'] ) ) ? Func::cleanDirPath($_POST['theDir']) : DEFAULT_GAL_DIR );
	  $theFile = $_FILES['upFile'];
	  $location = $theDir .'/'. Func::SanitizeFilename( $theFile['name'], true );

	    if( $theFile['size'] > MAX_UPLOAD_SIZE * 1024 ) {
	      Func::returnMsg( 'The file exceeds the maximum file size allowed ['.MAX_UPLOAD_SIZE.'k]', 0, 1 );
		  return false;
	    } else {
	      if( !file_exists( $location ) ){
		    if( move_uploaded_file( $theFile['tmp_name'], $location ) )
			{
			  $resizeMsg = '';
			  $temp_size = getimagesize( $location );
			  if( isset( $_POST['doResize'] ) && $temp_size['1'] > 900 ||  $temp_size['2'] > 900) // Attempt to resize
			  {
				if( Gallery::resizeToFile( $location, 900, 900, $location, false ) ) { $resizeMsg = '<br />[ File was resized ] '; } else { $resizeMsg = '<br />[ File could not be resized ] '; }
			  }
			  Func::returnMsg( "File was successfully uploaded.<br />
								Location: $location
								$resizeMsg", 1, 1 );
		    } else {
			error_reporting(E_ALL | E_STRICT);
			  Func::returnMsg( 'File could not be uploaded.', 0 , 1 );
			  exit(1);
		    }
	      } else {
		    Func::returnMsg( 'File by this name already exists', 0 , 1 );	
	      }
	    }
	}

	/* Overwrite File */
	function overwrite( $theFile ){
	  $this->fileInfo = $theFile;
	  $this->fileLocation = $this->direct .'/'. $this->fileInfo['name'];
	  if( file_exists( $this->fileLocation ) ){
		  $this->delete( $this->fileInfo['name'] );
	  }
	  return $this->upload( $this->fileInfo );
	}
	
	/* Delete Files Array */
	function deleteArray( $delThumb = false ){
	  $aFileName =  @$_POST['FileToDelete'];
	  if( is_array( $aFileName ) ) {
	  $msg = '';
	    foreach ( $aFileName as $k => $v ) {
		  $msg .= $this->delete( $v );
			if( $delThumb === true && is_file( $this->direct .'/'. THUMB_PREF.$v ) ){
			  $msg .= $this->delete( THUMB_PREF.$v );
			}
		}
		unset( $v );
	  return $msg;
	  } else {
		Func::returnMsg( 'Not a valid list of files', 0 );
	  }
	}
	  /***************/
	 /* Delete File */
	/***************/
	function delete(){
	$theFile = ( !empty( $_POST['theFile'] ) ? $_POST['theFile'] : null );
	$theDir  = ( !empty( $_POST['theDir']  ) ? $_POST['theDir']  : null );

	if( file_exists ( $theDir .'/'. $theFile ) ) $fileLocation = $theDir .'/'. $theFile;
	if( file_exists (  $theFile ) ) 			 $fileLocation = $theFile;
	
	  if( is_file( $fileLocation ) )
	  {
	    if( @unlink( $fileLocation ) ) 
		{
		  Func::returnMsg( "File [$theFile] was deleted from [$theDir]", 1, 0, Func::SanitizeFilename($fileLocation) );
		} else {
		  Func::returnMsg( "Could not delete [$theFile]", 0 );
		}
	  } elseif ( is_dir( $fileLocation ) ) {
	    if( @rmdir( $fileLocation ) )
		{
		  Func::returnMsg( "Directory [$fileLocation] was deleted.",1, 0, Func::SanitizeFilename( $fileLocation ) );
		} else {
		Func::returnMsg( "Could not delete [$fileLocation] make sure that the folder is empty.", 0 );
		}
	  } else {
		Func::returnMsg( "This file or directory does not exist: $fileLocation.", 0 );	
	  }
	}

	  /********************/
	 /* Create Directory */
	/********************/
	function CreateDir (){
	$dirName = ( !empty( $_POST['dirName'] ) ? Func::SanitizeFilename( $_POST['dirName'] ) : 'new_dir' );
	$theDir =  ( !empty( $_POST['theDir'] )  ? Func::cleanDirPath( $_POST['theDir'] ): DEFAULT_GAL_DIR );
	  if ( is_dir( $theDir .'/'. $dirName ) ) {
		Func::returnMsg( "A directory by the name of '$dirName' already exists in '$theDir'", 0 );
	  } else {
	      if ( @mkdir( $theDir .'/'. $dirName, 0775 ) ) { // We are good to create this directory:
	        Func::returnMsg( 'Your directory has been created succesfully', 1, 1 );
	      } else {
	        Func::returnMsg( "Unable to create dir: $dirName in '$theDir'", 0 );
			}
	   }
	}
	  /***************/
	 /* Delete Dir  */
	/***************/
	function deleteDir( $dirName )
	{
	  if( is_dir( $dirName ) )
	  {
	    if( @rmdir( $dirName ) ) 
		{
		  Func::returnMsg( "Directory [$dirName] was deleted. \n" );
		} else {
		Func::returnMsg( "Could not delete [$dirName]\n", 0 );
		}
	  } else {
		Func::returnMsg( $this->fileLocation." is not a valid directory \n", 0 );	
	  }
	}
	
	function renameFile ( $filename, $newName )
	{
	  $filename = $this->direct .'/'. $filename;
	  $fileInfo = Func::getFileInfo( $filename ); 
	  $newName  = $this->direct .'/'. Func::SanitizeFilename( $newName );
	    if ( @rename( $filename, $newName. '.' .$fileInfo['extension'] ) ) 
		{
		  Func::returnMsg( "$filename was succesfully renamed to $newName ".$fileInfo['extension'] );
		} else {
		Func::returnMsg( "Could not rename $filename to $newName ".$fileInfo['extension'], 0 );	
		}
	}

} /* Ends Class */

$oUp = new Upload( DEFAULT_GAL_DIR );	/* Initialize Upload Object */

?>