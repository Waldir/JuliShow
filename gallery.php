
<h2><img src="layout/icons/dir.png" class="icon"> <?php echo Gallery::GalleryPath( $_GET );?> </h2>
	<div class="BarContent">
	<?php echo $oSession->getPageText('gallery_header');?>
	</div>
	<?php echo Gallery::PublicSide( $dir, 1 ); ?>
