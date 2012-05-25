<?php
	define('FACEBOOK_APP_ID', '222667541173410');
	define('FACEBOOK_SECRET', '53c7226c15fcc0f7faf5ead2cacd8e99');

	class User extends CI_Controller {

		function index() {
			$data['main_content'] = "member_area";
			$this->load->view('templates/template', $data);
		}

		//=====================LOG IN FUNCTION===========================/
		function sign_in()
		{
			if ($this->session->userdata('is_logged_in'))
			{
				$data['main_content']='home';
				$this->load->view('templates/template', $data);
				return;
			}

			$data['main_content'] = 'sign_in';
			$this->load->view('templates/template', $data);
		}

		function facebookLogin(){
			$data['page_title']='Facebook Login';
			$data['main_content']='facebookLogin';
			$this->load->view('templates/template', $data);
		}

		function facebookLoggedIn(){
			$this->load->model('facebook_model');
			$this->load->model('user_model');
			if ($_REQUEST) {
				//Get facebook user information through signed_request
			  $response = $this->facebook_model->parse_signed_request($_REQUEST['signed_request'],
											   FACEBOOK_SECRET);
			  $password=md5($response['registration']['password']);
			  $email_address=$response['registration']['email'];
			  $first_name=$response['registration']['first_name'];
			  $last_name=$response['registration']['last_name'];
			  $gender=$response['registration']['gender']=='male'?1:0;
			  $dob_delimiter=explode('/',$response['registration']['birthday']);
			  $dob=$dob_delimiter[2].'-'.$dob_delimiter[0].'-'.$dob_delimiter[1];

			  if(! $this->user_model->check_email_exist($email_address)){

				  $this->facebook_model->register_facebook_user($password, $email_address,$first_name, $last_name,$gender,$dob);
				  $data = array(
					'user'           => $this->user_model->get_user($email_address)->row(),
					'is_logged_in'   => TRUE
					);

				  $this->session->set_userdata($data);
				  $data['main_content'] = 'member_area'; // redirect the browser to the page that request this function
				  $this->load->view('templates/template', $data);
			  }else{
				if($this->facebook_model->validate_facebook_login($email_address,$password)){
					$data = array(
					'user'           => $this->user_model->get_user($email_address)->row(),
					'is_logged_in'   => TRUE
					);
				  $this->session->set_userdata($data);
				  $data['main_content'] = 'member_area'; // redirect the browser to the page that request this function
				  $this->load->view('templates/template', $data);
				}else{
				  $data['page_title']='Facebook Login';
				  $data['main_content']='facebookLogin';
				  $this->load->view('templates/template', $data);
				}
			  }
			  } else {
			  echo '$_REQUEST is empty';}
		}

		function validate_login()
		{
			$user = $this->session->userdata('user');
			if (isset($user->email_address))
			{
				$this->index();
				return;
			}
			$this->load->model('user_model');
			$q = $this->user_model->validate_login();
			if ($q)
			{
				$data = array(
					'user'  => $this->user_model->get_user($this->input->post('email_address'))->row(),
					'is_logged_in'   => TRUE
					);

				// Store user info in $_SESSION
				$this->session->set_userdata($data);

				$data['main_content'] = 'member_area'; // redirect the browser to the page that request this function
				$this->load->view('templates/template', $data);
			}
			else
			{
				$data['id_pwd_not_matched'] = TRUE;
				$data['main_content'] = 'sign_in';
				$this->load->view('templates/template', $data);
			}
		}


		//=====================LOGGING OUT FUNCTION===========================//
		function log_out()
		{
	        $this->session->sess_destroy();
	        session_unset();
	        redirect('home');
		}

		//=====================SIGN UP FUNCTION===========================//
		function sign_up()
		{
			$data['page_title'] = 'Sign Up';
			$data['main_content'] = 'sign_up';
			$this->load->view('templates/template', $data);
		}

		function add_user()
		{
			$this->load->model('user_model');

			$this->form_validation->set_rules('email_address', 'Email', 'required|valid_email|is_unique[user_info.email_address]');
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
			$this->form_validation->set_rules('passconf', 'Confirm Password', 'required|matches[password]');


			if ($this->form_validation->run() == FALSE)
			{
				$data['main_content'] = 'sign_up';
				$this->load->view('templates/template', $data);
			}

			else
			{
				// create new member
				$email_address       = $this->input->post('email_address');
				$password            = md5($this->input->post('password'));
				$activation_code     = $this->user_model->_random_string(22);
				$password_reset_code = $this->user_model->_random_string(22);

				// send activation email
				$this->send_activation_email($email_address, $activation_code);
				if($this->user_model->register_user($email_address, $password, $activation_code, $password_reset_code))
				{
					// save user data for current session
					$data = array($email_address);

					$this->session->set_userdata($data);
					$data['main_content'] = 'sign_up_successful';
					$this->load->view('templates/template', $data);
				}
				else
				{
					// registration not successful - reload the page
					$this->load->view('sign_up');
				}
			}
		}

		//=====================ACCOUNT ACTIVATION=========================//
		function activation_confirm()
		{
			$activation_code = $this->uri->segment(3);

			if ($activation_code == '')
			{
				echo 'Error no registration code in URL';
				exit();
			}

			$this->load->model('user_model');
			$q = $this->user_model->confirm_registration($activation_code);
			if ($q)
			{
				$data['page_title'] = 'Thank You For Registering';
				$data['main_content'] = 'account_activated';
				$this->load->view('templates/template', $data);
			}

			// WHEN INVALID ACTIVATION LINK
			else
				echo "Please go to your email and click on the link again";
		}

		function send_activation_email($email_address, $activation_code)
		{
			//send activation emails
			$this->email->from('phamminh91@gmail.com', 'The Gourmet');
			$this->email->to($email_address);
			$this->email->subject('Registration Confirmation');
			$this->email->message('Please click this link to confirm your registration'
								. anchor('/user/activation_confirm/' . $activation_code, 'Confirm Registration'));
			$this->email->send();
		}

		//===================== PASSWORD RESET =========================//
		function forgot_password()
		{
			$data['page_title'] = 'Reset Password';
			$data['main_content'] = 'forgot_password';
			$this->load->view('templates/template', $data);
		}

		function send_password_reset_code()
		{
			$email_address = $this->input->post('email_address');

			$this->load->model('user_model');
			$pwd_code = $this->user_model->get_password_reset_code($email_address);
			if ($pwd_code != null)
			{
				$this->email->from('phamminh91@gmail.com', 'The Gourmet');
				$this->email->to($email_address);
				$this->email->subject('Password Reset Confirmation');
				$this->email->message('If you have not asked our system to reset your password, please ignore this email. Otherwise, please click this link to proceed to reset your password'
							  .anchor('/user/confirm_reset/'.$email_address."/".$pwd_code, "<br /> Reset Password.")." Thank you!");
				$this->email->send();
			}
			else
				$this->forgot_password();
		}

		function confirm_reset()
		{
			$email_address = $this->uri->segment(3);
			$reset_code = $this->uri->segment(4);

			if ($reset_code == '')
			{
				echo 'Error no reset code in URL';
				exit();
			}

			$this->load->model('user_model');

			$q = $this->user_model->confirm_reset_password($reset_code);
			if ($q)
			{
				$data['main_content'] = 'reset_password'.$email_address;
				$this->load->view('templates/template', $data);
			}
		}

		function reset_password($email_address)
		{
			$new_pwd = $this->input->post('new_pwd');
			$confirm_pwd = $this->input->post('confirm_pwd');
			if ($new_pwd == $confirm_pwd)
			{
				$query_str = 'UPDATE user_info SET password = ? WHERE email_address = ?';
				$query_data = array($new_pwd, $email_address);
				$this->db->query($query, $query_data);
			}
		}

		//===================== UPDATE PROFILE =========================//
		function edit_profile()
		{
			if ($this->session->userdata('is_logged_in'))
			{
				// load current user's info
				$this->load->model('user_model');
				$email_address = $this->session->userdata('user')->email_address;
				$user          = $this->user_model->get_user($email_address)->row();
				$this->session->set_userdata('user', $user);

				// load view
				$data['page_title']   = 'Profile';
				$data['main_content'] = 'edit_profile';
				$this->load->view('templates/template', $data);
			}
		}

		function update_profile($form_name)
		{
			$this->load->model('user_model');
			switch ($form_name) {
				case 'form_name':
					$this->form_validation->set_rules('first_name', 'First name', 'required|min_length[2]');
					$this->form_validation->set_rules('last_name', 'Last name', 'required|min_length[1]');
					if ($this->form_validation->run())
						$this->user_model->update_profile($form_name, $this->session->userdata('user')->user_id);
					$this->edit_profile();
					break;

				case 'form_email':
					$this->form_validation->set_rules('email_address', 'Email', 'required|valid_email|is_unique[user_info.email_address]');
					if ($this->form_validation->run())
						$this->user_model->update_profile($form_name, $this->session->userdata('user')->user_id);
					$this->edit_profile();
					break;

				case 'form_gender':
					$this->form_validation->set_rules('gender', 'Gender', 'required');
					if ($this->form_validation->run())
						$this->user_model->update_profile($form_name, $this->session->userdata('user')->user_id);
					$this->edit_profile();
					break;

				case 'form_height':
					$this->form_validation->set_rules('height', 'Height', 'required');
					if ($this->form_validation->run())
						$this->user_model->update_profile($form_name, $this->session->userdata('user')->user_id);
					$this->edit_profile();
					break;

				case 'form_weight':
					$this->form_validation->set_rules('weight', 'Weight', 'required');
					if ($this->form_validation->run())
						$this->user_model->update_profile($form_name, $this->session->userdata('user')->user_id);
					$this->edit_profile();
					break;

				case 'form_preference':
					$vege_value = 0; //unchecked

					if(isset($_POST['preference']))
					{
						$value = $_POST['preference'];
						if ($value=='vegetarian')
							$vege_value = 1;
					}
					$this->user_model->update_a_field($this->session->userdata('user')->user_id, 'vegetarian', $vege_value);
					$this->edit_profile();
					break;

				default:
					$this->edit_profile();
					break;
			}
		}

		//===================== UPLOAD RECIPE =========================//
		function upload_recipe()
		{
			if ($this->session->userdata('is_logged_in'))
			{
				$data['main_content'] = 'upload_recipe';
				$this->load->view('templates/template', $data);
			}
			else
			{
				$data['main_content'] = 'sign_in';
				$this->load->view('templates/template', $data);
			}
		}

		function create_recipe()
		{
			$this->load->model('recipe_model');

			$this->form_validation->set_rules('name', 'Recipe\'s name', 'required|min_length[6]');
			$this->form_validation->set_rules('description', 'Description', 'required|min_length[30]|max_length[300]');
			$this->form_validation->set_rules('ingredients', 'Ingredients', 'required|min_length[30]');
			$this->form_validation->set_rules('steps', 'Steps', 'required|min_length[30]');

			if ($this->form_validation->run() == FALSE)
			{
				$data['main_content'] = 'upload_recipe';
				$this->load->view('templates/template', $data);
			}
			else
			{
				$recipe_id=$this->recipe_model->create_recipe();
				$this->db->select('recipe_id, recipe_name, meal_time, description, num_of_likes,calories');
				$this->db->where('recipe_id', $recipe_id);

				$data['recipe'] = $this->db->get('recipes')->row();
				$data['main_content'] = 'upload_successful';
				$this->load->view('templates/template', $data);
			}
		}

		//-------------------------Get favorites---------------------------//
		function favorites()
		{
			//get records
			$user_id = $this->session->userdata('user')->user_id;
			$this->load->model('user_model');
			$records = $this->user_model->get_favorites($user_id, $this->uri->segment(3), 4);

			//config pagination
			$config['total_rows']     = $this->count_row_with_id('user_favorites', $user_id);
			$config['per_page']       = 4;
			$config['num_links']      = 3;
			$config['base_url']       = base_url().'index.php/user/favorites';
			$config['full_tag_open']  = '<div class="pagination-link">';
			$config['full_tag_close'] = '</div>';
			$this->pagination->initialize($config);

			$data['records'] = $records;
			$data['main_content'] = 'view_favorites';
			$this->load->view('templates/template',$data);
		}
		//-------------------------Uploaded---------------------------//
		function uploaded()
		{
			//get records
			$user_id = $this->session->userdata('user')->user_id;
			$this->load->model('user_model');
			$records=$this->user_model->get_uploaded($user_id, $this->uri->segment(3), 4);

			//config pagination
			$this->load->library('pagination');
			$config['total_rows']     = $this->count_row_with_id('uploaded', $user_id);
			$config['per_page']       = 4;
			$config['num_links']      = 3;
			$config['base_url']       = base_url().'index.php/user/uploaded';
			$config['full_tag_open']  = '<div class="pagination-link">';
			$config['full_tag_close'] = '</div>';
			$this->pagination->initialize($config);

			$data['records']=$records;
			$data['main_content']='view_uploaded';
			$this->load->view('templates/template',$data);
		}

		/*=============== PLAN MEAL ==============*/
		function plan_meal()
		{
			if (isset($this->session->userdata('user')->email_address))
			{
				$data['plans'] = $this->get_all_plans();
				$data['main_content'] = 'plan_meal';
				$this->load->view('templates/template', $data);
			}
			else
			{
				$data['main_content'] = 'sign_in';
				$this->load->view('templates/template', $data);
			}
		}

		function generate_plan()
		{
			$this->load->model('recipe_model');
			$indices = $this->recipe_model->recipes_suggestion($this->session->userdata['user']);
			$data['new_plan'] = $this->get_generated_plan($indices);
			$data['plans'] = $this->get_all_plans();
			$data['main_content'] = 'new_plan';
			$this->load->view('templates/template', $data);
			// var_dump($data['new_plan']);
		}

		function get_saved_plans()
		{
			$this->load->model('user_model');
			$query_r = $this->user_model->get_plans($this->session->userdata('user')->user_id);

			if ($query_r == null)
				return $query_r;

			$temp = array('plan1' => array_map('trim',explode("-", $query_r->plan1)),
					  'plan2' => array_map('trim',explode("-", $query_r->plan2)),
					  'plan3' => array_map('trim',explode("-", $query_r->plan3)));

			return $temp;
		}

		function get_recipe($recipe_id)
		{
			$this->load->model('recipe_model');
			return $this->recipe_model->get_recipe($recipe_id);
		}

		function get_plan_from_indices($index)
		{
			if (count($index) == 3)
			{
				return $plan = array($this->get_recipe($index[0]),
			   		$this->get_recipe($index[1]),
			   		$this->get_recipe($index[2]));
			}

			return null;
		}

		function get_all_plans()
		{
			if (isset($this->session->userdata('user')->email_address))
			{
				// get recipes' ids of each plan
				$plans_i = $this->get_saved_plans();
				$plan1_i= $plans_i['plan1'];
				$plan2_i= $plans_i['plan2'];
				$plan3_i= $plans_i['plan3'];

				// store each recipe in the plan
				$plan1 = $this->get_plan_from_indices($plan1_i);
				$plan2 = $this->get_plan_from_indices($plan2_i);
				$plan3 = $this->get_plan_from_indices($plan3_i);

				return array($plan1, $plan2, $plan3);
			}
		}

		function get_generated_plan($index)
		{
			if (count($index) == 3)
			{
				return $plan = array($this->get_recipe($index['breakfast']),
			   		$this->get_recipe($index['lunch']),
			   		$this->get_recipe($index['dinner']));
			}

			return null;
		}

		// save the current plan
		function save_plan($recipe_ids, $plan_no)
		{
			$this->load->model('user_model');
			$this->user_model->save_plan($this->session->userdata('user')->user_id, $recipe_ids, $plan_no);

			$data['plans'] = $this->get_all_plans();
			$data['main_content'] = 'plan_meal';
			$this->load->view('templates/template', $data);
		}

		function count_row_with_id($table, $user_id)
		{
			$query_str = "SELECT * FROM ".$table." WHERE user_id = $user_id";
			$query_r = $this->db->query($query_str);
			return $query_r->num_rows();
		}
	}
?>