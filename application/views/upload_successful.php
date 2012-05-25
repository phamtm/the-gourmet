<div class="white-box">
	<div class="gourmet-title" style="font-size:3em;">Upload Successful</div>
	<br><br>Click below to view your recipe<br/><br/><br/>
	<?php
		$img_url = base_url()."/media/recipe_imgs/thumbs/".$recipe->recipe_id.".jpg";
		echo "<a href='".base_url()."index.php/recipe/view_recipe/".$recipe->recipe_id."' title='".$recipe->recipe_name."'>";
		echo '<div class="food-river-box" style="width:360px;height:200px;margin:0 auto;"><img src="'.$img_url.'" width="360" height="200"></div><br/><br/><br/>';
		echo '<span style="color:orange;">Calories: '.$recipe->calories.'Cal</span>';
		echo'</a>';
	?>
	<br/><br/><br>
</div>