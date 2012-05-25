<div class="center-heading">Manage Recipes</div>

<div id="page">

	<div class="header">

		<div class="footer">

			<div class="body">

				<div id="sidebar">

					<ul class="navigation">
						<li><?php echo anchor('admin/index_user','Manager Users','title="manage_user"'); ?></li>
						<li><?php echo anchor('admin/index_recipe','Manage Recipes','title="manage_recipe"'); ?></li>
					</ul>
				</div> <!-- end of sidebar -->

				<div id="content">
					<div class="content">
<!-- 						<div class='admin-content'>
							<h2>Manage Recipes</h2>
						</div> -->
						<div class='deleted-notation'>
							<?php
							if (isset($deleted) and $deleted==TRUE) echo "Recipe Deleted!";
							if (isset($edited) and $edited==TRUE) echo "Recipe Edited!";
							echo "</div>";
							?>


						<div class='admin-table'><table border="1">
						<?php
						if (isset($records)) {
							$query = $records;
							echo "<th>ID</th> <th>Recipe Name</th> <th>Description</th> <th>Delete</th> <th>Edit</th>";
							foreach($query->result() as $row) {
								echo "<tr>";
								echo "<td>".$row->recipe_id."</td>";
								echo "<td id='edit-link' width=\"300\">".anchor('recipe/view_recipe/'.$row->recipe_id,$row->recipe_name)."</td>";
								echo "<td>".$row->description."</td>";
								echo "<td id='edit-link'>".anchor('admin/delete_recipe/'.$row->recipe_id,'Delete')."</td>";
								echo "<td id='edit-link'>".anchor('admin/edit_recipe/'.$row->recipe_id,'Edit')."</td>";
								echo "</tr>";
							}
						}
						echo "</table></div>";
						echo "<div class='pagination-link'>";
						echo $this->pagination->create_links();
						echo "</div>";
						?>

					</div>
				</div> <!-- end of content -->

			</div> <!-- end of body -->

		</div> <!-- end of footer -->

  </div> <!-- end of header -->