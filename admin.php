	<!-- Admin Login [START]-->
	<div class="center_text">
	<h1>Administrator:</h1>
	<?php if( $oSession->loggedin ) { ?>
	
	<form action="session.php" method="post" id="adminLoginForm">
	<input name="todo" type="hidden" value="adminLogout">
	You are logged in as <strong><?php echo admin_username;?></strong>: <input type="submit" value="Log out" class="regular" />
	</form>

	<?php } else { ?>
	<form action="session.php" method="post" id="adminLoginForm">
	<input name="todo" type="hidden" value="adminLogoin">
	<p class="Y-Box">
	<label>Username: </label> 
	<input name="admin_username" type="text" /> 
	<br />	
	<label>Password: </label> 
	<input name="admin_password" type="password" />
	<br />
	<input value="Log In" type="submit"  class="regular" name="adminLogin" />
	</p>
	</form>
	<?php } ?>
	</div>
	<!-- Admin Login [END]-->
	
<?php if( $oSession->loggedin ) { ?>
	
	<!-- Edit Web site settings [START]-->
	
	<h2 class="table">
	
		<span class="titleCell">
			<img src="layout/icons/write.png" class="icon"> Edit Web site settings
		</span>
		
		<span class="optionCell">
			 <a href="#" id="SiteSettings" class="toggle"><span class="toggleDown">&nbsp;</span></a>
		</span>
	
	</h2>
	
	<div class="ShowBox" id="ShowBox_SiteSettings" >
	<form action="session.php" method="post">
	<?php 
	foreach ( $oSession->siteSettings as $key => $value) {
	$inputType = ( stristr( $key, 'password' ) ? 'password' : 'text' ); 
	?>
	<label for="<?php echo $key;?>" style="width: 30%;"><?php echo ucfirst ( preg_replace("#_#", " ", $key) );?>:</label> 
	
	<?php
	if( $key == 'enable_site' ){
	?>
	<select name="<?php echo $key;?>">
		<option value="1" <?php echo ( $value == '1' ?  'selected' : '' ); ?>>On</option>
		<option value="0" <?php echo ( $value == '0' ? 'selected' : '' ); ?>>Off</option>
	</select>
		<hr>
	<?php } else { ?>
	<input type="<?php echo $inputType;?>" name="<?php echo $key;?>" value="<?php echo $value;?>" />
		<hr>
	<?php }} ?>

	<input type="hidden" name="todo" value="save_settings" />
	<input type="submit" value="Update" class="apply" />
	</form>
	</div>
	
	<!-- Edit Web site settings [END]-->

	<!-- Manage Youtube Videos [START]-->
	<h2 class="table">
	
		<span class="titleCell">
			<img src="layout/icons/youtube.png" class="icon"> Manage Youtube Videos
		</span>
		
		<span class="optionCell">
			 <a href="#" id="ManageVideos" class="toggle"><span class="toggleDown">&nbsp;</span></a>
		</span>
		
	</h2>
	
	<div class="ShowBox" id="ShowBox_ManageVideos">
	<?php
	MyVideo::getAdminSide(); 
	?>
	</div>
	<!-- Manage Youtube Videos [END]-->

	<!-- Manage Gallery [START]-->
	<h2 class="table">
		<span class="titleCell">
			<img src="layout/icons/gallery.png" class="icon"> Manage Gallery 
		</span>
		
		<span class="optionCell">
			<a href="#" id="ManageGallery" class="toggle"><span class="toggleDown">&nbsp;</span></a>
		</span>
	</h2>
	
	<div class="ShowBox" id="ShowBox_ManageGallery">

		<h3>How to:</h3>
		<ul>
		<li><img src="layout/icons/delete.png" class="icon"> Delete files and folders.</li>
		<li><img src="layout/icons/new_dir.png" class="icon"> Creat new folder inside other folders.</li>
		<li><img src="layout/icons/upload.png" class="icon"> Upload images to the selected folder.</li>
		<li>* Folder has to be empty in order to be deleted.</li>
		<li>* In order to delete a thumb file (_thb_filename.jpg) its correspoinding image has to be deleted first (filename.jpg)</li>
		</ul>
		
		<?php Gallery::fileTreeDisplay( $dir );?>
		
	</div>
	<!-- Manage Gallery [END]-->
	
	<!-- Manage Navigation [START]-->
	<h2 class="table">
		<span class="titleCell">
			<img src="layout/icons/right.png" class="icon"> Manage Navigation
		</span>
		
		<span class="optionCell">
			<a href="#" id="ManageNav" class="toggle"><span class="toggleDown">&nbsp;</span></a>
		</span>
	</h2>
	
	<div class="ShowBox" id="ShowBox_ManageNav">
		<?php echo $oSession->getNavigation(1); ?>
	</div>
	<!-- Manage Navigation [END]-->

	<!-- Add PageText [START]-->
	<h2 class="table">
		<span class="titleCell">
			<img src="layout/icons/add.png" class="icon"> Add a page
		</span>
		
		<span class="optionCell">
			<a href="#" id="AddPage" class="toggle"><span class="toggleDown">&nbsp;</span></a>
		</span>
	</h2>
	
	<div class="ShowBox" id="ShowBox_AddPage">
		<form action="session.php" method="post">
		Title: <input type="text" name="title" />
		<ul>
		<li><a href="#" class="addTextToInput" id="atti_addPage">Insert Gallery (<img src="layout/icons/gallery.png" class="icon"> Images only)</a></li>
		<li><a href="#dir" class="addTextToInput" id="atti_addPage">Insert Gallery (<img src="layout/icons/gallery.png" class="icon">+<img src="layout/icons/dir.png" class="icon"> Images & Directories)</a></li>
		</ul>
		<textarea name="text" style="width: 750px;" id="atti_addPage_ta" /></textarea>			
		<input type="submit" value="Add" class="apply" />
		<input type="hidden" name="todo" value="addPage" />
		</form>
	</div>
	<!-- Add PageText [END]-->

	<?php
	$aPageText = $oSession->getPageText();
	  foreach ( $aPageText as $key => $value) 
	  {
	?>
	<!-- Edit PageText <?php echo $value['title'];?> [Start]-->
<div id="pageTextbar_<?php echo $value['id'];?>">
	<h2 class="expand table">
	
		<span class="titleCell">
			<img src="layout/icons/write.png" class="icon"> Edit: <?php echo $value['title'];?>
		</span>
		<span class="optionCell">
		<form method="post" action="session.php">
		<input type="hidden" name="todo" value="deletePageText" />
		<input type="hidden" name="id" value="<?php echo $value['id'];?>" />
		<input type="image" src="layout/icons/delete.png" onclick="return deletechecked('Are you sure you want to delete: <?php echo $value['title'];?>');">
		</form>
		</span>
		<span class="optionCell">
			 <a href="#" id="pagetext_<?php echo $value['id'];?>" class="toggle"><span class="toggleDown"> </span></a>
		</span>

	</h2>

	<div class="ShowBox" id="ShowBox_pagetext_<?php echo $value['id'];?>">
		<form method="post" action="session.php">
		Title: <input type="text" name="title" value="<?php echo $value['title'];?>" />
		<ul>
		<li><a href="#" class="addTextToInput" id="atti_<?php echo $value['title'];?>">Insert Gallery (<img src="layout/icons/gallery.png" class="icon"> Images only)</a></li>
		<li><a href="#dir" class="addTextToInput" id="atti_<?php echo $value['title'];?>">Insert Gallery (<img src="layout/icons/gallery.png" class="icon">+<img src="layout/icons/dir.png" class="icon"> Images & Directories)</a></li>
		</ul>
		<textarea name="text" style="width: 750px;" id="atti_<?php echo $value['title'];?>_ta">
		<?php echo stripslashes( $value['text'] );?>
		</textarea>
		<input type="hidden" name="todo" value="updatePageText" />
		<input type="hidden" name="id" value="<?php echo $value['id'];?>" />
		<input type="submit" class="apply" value="Update"/>
		</form>
	</div>
</div>
	<!-- Edit PageText <?php echo $value['title'];?> [END]-->
<?php }?>
<?php } ?>