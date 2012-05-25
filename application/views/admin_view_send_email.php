<script type="text/javascript" src="<?php echo base_url();?>style/js/placeholder.js"></script>

<div class='admin-send-email'>
	<div id='send-email-heading'>
		Send email to User
	</div>

	<div id='send-email-area'>
		<form action="<?php echo base_url(); ?>index.php/admin/send_email/<?php echo $id1; ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
			<label>	Title </label>
			<input type="text"	name="title"></br>
			<label> Content </label>
			<textarea rows="3" name="content"></textarea></br>
			<input type="submit" name="submit" value="Send Email">
		</form>	
	</div>
</div>
