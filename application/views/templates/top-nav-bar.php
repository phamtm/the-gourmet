<div class="centered" id="header">
	<a id="logo" href="/index.php">The Gourmet</a>
	<ul>				
		<?php 			
			echo "<li>".anchor('home/index', 'Browse')."</li>";
			echo "<li>".anchor('home/index', 'Meal Planner')."</li>";
			if ($this->session->userdata('is_logged_in') or $this->session->userdata('is_logged_in') != null)
			{				
				echo "<li>".anchor('user/edit_profile', 'Edit Profile')."</li>"; 
				echo "<li>".anchor('user/log_out', 'Sign Out')."</li>";
			}
			else
				echo "<li>".anchor('user/sign_in', 'Sign In')."</li>";
		?>

		<!-- WELCOME BOX -->
		<!-- <div id="welcome">		 -->
			<?php 
				if ($this->session->userdata('is_logged_in'))
					echo "<li>Welcome ".$this->session->userdata('username')."</li>"
					// echo 'Welcome '.$this->session->userdata('username');
			?>
		<!-- </div> -->
	</ul>
</div><!-- End of header -->
