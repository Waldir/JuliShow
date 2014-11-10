<?php

class Gallery
{
	private $dir;

	function __construct ( $dir ) 
	{
		$this->dir = Func::cleanDirPath( $dir );
		$checkBaseDir = explode( "/", $this->dir );
		if( $checkBaseDir[0] !== Func::cleanDirPath( DEFAULT_GAL_DIR ) )
		{
		  die( 'Invalid base directory name');
		}
		if( !is_dir( $this->dir ) )
		{
		  die('Invalid directory path.');
		}

	}

	  /***********************/
	 /* Get Public Side     */
	/***********************/
	function PublicSide( $dir, $showDirs = 0 )
	{
	  $oGallery = new Gallery( $dir );
	  $aDir   = Gallery::getFoldersFilesArray( $oGallery->dir, 1 );
	  $aFiles = Gallery::getFoldersFilesArray( $oGallery->dir, 2, THUMB_PREF );
	  $theHTML = '';
  	  if( $aDir && $showDirs ) 
	  {
	    $theHTML .= '<h2><img src="layout/icons/dir.png" class="icon"> Folders </h2>';
		foreach ( $aDir as $key => $value ) 
		{
		$aQs = array( 'p' => 'gallery', 'dir' => $dir.'/'.$value );
		$theHTML .=	'
		    <span class="Y-Box" style="width: 100px; text-align: center;">
				<p>'.Func::QsLink( $aQs, '<img src="layout/icons/present.png">' ).'</p>
				<p>'.Func::QsLink( $aQs, Func::DirDisplayName( $value ) ).'</p>
			</span>';
		}
		unset($value, $key, $aQs);
	  }
	
	  if( $aFiles ) 
	  {
	    $page = ( empty( $_GET['page'] ) ? 1 : $_GET['page'] );
	    $aFiles = array_chunk( $aFiles, GAL_MAX_ITEMS_PER_PAGE );
	    $theHTML .= '<h2><img src="layout/icons/photo.png" class="icon"> Images</h2>';
		$theHTML .= Gallery::pagination();
		$i = 0; 
		foreach ( $aFiles[$page-1] as $filename) 
		{
		  if( stristr( $filename, THUMB_PREF ) ) { continue; }
		  $theHTML .= '
		  <span class="GalBox">
			'.Func::QsLink( array( 'src' => $dir.'/'.$filename ), '<img src="'.$dir.'/'.THUMB_PREF.$filename.'" class="GalImage">', 0, array( 'rel' => 'ibox', 'title' => Func::DirDisplayName( $filename ) ), 'img.php' ).'
		  
			<!-- Comments [START] -->
			<div id="ShowComment_'.Func::SanitizeFilename( $dir.'/'.$filename ).'" style="display: none">
			  <div class="fb-comments" data-href="http://'.$_SERVER['SERVER_NAME'].'/img.php?src='.$dir.'/'.$filename.'" data-num-posts="10" data-width="470"></div>
			</div>
			<!-- Comments [END] -->

			<span class="GalText">
			  <div class="fb-like" data-href="http://'.$_SERVER['SERVER_NAME'].'/img.php?src='.$dir.'/'.$filename.'" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false"></div>
			  <a href="#ShowComment_'.Func::SanitizeFilename( $dir.'/'.$filename ).'" rel="ibox&width=490" title="Comments for '.$filename.'"><img src="layout/icons/fb-comment.png" class="icon"></a>
			</span>
		  </span>';	
		  $i++;
		  if( ! ( $i % GAL_MAX_ROW_ITEMS ) ) 
		  { 
		   $theHTML .= "<br />\n"; 
		  }
		}
		$theHTML .= Gallery::pagination();
		unset($filename);
	  }
	  return $theHTML;
	}
	
