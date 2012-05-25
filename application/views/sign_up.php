<div class="gourmet-title">The Gourmet</div>

<div class="center-holder">

	<div id="gallery">

		<div id="slides">

			<div class="slide"><a href="/index.php/recipe/browse"><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/macbook.jpg" width="600" height="400" alt="side" /></a></div>
			<div class="slide"><a href="/index.php/recipe/browse"><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/iphone.jpg" width="600" height="400" alt="side" /></a></div>
			<div class="slide"><a href="/index.php/recipe/browse"><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/imac.jpg" width="600" height="400" alt="side" /></a></div>

		</div>

		<div id="menu">

			<ul>
				<li class="menuItem"><a href=""><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/thumb_macbook.png" alt="thumbnail" /></a></li>
				<li class="menuItem"><a href=""><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/thumb_iphone.png" alt="thumbnail" /></a></li>
				<li class="menuItem"><a href=""><img src="<?php echo base_url(); ?>style/img/slideshow/sample_slides/thumb_imac.png" alt="thumbnail" /></a></li>
			</ul>

		</div>

	</div>

	<div class="form-center" id ="form-signup">
		<script type="text/javascript" src="<?php echo base_url();?>style/js/validation_hoa.js"></script>

		<h1 style="margin-bottom:0; line-height:2em;">Register</h1>

		<br/>

		<form action="<?php echo base_url(); ?>index.php/user/add_user" method="post" accept-charset="utf-8">

			<fieldset>

				<!-- email address -->
				<label>Email address (Required)</label>
				<span class="error"><?php echo form_error('email_address');?></span>

				<span>
					<input size="68" type="text" id="email_address" name="email_address" value="<?php echo set_value('email_address'); ?>" onkeyup="validate_email('email_address', 'yes1', 'no1')"/>
					<img src="<?php echo base_url(); ?>style/img/yes.png" alt="yes" width="16" height="16" id ="yes1" style="position:absolute;margin-top:-30px;margin-left:220px;" />
					<img src="<?php echo base_url(); ?>style/img/no.png" alt="no" width="16" height="16" id ="no1" style="position:absolute;margin-top:-30px;margin-left:220px;" />
					<br/>
				</span>
				<br />

				<!-- password -->
				<label>Password (Required)</label>
				<span class="error"><?php echo form_error('password');?></span>
				<span>
					<input size="68" type="password" id="password" name="password" onkeyup="validate_chars('password', 'yes2', 'no2')" />
					<img src="<?php echo base_url(); ?>style/img/yes.png" alt="yes" width="16" height="16" id ="yes2" style="position:absolute;margin-top:-30px;margin-left:220px;" />
					<img src="<?php echo base_url(); ?>style/img/no.png" alt="no" width="16" height="16" id ="no2" style="position:absolute;margin-top:-30px;margin-left:220px;" />
				</span>

				<!-- password -->
				<label>Confirm Password (Required)</label>
				<span class="error"><?php echo form_error('passconf');?></span>
				<span>
					<input size="68" type="password" id="passconf" name="passconf" onkeyup="validate_chars('password', 'yes3', 'no3')" />
					<img src="<?php echo base_url(); ?>style/img/yes.png" alt="yes" width="16" height="16" id ="yes3" style="position:absolute;margin-top:-30px;margin-left:220px;" />
					<img src="<?php echo base_url(); ?>style/img/no.png" alt="no" width="16" height="16" id ="no3" style="position:absolute;margin-top:-30px;margin-left:220px;" />
				</span>

			</fieldset> <!-- End of account info -->

			<!-- submit button -->
			<button class="cupid-green" type="submit" style="margin-top:20px;">Create Account</button>
		</form>
		<div style="margin-top:10px;">
			<a href="<?php echo base_url(); ?>index.php/user/facebookLogin">
				<img src="<?php echo base_url() ?>style/img/facebook.png" width="200" height="24" alt="Log in with facebook">
			</a>
		</div>
	</div><!-- end of form-signup -->

</div>