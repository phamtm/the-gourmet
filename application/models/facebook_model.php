<?php
class Facebook_model extends CI_model{
	  function parse_signed_request($signed_request, $secret) {
		list($encoded_sig, $payload) = explode('.', $signed_request, 2);
		// decode the data
		$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
		$data =json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
		  error_log('Unknown algorithm. Expected HMAC-SHA256');
		  return null;
		}

		// check sig
		$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
		if ($sig !== $expected_sig) {
		  error_log('Bad Signed JSON signature!');
		  return null;
		}

		return $data;
	  }
	  function validate_facebook_login($email_address,$password){
		  $this->db->where('email_address', $email_address);
			$this->db->where('password', $password);
			$this->db->where('activated', 1);
			$query = $this->db->get('user_info');

			// one record is returned
			if ($query->num_rows() == 1)
				return true;
	}
	function register_facebook_user($password,$email,$first_name,$last_name,$gender,$dob){
		$account_info= array(
				'password'            => $password,
				'email_address'       => $email,
				'activated'           => true,
				'admin_right'         => false,
				'first_name'          => $first_name,
				'last_name'           => $last_name,
				'dob'				  => $dob,
				'gender'			  =>$gender,
		);
		$insert_result = $this->db->insert('user_info', $account_info);
		return $insert_result;
	}
}
?>
