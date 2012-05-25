	<?php
		$user = $this->session->userdata('user');
		if (isset($user->email_address))
		{
			if ($user->admin_right == 1)
			{
				echo "<span class='logo' id='logo-admin'><a href='/'>The Gourmet</a></span>";
				echo "<ul class='nav' id='nav-admin'>";
			}
			else
			{
				echo "<span class='logo'><a href='".base_url()."'>The Gourmet</a></span>";
				echo "<ul class='nav'>";
			}
		}
		else
		{
			echo "<span class='logo'><a href='/'>The Gourmet</a></span>";
			echo "<ul class='nav'>";
		}
		echo "<li>".anchor('/', 'Home')."</li>";
		echo "<li>".anchor('/recipe/browse_recipe', 'Browse')."</li>";
		echo "<li>".anchor('/user/plan_meal', 'Plan Meal')."</li>";
		echo "<li>".anchor('/user/upload_recipe', 'Upload')."</li>";
		if (isset($this->session->userdata('user')->email_address))
		{
			echo "<li>".anchor('user/edit_profile', 'Edit Profile')."</li>";
			if ($user->admin_right == 1)
			{
				echo "<li>".anchor('admin/', 'Manage System')."</li>";
			}
			echo "<li>".anchor('user/log_out', 'Sign Out')."</li>";
		}
		else
			echo "<li>".anchor('user/sign_in', 'Sign In')."</li>";
		echo "</ul>";
	?>

	<div id="wrapper">