<div class="ma-wrapper">
	<div class="gourmet-title" style="border-bottom:1px solid black">The Gourmet</div>

	<?php
		$first_name = $this->session->userdata('user')->first_name;
		if (isset($first_name))
			echo "<h1>Hello ".$first_name."!</h1>";
		else
			echo "Hello ".$this->session->userdata('user')->email_address;

		if (isset($this->session->userdata['user']->admin_right))
		{
			if ($this->session->userdata['user']->admin_right == 1)
				echo "<h1>Welcome to admin mode.</h1>";
		}

		echo "Start using The Gourmet by clicking one of the icon below!";
	?>

	<div class="ma-river">
		<div class="ma-pic" style="margin-right:20px;">
			<a href="<?php echo base_url(); ?>index.php/user/plan_meal"><img src="<?php echo base_url(); ?>style/img/member/plan.png" alt="plan" width="128px;" height="128px";></a>
			<p>Plan</p>
		</div>

		<div class="ma-pic" style="margin-right:20px;">
			<a href="<?php echo base_url(); ?>index.php/recipe/browse_recipe"><img src="<?php echo base_url(); ?>style/img/member/search.png" alt="search" width="128px;" height="128px";></a>
			<p>Search</p>
		</div>

		<div class="ma-pic">
			<a href="<?php echo base_url(); ?>index.php/user/upload_recipe"><img src="<?php echo base_url(); ?>style/img/member/share.png" alt="share" width="128px;" height="128px";></a>
			<p>Share</p>
		</div>
	</div>

</div>