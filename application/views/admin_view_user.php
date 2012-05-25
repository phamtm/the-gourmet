<div class="center-heading">Manage Users</div>

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
						<?php
						// echo "<div class='admin-content'>";
						// echo "User Manager";
						// echo "</div>";
						echo "<div class='deleted-notation'>";
						if (isset($deleted) and $deleted==TRUE) echo "User Deleted!";
						if (isset($sent_email)and $sent_email==TRUE) echo "Email Sent!";
						echo "</div>";
						?>

						<?php
						echo "<div class='admin-table'><table>";
						$query = $records;
						echo "<th>id</th> <th>Email Address</th> <th>Send Email</th> <th>Delete</th>";
						foreach($query->result() as $row) {
							echo "<tr>";
							echo "<td>".$row->user_id."</td>";
							echo "<td width=\"100\">".$row->email_address."</td>";
							echo "<td id='edit-link'>".anchor('admin/send_email_view/'.$row->user_id,'Send Email')."</td>";
							echo "<td id='edit-link'>".anchor('admin/delete_user/'.$row->user_id,'Delete User')."</td>";
		// if ($row->admin_right == 0)
		// 	echo "<td id='edit-link'>".anchor('admin/make_admin/'.$row->user_id,'Make Admin')."</td>";
		// else
		// 	echo "<td id='edit-link'>".anchor('admin/remove_admin/'.$row->user_id,'Remove Admin')."</td>";
							echo "</tr>";
						}
						echo "</table></div>";
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