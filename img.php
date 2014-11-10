<?php
$file = str_replace( $_GET['src'] = 
if( !file_exists( $_GET['src'] ) ) {
die('Invalid image file. ');
}
	$stamp_source = 'layout/images/stamp.png';
	$stamp = imagecreatefrompng( $stamp_source );// Watermark
	$im = $_GET['src'];//The image
	
	/* Get the dimensions of the source picture */
	list( $Source_w, $Source_h, $Source_type ) = getimagesize( $im );
	list( $Stamp_w, $Stamp_h, $Stamp_type ) = getimagesize( $stamp_source );
	
		if( $Stamp_w * $Stamp_h > $Source_w * $Source_h ) 
		{ 
		  header('Content-type: image/jpeg');
		  readfile( $im );
		  die();
		}
	
	/* Write thumbnail based on file type ***/
	switch ( $Source_type ) 
	{
	  case 1: $im = imagecreatefromgif ( $im ); break;
	  case 2: $im = imagecreatefromjpeg( $im ); break;
	  case 3: $im = imagecreatefrompng ( $im ); break;
	  case 7: $im = imagecreatefromwbmp( $im ); break;
	}
	
//die();
// Copy the stamp image onto our photo using the margin offsets and the photo 
// width to calculate positioning of the stamp. 
imagecopy(
	$im, 						//Destination image link resource.
	$stamp, 					//Source image link resource.
	$Source_w - $Stamp_w - 10, 	//x-coordinate of destination point.
	$Source_h - $Stamp_h - 10, 	//y-coordinate of destination point.
	0,							//x-coordinate of source point.
	0, 							//y-coordinate of source point.
	$Stamp_w, 					//Source width.
	$Stamp_h					//Source height.
	);

// Output and free memory
header('Content-type: image/png');
imagepng($im);
imagedestroy($im);
?>