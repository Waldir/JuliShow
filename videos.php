<h2><img src="layout/icons/video.png" class="icon"> Videos</h2>
	<div class="BarContent">
	<?php echo $oSession->getPageText('video_header');?>
	</div>
<?php MyVideo::getPublicSide();?>