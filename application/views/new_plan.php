	<div class="gourmet-title">Meal Advisor</div>

	<div class="search">

		<div class="search-sidebar" style="width:250px; float:left; padding-top:30px; height:440px;">
			<ul>
				<li>My Information</li>
				<?php
				$gender = $this->session->userdata['user']->gender == 0 ? 'Female' : 'Male';
				$weight = $this->session->userdata['user']->weight;
				$height = $this->session->userdata['user']->height;
				$vegetarian = $this->session->userdata['user']->vegetarian == 0 ? 'No' : 'Yes';
				echo "<li>Gender: ".$gender."</li>";
				echo "<li>Weight: ".$weight."kg</li>";
				echo "<li>Height: ".$height."cm</li>";
				echo "<li>Vegetarian: ".$vegetarian."</li>";
				?>
				<li><a href="<?php echo base_url(); ?>index.php/user/edit_profile"  class="gh-button icon edit">Edit Profile</a></li>
				<li style="margin-top:20px;">My Previously Saved Plans</li>
				<li>
					<div>
						<div class="gh-button-group">
							<a href="#" class="gh-button" id="button1" onclick="switchPlan(1)">Plan 1</a>
							<a href="#" class="gh-button" id="button2" onclick="switchPlan(2)">Plan 2</a>
							<a href="#" class="gh-button" id="button3" onclick="switchPlan(3)">Plan 3</a>
						</div>
					</div>
				</li>
				<li>Choose another plan</li>
				<li>
					<div class="gh-button-group">
						<a href="<?php echo base_url(); ?>index.php/user/generate_plan" class="gh-button icon log">Generate a plan</a>
						<a href="#" onclick="save()" class="gh-button icon approve">Save this plan</a>
					</div>
				</li>

				<li style="font-weight:normal; font-size:0.8em; text-align:justify; padding-top:30px;">Disclaimer: All your information is private. The more information you provide, the more accurate our suggestions to you. <br/><br/>Happy dining!</li>
			</ul>
		</div>

		<div class="search-area" style="width:800px; padding-left:320px; margin-left:0px; padding-top:0px;">

			<!-- Generated Plan here -->
			<?php
				if (isset($new_plan) and count($new_plan) == 3)
				{
					if ($new_plan[0] == null || $new_plan[1] == null || $new_plan[2] == null)
					{
						echo '<div id="hide-me" style="min-height:800px;padding-top:20px;margin-bottom:40px;">';
						echo '<div class="empty">';
						echo '<p>Sorry, there is currently no suitable recipe for you</p>';
						echo '<img src="'.base_url().'style/img/empty_box.png" alt="empty">';
						echo '</div>';
						echo '</div>';
					}
					else
					{
						$count = 0;
						echo '<div id="hide-me">';
						echo '<p>I think you will like these recipes. Happy cooking and eating!</p>';
					 	foreach ($new_plan as $meal_index => $recipe)
					 	{
					 		$recipe_id = $recipe->recipe_id;
					 		$recipe_name = $recipe->recipe_name;
					 		$num_of_likes = $recipe->num_of_likes;
					 		$description = $recipe->description;
					 		$calories = $recipe->calories;

							if ($count == 0)
								$meal_time = 'Breakfast';
							else if ($count == 1)
								$meal_time = 'Lunch';
							else
								$meal_time = 'Dinner';

							echo '<div class="mp-recipe-row">';
							echo '<a href="'.base_url().'index.php/recipe/view_recipe/'.$recipe_id.'" title="'.$recipe_name.'">';
							echo '<div class="collage collage-pic-only">';
							echo '<img src="'.base_url().'media/recipe_imgs/'.$recipe_id.'.jpg" alt="" width="200" height="140">';
							echo '<div class="collage-text">';
							echo '<div class="collage-title">'.$recipe_name.'</div>';
							echo '<div class="rating">Likes: '.$num_of_likes.'</div>';
							echo '</div></div></a>';
							echo '<div class="mp-recipe-description-box">';
							echo '<div class="mp-recipe-time">'.$meal_time.'</div>';
							echo '<div class="mp-recipe-nutrients">Nutrients: '.$calories.'kCal</div>';
							echo '<div class="mp-recipe-short-description">'.$description.'</div>';
							echo '</div></div>';
							$count+=1;
						}
						echo '</div>';
					}
				}
			?>
			<!-- End  Generated Plan here -->

		<?php
			$plan_index = 1;
			foreach ($plans as $plan_i => $plan)
			{
				$count = 0;
				if ($plan == null)
				{
					echo '<div id="plan'.$plan_index.'" style="display:none;height:480px;">';
					echo '<div  class="empty"><p>This plan is empty. <br/>Click on "Generate a plan" on the left sidebar to get a new plan</p>';
					echo '<img src="'.base_url().'style/img/empty_box.png" alt="empty">';
					echo '</div>';
					echo '</div>';
					// echo '<div id="hide-me"></div>';
				}
				else
				{
				 	echo '<div id="plan'.$plan_index.'" style="display:none;">';
				 	foreach ($plan as $meal_index => $recipe)
				 	{
				 		$recipe_id = $recipe->recipe_id;
				 		$recipe_name = $recipe->recipe_name;
						if (strlen($recipe_name) > 34)
						{
							$recipe_name = substr($recipe_name, 0, 34)."...";
						}
				 		$num_of_likes = $recipe->num_of_likes;
				 		$description = $recipe->description;
				 		$calories = $recipe->calories;

				 		if ($count == 0)
				 			$meal_time = 'Breakfast';
				 		else if ($count == 1)
				 			$meal_time = 'Lunch';
				 		else
				 			$meal_time = 'Dinner';

					 	echo '<div class="mp-recipe-row">';
						echo '<a href="'.base_url().'index.php/recipe/view_recipe/'.$recipe_id.'" title="'.$recipe_name.'">';
						echo '<div class="collage collage-pic-only">';
						echo '<img src="'.base_url().'media/recipe_imgs/'.$recipe_id.'.jpg" alt="" width="200" height="140">';
						echo '<div class="collage-text">';
						echo '<div class="collage-title">'.$recipe_name.'</div>';
						echo '<div class="rating">Likes: '.$num_of_likes.'</div>';
						echo '</div></div></a>';
						echo '<div class="mp-recipe-description-box">';
						echo '<div class="mp-recipe-time">'.$meal_time.'</div>';
						echo '<div class="mp-recipe-nutrients">Nutrients: '.$calories.'kCal</div>';
						echo '<div class="mp-recipe-short-description">'.$description.'</div>';
						echo '</div></div>';
						$count+=1;
				 	}
				 	echo '</div>';
			 	}
			 	$plan_index += 1;
			 }
		?>

		</div>

		<script type="text/javascript">
		function switchPlan(index)
		{
			if (index == 1)
			{
				document.getElementById('hide-me').style.display = 'none';

				document.getElementById('plan1').style.display = 'block';
				document.getElementById('plan2').style.display = 'none';
				document.getElementById('plan3').style.display = 'none';

				document.getElementById("button1").setAttribute("class", "gh-button gh-active");
				document.getElementById("button2").setAttribute("class", "gh-button");
				document.getElementById("button3").setAttribute("class", "gh-button");
			}
			if (index == 2)
			{
				document.getElementById('hide-me').style.display = 'none';

				document.getElementById('plan1').style.display = 'none';
				document.getElementById('plan2').style.display = 'block';
				document.getElementById('plan3').style.display = 'none';

				document.getElementById("button1").setAttribute("class", "gh-button");
				document.getElementById("button2").setAttribute("class", "gh-button gh-active");
				document.getElementById("button3").setAttribute("class", "gh-button");
			}
			else if (index == 3)
			{
				document.getElementById('hide-me').style.display = 'none';

				document.getElementById('plan1').style.display = 'none';
				document.getElementById('plan2').style.display = 'none';
				document.getElementById('plan3').style.display = 'block';

				document.getElementById("button1").setAttribute("class", "gh-button");
				document.getElementById("button2").setAttribute("class", "gh-button");
				document.getElementById("button3").setAttribute("class", "gh-button gh-active");
			}
		}
		</script>

	<?php
	$recipe_ids = $new_plan[0]->recipe_id.'-'.$new_plan[1]->recipe_id.'-'.$new_plan[2]->recipe_id;
	echo '<script type="text/javascript">';
	echo 'function save(){';
	echo 'var name = prompt("Please enter a slot to save this plan (1, 2 or 3)","1");';
	echo 'while (true){';
	echo 'if (name != null && ((name == 1 ) || (name == 2) || (name == 3))){';
	echo 'window.location ="'.base_url().'index.php/user/save_plan/'.$recipe_ids.'/" + name;break;}';
	echo 'else name = prompt("Please enter a valid option (1, 2 or 3)","1");';
	echo '}}</script>';
	?>