	  /***********************/
	 /* File tree Display   */
	/***********************/
	function fileTreeDisplay( $dir )
	{
	$the_html = '
	<div class="dirBox table">
		<span class="titleCell">
			<a href="#" id="gallery_images" class="toggle"><img src="layout/icons/dir.png" class="icon"> Main gallery folder: '.$dir.' </a>
		</span>
		<span class="optionCell">
		'.Gallery::formNewDir ( $dir ).'
		</span>
		<span class="optionCell">
		<a href="#upload_'.Func::SanitizeFilename( $dir ).'" rel="ibox" title="Upload images to: '.$dir.'"><img src="layout/icons/upload.png" class="icon"></a>
		<iframe id="upload_'.Func::SanitizeFilename( $dir ).'" src="upload.form.php?dir=' .  $dir  . '"" style="display: none" height="400" width="400"></iframe>
		</span>
	</div>
	<div class="ShowBox" id="ShowBox_gallery_images">
	'.Gallery::fileTreeDir( $dir ).'
	</div>
	';
	echo $the_html;
	}
	  /***********************/
	 /* Form: New Directory */
	/***********************/
	static function formNewDir ( $location )
	{
	return '
	<!-- Creat Dir Form for ' . $location. ' [START] -->
	<a href="#new_dir_' .Func::SanitizeFilename( $location ).'" rel="ibox" title="Creat new directory inside: '.$location.'"><img src="layout/icons/new_dir.png" class="icon"></a>
	<form action="session.php" method="POST" id="new_dir_' .Func::SanitizeFilename( $location ).'" style="display: none">
	<input type="hidden" name="todo" value="NewDir" />
	<input type="hidden" name="theDir"  value="'.$location.'" />
	<input type="text" value="Enter name" size="30" name="dirName" />
	<input type="submit" value="Create" class="apply"/>
	</form>
	<!-- Creat Dir Form for ' . $location. ' [End] -->
	';
	}
	
	  /***********************/
	 /* File Tree Directory */
	/***********************/
	function fileTreeDir( $dir ) 
	{
		$dir = Func::cleanDirPath( $dir );			 // Clean dir name
		$file = Gallery::getFoldersFilesArray( $dir ); //Get the arrays
		
		if( count($file) > 0 ) 
		{
		  $fileTree  = '';
		  foreach( $file as $this_file ) 
		  {
			if( is_dir( $dir .'/'. $this_file ) ) 
			{
				// Directory
				$fileTree .= '
				<div class="dirBox table" id="' .Func::SanitizeFilename( $dir.'/'.$this_file ).'">

					<span class="titleCell">
						<a href="#" id="dir_'.$this_file.'" class="toggle"><img src="layout/icons/dir.png" class="icon">' . htmlspecialchars($this_file) . '</a>
					</span>
							
					<span class="optionCell">
						<!-- Delete Form for ' . $this_file. ' [START] -->
						<form action="session.php" method="post">
						<input type="hidden" name="todo" value="DeleteFiles" />
						<input type="hidden" name="theFile" value="' . $this_file. '" />
						<input type="hidden" name="theDir"  value="' . $dir .'" />
						<input type="image" src="layout/icons/delete.png">
						</form>
						<!-- Delete Form for ' . $this_file. ' [End] -->
					</span>
					<span class="optionCell">
						'.Gallery::formNewDir ( $dir.'/'.$this_file ).'
					</span>
					<span class="optionCell">
						<a href="#upload_'.Func::SanitizeFilename( $dir.'/'.$this_file ).'" rel="ibox" title="Upload images to: '.$dir.'/'.$this_file.'"><img src="layout/icons/upload.png" class="icon"></a>
						<iframe id="upload_'.Func::SanitizeFilename( $dir.'/'.$this_file ).'" src="upload.form.php?dir=' .  $dir .'/'. urlencode( $this_file ) . '"" style="display: none" height="400" width="400"></iframe>
					</span>
							
				</div>
						
				<div class="ShowBox" id="ShowBox_dir_'.$this_file.'">
					'.Gallery::fileTreeDir( $dir .'/'. $this_file ).'
				</div>';
				  
			} else {
				// File
				$fileTree .= '
				<div style="background: #'.Func::cycleCell( 'dcdcdc', 'ebebeb' ).';" class="table" id="' .Func::SanitizeFilename( $dir.'/'.$this_file ).'">
					
				<span class="titleCell">
					<img src="layout/icons/jpg.png" class="icon">
					<a href="' .  $dir .'/'. urlencode( $this_file ) . '" rel="ibox" title="'.$this_file.'">'.$this_file.'</a>
				</span>
						
				<span class="optionCell">
					<form action="session.php" method="post">
					<input type="hidden" name="todo" value="DeleteFiles" />
					<input type="hidden" name="theFile" value="' . $this_file. '" />
					<input type="hidden" name="theDir"  value="' . $dir .'" />
					<input type="image" src="layout/icons/delete.png">
					</form>
				</span>
				</div>
							';
			}
					
		  }
		}
		return ( !empty( $fileTree ) ? $fileTree : '' );
	}
	
