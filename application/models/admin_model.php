<?php
class Admin_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}

	function delete_recipe($recipe_id_delete) {
		//echo $recipe_id_delete;

		$this->load->database();
		$query = "DELETE FROM recipes WHERE recipe_id=$recipe_id_delete";

		return $this->db->query($query);
	}

	function edit_recipe($id_edit) {
		$new_recipe = array(
			'recipe_name' => $this->input->post('recipe_name'),
			'description' => $this->input->post('rec_desc'),
			'meal_time'   => $this->input->post('meal_time'),
			'ingredients' => $this->input->post('ingredients'),
			'steps'       => $this->input->post('steps')
			);
		$this->db->where('recipe_id',$id_edit);
		if (is_null($new_recipe['recipe_name'])) return false;
		$update = $this->db->update('recipes',$new_recipe);
		return $update;
	}

	function delete_user($user_id_delete) {
		$this->load->database();

		$query="DELETE FROM user_info WHERE user_id=$user_id_delete";

		return $this->db->query($query);
	}

	function send_email($id) {
		//get user's email address
		$this->db->where('user_id',$id);
		$query=$this->db->get('user_info');

		foreach($query->result() as $row) $email_address=$row->email_address;


		$config=Array(
			'protocol'=>'smtp',
			'smtp_host'=>'ssl://smtp.googlemail.com',
			'smtp_port'=>465,
			'smtp_user'=>'trungkienioicamp@gmail.com',
			'smtp_pass'=>'kienloan'
			);

		$this->load->library('email',$config);
		$this->email->from('trungkienioicamp@gmail.com', 'The Gourmet');
		$this->email->to($email_address);
		$this->email->subject($this->input->post('title'));
		$this->email->message($this->input->post('content'));
		if (is_null($this->input->post('title'))) return false;
		//echo $this->input->post('title');
		//echo $this->input->post('content');
		return $this->email->send();
	}

	function make_admin($user_id)
	{
		$this->db->where('user_id',$user_id);
		$query_data = array('admin_right' => 1);
		$update = $this->db->update('user_info', $query_data);
		return $update;
	}

	function remove_admin($user_id)
	{
		$this->db->where('user_id',$user_id);
		$query_data = array('admin_right' => 0);
		$update = $this->db->update('user_info', $query_data);
		return $update;
	}

	function get_recipes_ordered($start_index, $per_page)
	{
		if ($start_index == '')
			$start_index = '0';

		$query_str = "SELECT * FROM recipes ORDER BY recipe_id ASC LIMIT $start_index, $per_page ";
		$query_r = $this->db->query($query_str);
		return $query_r;
	}
}
?>