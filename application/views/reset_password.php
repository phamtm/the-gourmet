<div class="form-center">
	<h1>Reset Password</h1>
	<fieldset>
		<form action="/index.php/user/send_password_reset_code" method="post" accept-charset="utf-8" autocomplete="off">
			<label>New password:</label>
			<input type="password" name="new_pwd">
			<label>Old password:</label>
			<input type="password" name="confirm_pwd">
			<input type="submit" name="submit" value="Send Confirm Email">
		</form>
	</fieldset>
</div>