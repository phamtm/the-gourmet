<script type="text/javascript" src="<?php echo base_url(); ?>style/js/combine.js"></script>

<div class="foodview">

	<div class="foodview-top-row">
		<div class="food-title">
			<?php echo ucwords($cur_recipe->recipe_name); ?>
		</div>
		<div style="text-align:center;padding-bottom:10px;font-weight:bold;">
			<?php
				echo '<span style="font-size:0.9em;color:orange;">Nutrient: '.$cur_recipe->calories.'Cal</span>';
				if ($cur_recipe->vegetarian == 1)
					 echo ' | <span style="font-size:0.9em;color:green;"> Vegetarian</span>';
			?>
		</div>

		<div class="fv-description">
			<?php echo ucfirst($cur_recipe->description); ?>
		</div>
	</div> <!-- end of foodview-top-row -->

	<div class="foodview-wrapper">
		<div class="foodview-left-wrapper">
			<div class="foodview-left-content">
				<?php
				echo "<a href=\"".base_url()."media/recipe_imgs/".$cur_recipe->recipe_id.".jpg\" toptions='title = , layout = dashboard, shaded = 1, overlayClose = 1' />";
				echo "<img src=\"".base_url()."media/recipe_imgs/".$cur_recipe->recipe_id.".jpg\" class=\"thumb\" alt=\"recipe\" width=\"500px\" height=\"313px\" />";
				echo "<div class=\"thumb-caption\">Click to View Picture</div>";
				echo "</a>";
				?>
			</div>
			<div class="social-box">
				<!-- facebook -->
				<div class="fb-like" data-href="<?php base_url().$_SERVER['REQUEST_URI'] ?>" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-action="recommend" data-font="lucida grande"></div>

				<!-- twitter -->
				<a href="https://twitter.com/share" class="twitter-share-button" data-hashtags="thegourmet">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

				<!-- favorite -->
				<?php
				if (isset($recipe_was_favorited))
				{
					if (!$recipe_was_favorited)
					{
						echo '<div class="favorite">';
						echo '<a href="'.base_url().'index.php/recipe/add_to_favorite/'.$this->uri->segment(3).'">';
						echo '<button class="cupid-green">';
						echo 'Add to favorite';

						echo '</button>';
						echo '</a>';
						echo '</div>';
					}
					else
					{
						echo '<div class="favorite">';
						echo '<button class="minimal">';
						echo 'Added to favorite';
						echo '</button>';
						echo '</div>';
					}
				}
				?>
				<div class="favorite" style="margin-right:8px;">
					<a href="http://maps.google.com/maps?f=d&amp;source=s_d&amp;saddr=50+Nanyang+Ave,+Singapore+639798+(Nanyang+Technological+University)&amp;daddr=nearest+supermarket&amp;geocode=FTiEFAAd6gsuBiEZgi3FAAi7uA%3BFciDFAAdywsuBiGHo3yjULMdYSkfT34NdQ_aMTFoOoe-JRqxvQ&amp;aq=0&amp;oq=nearest+super&amp;sll=1.341282,103.693814&amp;sspn=0.024155,0.042272&amp;hl=en&amp;mra=ls&amp;ie=UTF8&amp;t=m&amp;ll=1.34475,103.680995&amp;spn=0,0.00003&amp;output=embed" toptions="width = 1000, height = 500, type = iframe, title = Nearest Supermarket, layout = dashboard,overlayClose = 1,shaded = 1" id="element_1">
						<button class="cupid-green" style="width:80px;">Maps</button>
					</a>
				</div>
			</div>
		</div>

		<div class="foodview-right-wrapper">
			<div class="foodview-ingredient">
				<h1>Ingredients</h1>
				<ul>
					<?php
					$list_ingred = $cur_recipe->ingredients;
					$array_ingred = explode(PHP_EOL, $list_ingred);
					foreach ($array_ingred as $an_ingred)
					{
						echo "<li>".ucfirst($an_ingred)."</li>";
					}
					?>
				</ul>
			</div>

			<div class="foodview-col3-wrapper">
				<div class="foodview-col3">
					<h1>Step</h1>
					<ul>
						<?php
						$list_steps = $cur_recipe->steps;
						$array_steps = explode(PHP_EOL, $list_steps);
						foreach ($array_steps as $a_step)
						{
							echo "<li>".ucfirst($a_step)."</li>";
						}
						?>
					</ul>
				</div>
			</div>
		</div>
	</div> <!-- end of foodview-wrapper -->

	<div class="food-river">
		<?php
		$recipes = $records->result();
		if ($records->num_rows > 0)
		{
			echo "<h2 style='text-align:center; margin-bottom:10px;'>You may also like:</h2>";
			echo "<div class='food-river-content'>";
			foreach ($recipes as $a_recipe)
			{
				if ($a_recipe->recipe_id == $this->uri->segment(5))
					continue;
				$img_url = base_url()."media/recipe_imgs/thumbs/".$a_recipe->recipe_id.".jpg";
				echo "<a href='".base_url()."index.php/recipe/view_recipe/".$a_recipe->recipe_id."' title='".$a_recipe->recipe_name."'>";
				echo "<div class=\"food-river-box\"><img src=\"".$img_url."\" width=\"180\" height=\"100\" alt=\"\"></div>";
				echo "</a>";
			}
			echo "</div>";
		}
		?>
	</div> <!-- end of food-river -->

	<div style="width:100%; float:left; padding-top:40px;">
		<form class="form-wrapper cf" action="<?php echo base_url(); ?>index.php/recipe/search_recipe" method="post" accept-charset="utf-8" autocomplete="off">
			<input type="text" placeholder="Search here..." required onkeyup="getStates(thisValue)" name="query" value="<?php if(isset($cur_search_str)) echo $cur_search_str;?>" spellcheck="false">
			<button type="submit">Search</button>
		</form>
	</div> <!-- end of bottom search box -->

</div> <!-- end of foodview -->

<script type="text/javascript" src="http://gettopup.com/releases/latest/top_up-min.js"></script>