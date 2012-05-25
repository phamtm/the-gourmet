<script type="text/javascript" src="<?php echo base_url();?>style/js/placeholder.js"></script>	

<div id="upload-area">
	<img src="<?php echo base_url();?>style/img/clip.png" alt="" style="position:absolute; margin-left:-40px; margin-top:-80px;">
	<div id="upload-title">
		Edit Your Recipe
	</div><!-- end of upload-title -->
		<?php // Get recipe details
			$this->load->database();
			$query = $this->db->query("SELECT * FROM recipes WHERE recipe_id=$id1");
			foreach($query->result() as $row);
		?>

	<div id="rec-desc">
		<form action="<?php echo base_url(); ?>index.php/admin/update_recipe/<?php echo $id1; ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
			<label>Recipe Name:</label>
			<input type="text" id="rec_name" name="recipe_name" size="85" value="<?php echo $row->recipe_name?>"/>
			<br>
			
			<label>Short Description (&lt; 300 words):</label>			
			<textarea rows="3" name="rec_desc"> <?php echo $row->description?></textarea>
			<br>
			<div>
				<span style="float:left;">
					<input type="radio" name="meal_time" value="0" /> Breakfast
					<input type="radio" name="meal_time" value="1" /> Lunch
					<input type="radio" name="meal_time" value="2" /> Dinner
				</span>

				<span style="float:right;">
					<input type="checkbox" name="vegetarian" value="vegeterian" />Vegetarian
					<input type="checkbox" name="halal" value="halal" />Halal
				</span>
				<br><br>
			</div>		

			Upload a picture: <input type="file" name="userfile" value>
			
			<br><br>

			<label>Ingredients</label>			
			<textarea rows="7" name="ingredients"><?php echo $row->ingredients?></textarea>
			<br>
			
			<label>Steps</label><br>			
			<textarea rows="10" name="steps"><?php echo $row->steps?></textarea>
			<br>

			<input type="submit" name="upload" value="Submit" style="margin-left:44%;" />
		</form>
	</div>	

</div><!-- end of upload-area -->

