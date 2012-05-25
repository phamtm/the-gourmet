<div class="center-heading">Edit Profile</div>

<div id="page">

  <div class="header">

    <div class="footer">

      <div class="body">

        <div id="sidebar">

          <ul class="navigation">
           <li><?php echo anchor('user/edit_profile','Account Info','title="accountInfo_view"'); ?></li>
           <li><?php echo anchor('user/favorites','Favourites','title="favorites"'); ?></li>
           <li><?php echo anchor('user/uploaded','Uploaded Recipes','title="view_uploaded"'); ?></li>
         </ul>
        </div> <!-- end of sidebar -->

        <div class="search-area" style="float:none;">
		<div class="search-result">
			<?php
				if($records->num_rows() == 0){
					echo "<div style='width:100%; text-align:center; font-size:1.3em; margin-top:130px'>You didn't upload any recipe.</div>";
					echo "<div style='width:100%; text-align:center; font-size:1.3em; margin-top:130px'>Click <a href=".base_url()."index.php/user/upload_recipe>here</a> to upload recipe.</div>";
				}
				else
				{
				// loop through the result table
					foreach($records->result() as $row)
					{
						$img_url = base_url()."media/recipe_imgs/".$row->recipe_id.".jpg";
						$recipe_description = $row->description;
						$recipe_name = $title_name = $row->recipe_name;
						$num_of_likes = $row->num_of_likes;

						if (strlen($recipe_description) > 100)
						{
							$recipe_description = substr($recipe_description, 0, 100)."...";
						}
						if (strlen($recipe_name) > 30)
						{
							$recipe_name = substr($recipe_name, 0, 29)."...";
						}
						echo "<a href='".base_url()."index.php/recipe/view_recipe/".$row->recipe_id."' title='".$title_name."' />\r\n";
						echo "<div class='collage'>\r\n";
						echo "<img src='".$img_url."' alt='' width='200' height='140'>\r\n";
						echo "<div class='collage-text'>\r\n";
						echo "<div class='collage-title'>".$recipe_name."</div>\r\n";
						echo "<div class='rating'>Likes: ".$num_of_likes."</div>\r\n";
						echo "<div class='collage-description'>".$recipe_description."</div>\r\n";
						echo "</div>\r\n";
						echo "</div>\r\n";
						echo "</a>\r\n";

					}
					echo $this->pagination->create_links();
				}
			?>
		</div>
	</div>

      </div> <!-- end of body -->

    </div> <!-- end of footer -->

  </div> <!-- end of header -->

<script type="text/javascript">
function displayForm(formName)
{
  if (document.getElementById(formName).style.display != 'none')
    document.getElementById(formName).style.display = 'none';
  else
  {
    document.getElementById('form_name').style.display       = 'none';
    document.getElementById('form_email').style.display      = 'none';
    document.getElementById('form_gender').style.display     = 'none';
    document.getElementById('form_height').style.display     = 'none';
    document.getElementById('form_weight').style.display     = 'none';
    document.getElementById('form_preference').style.display = 'none';
    document.getElementById('form_password').style.display   = 'none';
    document.getElementById(formName).style.display          = 'block';
  }
}
</script>