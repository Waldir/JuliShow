<h2> <img src="layout/icons/contact.png" class="icon"> Contact us </h2>
	<div class="BarContent">
	<?php echo $oSession->getPageText('contact_header');?>
	</div>

<img src="layout/info/contact.jpg" style="float: right"> 

<form name="contact_form" method="post" action="session.php" id="ContactForm" onsubmit="return submitForm('#ContactForm');">

	<p class="Y-Box"><label>First Name * </label> <img src="layout/icons/user.png" class="icon"> <input name="fname"   type="text" size="40"/></p>
	<br />
	<p class="Y-Box"><label>Your E-mail * </label> <img src="layout/icons/contact.png" class="icon"> <input name="mail"    type="text" size="40" /></p>
	<br />
	<p class="Y-Box"><label>Subject * </label> <img src="layout/icons/write.png" class="icon"> <input name="subject" type="text" size="40" value="<?php echo ( isset( $_GET['pkg'] ) ? $_GET['pkg'] : '' );?>" /></p>
	<br />
	<p><label>Message * </label> <textarea name="message" rows="5" style="width: 430px;"></textarea></p>
	 <input type="hidden" name="todo" value="contact_form" />
	<p><input type="reset" name="reset" value="Reset" class="regular" /> <input type="submit" name="Submit" value="Submit" class="regular" /></p>
</form>

<br />
<br />
<br />
<br />
<?php echo $oSession->getPageText('contact_footer');?>