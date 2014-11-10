<?php
include ('session.php');
$p = ( isset( $_GET['p'] ) ? $_GET['p'] : '' );
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta property="og:title" content="<?php echo site_name;?>" />
<meta property="og:type" content="company" />
<meta property="og:url" content="<?php echo facebook_url;?>" />
<meta property="og:image" content="http://julishow.com/icon.jpg" />
<meta property="og:site_name" content="<?php echo site_name;?>" />
<meta property="fb:app_id" content="<?php echo facebook_appid;?>" />
<title><?php echo site_name;?></title>
<?php
if( mouse_cursor_image_url ){
?>
<style type="text/css">
	* { cursor: url('<?php echo mouse_cursor_image_url;?>'),  pointer;}
</style>
<?php } ?> 
<link type="text/css" rel="stylesheet" href="style.css" />
<link type="text/css" rel="stylesheet" href="ibox/skins/lightbox/lightbox.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> 
<script type="text/javascript" src="javascript/jquery.cookie.js"></script> 
<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> 
<script type="text/javascript" src="ibox/ibox.js"></script>
<script type="text/javascript" src="javascript/php_file_tree.js"></script>
<script type="text/javascript" src="javascript/waldir.js"></script>
<link rel="shortcut icon" type="image/x-icon" href="layout/icons/favicon.ico">
<script type="text/javascript">
//<![CDATA[
        bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
  //]]>
  </script>
<div id="fb-root"></div>
<script>
  (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo facebook_appid;?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>


</head>

<body>
<div id="message_box">
	<span id="error_success"></span>
	<span id="message_box_msg"></span>
	<span id="close_message">_</span>
</div>

<div id="fb-root"></div>

<?php
	/* If site is disabled show the splash page. */
	if( !enable_site && !$oSession->loggedin ) 
	{
		if( $p == 'admin' ){ include( $p.'.php' ); }
		include('splash.php');
		die();
	}
?>
 
	<div id="wrapper">
		<!-- HEADER [START] -->
		<div id="header"></div>
		 <!-- HEADER [END] --> 
		 
		 <!-- Left Column [START] --> 
		 <div id="leftcolumn">
			<div id="side_menu"> 
			<?php 
			if( $oSession->loggedin )
			{
			  echo '<h2><a href="?p=admin"><img src="layout/icons/user.png" class="icon"> Admin Panel</a><br /></h2><br />';
			}
			echo $oSession->getNavigation();
			?>
			</div>
			<div class="bottom"></div>
			<div id="nav_likeus"></div>
			  <div class="W-Box">
			    <div class="fb-like" data-href="<?php echo facebook_url;?>" data-send="true" data-layout="button_count" data-width="200" data-show-faces="false"></div>
			  </div>
			  <div class="W-Box">
			    <div class="fb-activity" data-site="julishow.com" data-app-id="<?php echo facebook_appid;?>" data-width="200" data-height="250" data-header="true" data-border-color="white" data-recommendations="true"></div>
			  </div>
			<div class="bottom"></div>
			<div id="nav_youtube" ><?php MyVideo::getPublicSide(2);?> 
			<br />
			 <a href="?p=videos" class="R-Box"><img src="layout/icons/video.png" class="icon"> View more Videos</a>
			</div>
			<div class="bottom"></div>
		</div>

		 <!-- Left Column [END] -->
		 
		 <!-- Content [START] --> 
		 <div id="content">
			<div class="pad">
				<?php
				if( file_exists( "$p.php" ) )
				{
				  include ( "$p.php" );
				} elseif( $oSession->pageExists( $p ) == true )
				{
				  echo $oSession->getPageText( $p );
				} else {
				  include ( "home.php" );
				}
			    ?>
				<br />
			</div>
		 </div>
		 <!-- Content [END] --> 

		 <div id="footer">
			<div class="foot_pad">
				<div class="Party-Box">
					<?php echo $oSession->getPageText('page_footer');?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>