	  /***********************************************/
	  /* Get array of folders and files
	  /* $return: 0 returns a merged array of both
      /*          1 returns directorys array
	  /*		  2 returns files array
	  /***********************************************/
	static function getFoldersFilesArray ( $dir, $return = 0, $excludeKey = '' )
	{ 
	  $dir = Func::cleanDirPath( $dir ); 	// Clean the dir name
	  $file = scandir( $dir );				// Get directories/files
	  natcasesort( $file );					// Sort Files
	  $files = $dirs = array();				// Make directories first
	  $allowedExtensions = array('jpg','jpeg','gif','png'); 
	  
		foreach( $file as $thisfile ) 
		{
		  if( is_dir( $dir .'/'. $thisfile ) && $thisfile != '.' && $thisfile != '..' && $return !== 2 ) 
		  {
		    if( $excludeKey !=='' && stristr( $thisfile, $excludeKey ) ) { continue; }
		    $dirs[] = $thisfile; 
		  } elseif( is_file( $dir .'/'. $thisfile ) && $return !== 1 )
		  {
		    if( $excludeKey !=='' && stristr( $thisfile, $excludeKey ) ) { continue; }
		    if( in_array( end( explode( ".", $thisfile ) ), $allowedExtensions ) )
			{
		      $files[] = $thisfile;
			  /* Create Thumbnails ( Could be moved else where... )*/
			  if( !file_exists( $dir  .'/'.  THUMB_PREF . $thisfile ) && !stristr( $thisfile, THUMB_PREF )) 
			  {
			    Gallery::resizeToFile ( $dir .'/'. $thisfile, THUMB_MAX_SIZE, THUMB_MAX_SIZE, $dir  .'/'.  THUMB_PREF . $thisfile );
			  }
			  /* ----------------------------------------------- */
		    }
		  }
		}
		
		// Lets see what needs to be returned.
	  	switch ( $return ) 
		{
		  case 0: return array_merge( $dirs, $files ); break;
		  case 1: return $dirs; break;
		  case 2: return $files; break;
		}
	}
	
	/* resizeToFile resizes a picture and writes it to the harddisk
	* $sourcefile = the filename of the picture that is going to be resized
	* $newWidth    	 = X-Size of the target picture in pixels
	* $newheight     = Y-Size of the target picture in pixels
	* $targetfile = The name under which the resized picture will be stored
	* $quality   = The Compression-Rate that is to be used 
	*/
	static function resizeToFile ( $sourcefile, $newWidth, $newheight, $targetfile, $crop = true, $quality = 100)
	{
	  /* Get the dimensions of the source picture */
	  list( $Source_w, $Source_h, $Source_type ) = getimagesize( $sourcefile );

	  if( $crop )
	  {
	    /* Setting the crop size */
	    if( $Source_w > $Source_h )
	    {  
		  $BiggestSide = $Source_w;   
		  $CropPercent = .5;   
		  $CropWidth   = $BiggestSide * $CropPercent;   
		  $CropHeight  = $BiggestSide * $CropPercent;
		  $CropX       = ( $Source_w - $CropWidth )/2;
		  $CropY       = ( $Source_h - $CropHeight )/2;
	    } else {  
		  $BiggestSide = $Source_h;   
		  $CropPercent = .5;   
		  $CropWidth   = $BiggestSide * $CropPercent;   
		  $CropHeight  = $BiggestSide * $CropPercent;
		  $CropX       = ( $Source_w - $CropWidth )/2;
		  $CropY       = ( $Source_h - $CropHeight )/7; 
	    }
	  } else {
		  /* This creates thumbs with an aspect ration */
		  $imgType    = $Source_type ; 
		  $ratio      = $Source_w / $Source_h ; 
		  $w          = $ratio > 1 ? $newWidth : ( $newWidth * $Source_w ) / $Source_h;
		  $h          = $ratio > 1 ? ( $newWidth * $Source_h ) / $Source_w : $newWidth;
		  $CropWidth  = $Source_w;
		  $CropHeight = $Source_h;
		  $newWidth   = $w;
		  $newheight  = $h;
		  $CropY      = 0;
		  $CropX      = 0; 
		}
		
	  /* imagecreatefromstring will automatically detect the file type */
	  $Source = imagecreatefromstring( file_get_contents( $sourcefile ) );
	  /* Create the thumbnail canvas */ 
	  $thumb = imagecreatetruecolor( $newWidth, $newheight );
	  /* Resize the original picture and copy it into the just created (thumb) image object. */
	  imagecopyresampled( $thumb, $Source, 0, 0, $CropX, $CropY, $newWidth, $newheight, $CropWidth, $CropHeight );
	  /* Destroy the images */
	  imagedestroy( $Source );
	  /* Write thumbnail based on file type ***/
	  switch ( $Source_type ) 
	  {
	    case 1: imagegif ( $thumb, $targetfile ); break;
	    case 2: imagejpeg( $thumb, $targetfile, $quality ); break;
	    case 3: imagepng ( $thumb, $targetfile ); break;
	    case 7: imagewbmp( $thumb, $targetfile ); break;
	   }
	  return true;
	}

