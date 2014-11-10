<?php
include ('session.php');
$get_dir =  ( !empty( $_GET['dir'] ) ? $_GET['dir'] : DEFAULT_GAL_DIR);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" rel="stylesheet" href="style.css" />
</head>
<body>
	<div class="bar"><img src="layout/icons/upload.png" class="icon"> Upload:</div>
		<div class="BarContent">
		* Select file to be uploaded.<br />
		* Keep "Resize" checked to resize (Faster viewing)<br />
		* Only upload image(jpg, png, gif) files.<br />
		</div>
	<div class="Y-Box">
		<form action="session.php" method="post" enctype="multipart/form-data">
		<input type="file" name="upFile"> 
		<input type="submit" value="Upload" class="upload">
		<input type="hidden" name="theDir" value="<?php echo $get_dir;?>" />
		<input type="hidden" name="todo" value="UploadSubmit" />
		<br />
		<input type="checkbox" name="doResize" value="1" checked/> Resize.
		</form>
	</div>
		
</body>
</html>