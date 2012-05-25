<?php

	class User_model extends CI_Model
	{
		//===================== LOGGING IN LOGIC=========================//
		function validate_login()
		{
			$this->db->where('email_address', $this->input->post('email_address'));
			$this->db->where('password', md5($this->input->post('password')));
			$this->db->where('activated', 1);
			$query = $this->db->get('user_info');

			// one record is returned
			if ($query->num_rows() == 1)
				return true;
		}

		// return user_email_address, first_name, last_name, height, weight, gender
		function get_user($email_address)
		{
			$query_str     = "SELECT * FROM user_info WHERE email_address = ?";
			$query_r       = $this->db->query($query_str, $email_address);
			return $query_r;
		}

		//===================== REGISTRATION LOGIC=========================//
		// check validation code
		function confirm_registration($activation_code)
		{
			$query_str = "SELECT email_address FROM user_info WHERE activation_code = ?";
			$query_r = $this->db->query($query_str, $activation_code);
			if ($query_r->num_rows() == 1)
			{
				$query_str = "UPDATE user_info SET activated = 1 WHERE email_address = ?";
				$this->db->query($query_str, $query_r->row()->email_address);
				return true;
			}
			return false;
		}

		// add a new unauthenticated user to database
		// only if validation pass
		function register_user($email, $password, $activation_code, $password_reset_code, $isFB)
		{
			// register via facebook
			if ($isFB)
			{
				$account_info = array(
					'password'            => $password,
					'email_address'       => $email,
					'activation_code'     => $activation_code,
					'password_reset_code' => $password_reset_code,
					'activated'           => true,
					'admin_right'         => false,
				);
			}

			else
			{
				//1. Insert into user_account_info
				$account_info = array(
					'password'            => $password,
					'email_address'       => $email,
					'activation_code'     => $activation_code,
					'password_reset_code' => $password_reset_code,
					'activated'           => false,
					'admin_right'         => false,
				);
			}

			$insert_result = $this->db->insert('user_info', $account_info);
			return $insert_result;
		}

		// check if email exists
		function check_email_exist($email_address)
		{
		    $this->db->where('email_address',$email_address);
		    $query=$this->db->get('user_info');
		    if($query->num_rows()>0)
		        return true;
		    else
		        return false;
		}

		// creat a random string of predefined length
		function _random_string($length)
		{
			$this->load->helper('string');
			$len  = $length;
			$base = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
			$max  =strlen($base)-1;
			$activation_code = '';
			mt_srand((double)microtime()*1000000);
			while (strlen($activation_code)<$len+1)
				$activation_code.=$base{mt_rand(0, $max)};

			return $activation_code;
		}

		//===================== PASSWORD RESET LOGIC=========================//
		// return password_reset_code
		function get_password_reset_code($email_address)
		{
			$query_str = "SELECT password_reset_code FROM user_info WHERE email_address = ?";
			$r = $this->db->query($query_str, $email_address);

			if ($r->num_rows() == 1)
				foreach ($r->result() as $row)
					$pwd = $row->password_reset_code;
			return $pwd;
		}

		// check password_reset_code against db
		function confirm_reset_password($reset_code)
		{
			$query = "SELECT user_id FROM user_info WHERE password_reset_code = ?";
			$query_r = $this->db->query($query, $reset_code);

			if ($query_r->num_rows() == 1)
			{
				$user_id = $query_r->row()->user_id;
				$query = "UPDATE user_info SET password_reset_code = ? WHERE user_id = ?";
				$new_reset_code = $this->_random_string(22);
				$data = array('password_reset_code' => $new_reset_code, 'user_id' => $user_id);
				$this->db->query($query, $data);
				return true;
			}

			return false;
		}

		//===================== UPDATE PROFILE LOGIC=========================//
		function update_profile($form_name, $user_id)
		{
			switch ($form_name) {
				case 'form_name':
					$first_name = ucwords($this->input->post('first_name'));
					if (isset($first_name))
						$this->update_a_field($user_id, 'first_name', $first_name);
					$last_name = ucwords($this->input->post('last_name'));
					if (isset($last_name))
						$this->update_a_field($user_id, 'last_name', $last_name);
					break;

				case 'form_email':
					$email_address = $this->input->post('email_address');
					if (isset($email_address))
						$this->update_a_field($user_id, 'email_address', $email_address);
					break;

				case 'form_gender':
					$gender = $this->input->post('gender');
					if (isset($gender))
						$this->update_a_field($user_id, 'gender', $gender);
					break;

				case 'form_height':
					$height = $this->input->post('height');
					if (isset($height))
						$this->update_a_field($user_id, 'height', $height);
					break;

				case 'form_weight':
					$weight = $this->input->post('weight');
					if (isset($weight))
						$this->update_a_field($user_id, 'weight', $weight);
					break;

				case 'form_preference':
					$pref = $this->input->post('preference');
					// vegetarian
					if (isset($pref[0])&&$pref[0]=='v')
						$this->update_a_field($user_id, 'vegetarian', 1);
					else
						$this->update_a_field($user_id, 'vegetarian', 0);
					// halal
					if (isset($pref[1])&&$pref[1]=='h')
						$this->update_a_field($user_id, 'halal', 1);
					else
						$this->update_a_field($user_id, 'halal', 0);
					break;

				case 'form_password':
					$old_pwd = $this->input->post('old_pwd');
					$new_pwd = md5($this->input->post('new_pwd'));
					$conf_pwd = $this->input->post('conf_pwd');
					if (isset($new_pwd) && isset($old_pwd) && $new_pwd == $conf_pwd)
						$this->update_a_field($user_id, 'password', $new_pwd);
					else echo "invalid";
					break;

				default:
					break;
			}
		}

		function update_a_field($user_id, $field_name, $field_value)
		{
			$query_str = 'UPDATE user_info SET '.$field_name.' = ? WHERE user_id = ?';
			$query_data = array($field_value, $user_id);
			$query_success = $this->db->query($query_str, $query_data);
		}

		//-----------------Kien-------------------------------//
		function get_favorites($user_id, $start_index, $per_page)
		{
			if ($start_index == '')
				$start_index = '0';
			$records = $this->db->query("SELECT *
									     FROM recipes
									     WHERE recipe_id IN (SELECT recipe_id
									 					     FROM user_favorites
									 					     WHERE user_id = $user_id)
										 LIMIT $start_index, $per_page
									");
			return $records;
		}
		//-----------------Kien-------------------------------//
		function get_uploaded($user_id, $start_index, $per_page)
		{
			if ($start_index == '')
				$start_index = '0';
			$records = $this->db->query("SELECT *
									     FROM recipes
									     WHERE recipe_id IN (SELECT recipe_id
									 					     FROM uploaded
									 					     WHERE user_id = $user_id)
										 LIMIT $start_index, $per_page
									");
			return $records;
		}

		function get_plans($user_id)
		{
			$this->db->where('user_id', $user_id);
			$query_r = $this->db->get('user_plan');
			if ($query_r->num_rows() == 0)
				return null;
			return $query_r->row();
		}

		function save_plan($user_id, $recipe_ids, $plan_no)
		{
			$this->db->where('user_id', $user_id);
			$query_r = $this->db->get('user_plan');
			if ($query_r->num_rows() == 0)
			{
				if ($plan_no == 1)
					$new_plan = array('user_id' => $user_id, 'plan1' => $recipe_ids);
				else if ($plan_no == 2)
					$new_plan = array('user_id' => $user_id, 'plan2' => $recipe_ids);
				else
					$new_plan = array('user_id' => $user_id, 'plan3' => $recipe_ids);
				$this->db->insert('user_plan', $new_plan);
			}
			else
			{
				if ($plan_no == 1)
					$new_plan = array('user_id' => $user_id, 'plan1' => $recipe_ids);
				else if ($plan_no == 2)
					$new_plan = array('user_id' => $user_id, 'plan2' => $recipe_ids);
				else
					$new_plan = array('user_id' => $user_id, 'plan3' => $recipe_ids);
				$this->db->update('user_plan', $new_plan);
			}
		}
	}
?>