	  /****************/
	 /* Gallery Path */
	/****************/
	function GalleryPath ( $aGET ) 
	{
		$link = 'Navigate: ';
	  if( isset( $_GET['dir'] ) ) 
	  {
		$sDir = explode( '/', $_GET['dir']);
		$i = 0;
		$add = '';
		  foreach ( $sDir as $key => $val )
		  {
		    $add  .= ( ($i > 0 ) ? $sDir[($i-1)].'/' : '' );
		    $link .= ' / ';
			$aQs = array( 'p' => $_GET['p'], 'dir' => $add.$val );
			$link .= Func::QsLink( $aQs, Func::DirDisplayName ( $val ) );
		    $i++;
		  }
	  }
		return $link;
	}
	
//-----------------------
// PAGE NAVIGATION
//-----------------------
	function pagination () 
	{
	  global $dir;
	  $page = ( empty( $_GET['page'] ) ? 1 : $_GET['page'] );
	  $aFiles = Gallery::getFoldersFilesArray( $dir, 2, THUMB_PREF );
	  $totalItems = sizeof ( $aFiles );
	  $aFiles = array_chunk( $aFiles, GAL_MAX_ITEMS_PER_PAGE );
	  $perPage = ( $page == 'all' ? $totalItems : GAL_MAX_ITEMS_PER_PAGE );

		$pageNavigation = '';
		if( $totalItems > $perPage ) 
		{
		  $pageNavigation .= '<div class="Pagination">';
		  $class['class'] = 'Link';
		  $previousLink = ( $page - 1 ) !== 0          ? Func::QsLink( array( 'page' => $page - 1 ), 'Previous', true, $class ) : '<span class="noneLink">Previous</span>';
		  $nextLink     = ( $page ) < count( $aFiles ) ? Func::QsLink( array( 'page' => $page + 1 ), 'Next',     true, $class ) : '<span class="noneLink">Next</span>';
		  $pageNavigation .= $previousLink;
			for ( $i = 1; $i < count( $aFiles ) + 1; $i++ ) {
			  $pageNavigation .= ( ( $page == $i ) ? '<span class="noneLink">'.$i.'</span>' : Func::QsLink( array( 'page' => $i ), $i, true, $class ) );
			 // $pageNavigation .= (  $i != ceil( $aFiles ) / $perPage ? ' | ' : '' );
			}
		  //Insert link to view all images
		  //$pageNavigation .= ( $page == 'all' ?  ' All ' : "  <a href='$_SERVER[PHP_SELF]?page=all$this->querystring'>All</a>");
		  $pageNavigation .= $nextLink;
		  $pageNavigation .= '</div>';
		}
		  return $pageNavigation;
	}

	
} /* Engds Gallery Object */
?>