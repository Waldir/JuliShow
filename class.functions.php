<?php
class Func {

	 /**********************************************\
	 * Return Message 
	 * $msg: the message
	 * $success: Error or succes, success by default
     * $redirect: true redirects to refferer
     * $remove: removes an id element
	 \**********************************************/

	static public function returnMsg ( $msg, $success = 1,  $redirect = false, $remove = false ) 
	{
	  
	  if( $redirect )
	  {
	  $new_msg = '
	  <noscript>
      <meta http-equiv="refresh" content="5;url='.$_SERVER['HTTP_REFERER'].'" />
      </noscript>
		'.$msg.' redirecting in <span id="seconds">5</span>.
      <script>
      var seconds = 5;
      setInterval(
        function(){ 
          if (seconds <= 1) {
            window.location = "'.$_SERVER['HTTP_REFERER'].'";
          }
          else {
            document.getElementById("seconds").innerHTML = --seconds;
          }
        },
        1000
      );
      </script>';
	  } else {
	    $new_msg = $msg;
	  }
	  $remove = ( $remove ? $remove : '' );
	  $value = array( "error_success" => $success, "msg" => $new_msg, "remove" => $remove );
	  
	  if( @$_POST['ajaxrequest'] == 1 )
	  {
		header('Content-Type: application/json');
		echo json_encode($value);
	  } else {
		echo $new_msg;
	  }

	}
	
	/* Cycle cell color or class */
	static public function cycleCell ( $aColor, $bColor ) 
	{
		static $bgc = TRUE;
		return ( ( $bgc = !$bgc ) ? $bColor : $aColor );
	}
	

	  /******************/
	 /* Check Email    */
	/******************/
	static public function CheckEmail($email) 
	{
	return ( filter_var( $email, FILTER_VALIDATE_EMAIL) ? true : false );
	}
	
	  /***********************/
	 /* Clean Dir           */
	/***********************/
	function cleanDirPath( $dir ) 
	{
	  if( substr($dir, -1) == "/" ) $dir = substr($dir, 0, strlen($dir) - 1);
	  return $dir;
	}

	/* Display the name of a directory as a readable string */
	function DirDisplayName ( $str ) {
		$trans = array( '_'	=> ' ' );
		$str = strip_tags( $str );

			foreach ($trans as $key => $val) {
			  $str = preg_replace("#".$key."#i", $val, $str);
			}
		$str = ucfirst ($str);
		$str = explode( '.', $str);
		return trim( stripslashes( $str[0] ) );
	}

	/**
	* Function: sanitize Returns a sanitized string, typically for URLs.
	* Parameters:
	*     $string - The string to sanitize.
	*     $extention - if *true*, it will not remove "." as it will kill file extentions.
	*     $force_lowercase - Force the string to lowercase?
	*     $anal - If set to *true*, will remove all non-alphanumeric characters.
	*/
	function SanitizeFilename( $string, $extention = false, $force_lowercase = true, $anal = false ) {
      $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]",
                     "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                     ",", "<", ">", "/", "?");
	    if( $extention === false ){ 
	      array_push( $strip, "." );
		}
      $clean = trim( str_replace( $strip, "", strip_tags( $string ) ) );
      $clean = preg_replace( '/\s+/', "_", $clean );
      $clean = ( $anal ) ? preg_replace( "/[^a-zA-Z0-9]/", "", $clean ) : $clean ;
      return ( $force_lowercase ) ? ( function_exists( 'mb_strtolower' ) ) ? mb_strtolower( $clean, 'UTF-8' ) : strtolower($clean) : $clean;
	}
	
	/* Get The extension of a file if it exists
	 * if it doesnt, it get the extention just by the provided name
	 */
	function getFileInfo ( $file ) 
	{
	  if( file_exists( $file ) )
	  {
	    return pathinfo( $file );
	  } else {
	    return false;
	  }
	}
	
	function templateReplace ( $string )
	{

	/*$search =  array( '#\{gallery:(.+?)\}#i' );
	$replace = array( "\$oGallery = new Gallery( $1 ); \$oGallery->PublicSide();" );
	/*$output = preg_replace_callback( $search, $replace, $string ); */	
	$string =  preg_replace_callback("#\{gallery: '(.+?)'(?: dir: ([0-1]))?\}#i", create_function('$i', '$addDir = isset( $i[2] ) ? 1 : 0;  return Gallery::PublicSide( $i[1], $addDir );' ), $string);
	
	echo $string . PHP_EOL;
	}
	
	/********************************************************
	/ QsLink: Returns a link with new values (plus old ones?)
	/ 	$aQs: 		New Values to add (Array)
	/	$sName:		Name of the link.
	/	$addGet:	Add $_GET Values to the query String
	/	$aClassId:	Link Elements (id, class)
	/	$sScript:	Script to run (index.php)
	/********************************************************/
	function QsLink ( $aQs, $sName, $addGet = false, $aClassId = '', $sScript = 'self' )
	{
	  /* If Query String provided isnt an array, turn it into one */
	  $aQs = !is_array( $aQs ) ? array() : $aQs;
	  
	  /* This parses thing such as class, id, rel and others */
	  $classId = ' ';
	  if( $aClassId && is_array( $aClassId ) )
	  {
	    $classId = ' ';
	    foreach ( $aClassId as $key => $val )
		{
		  $classId .= $key . '="' . $val. '" ';
		}
		unset( $key, $val );
		$classId = substr( $classId, 0, -1 );
	  }
	  /* Check the Script we want to run i.e: index.php */
	  $sScript = $sScript == 'self' ? $_SERVER['PHP_SELF'] : $sScript;
	  
	  /* Creat the Query String */
	  $args = !empty( $_GET ) && $addGet == true ? http_build_query( $aQs + $_GET ) : http_build_query( $aQs ); 
	  
	  /* Return the link HTML */
	  return '<a href="'.$sScript.'?'.$args.'"'.$classId.'>'.$sName.'</a>';
	  
	}
		
	
} /* End class */
?>