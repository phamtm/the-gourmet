<?php

class Admin extends CI_Controller {

	// redirect to another page
	function redirect($uri)
	{
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			$data['main_content'] = $uri;
			$this->load->view('templates/template', $data);
		}
	}

	function index() {
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}
		$this->index_recipe();
	}
	//--------------------RECIPE SECTION------------------//
	function index_recipe()
	{
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}
		$this->load->model('admin_model');
		$records = $this->admin_model->get_recipes_ordered($this->uri->segment(3), 8);

		$this->load->library('pagination');
		$this->load->library('table');
		$config['base_url'] = base_url().'index.php/Admin/index_recipe';
		$config['total_rows'] = $this->db->get('recipes')->num_rows();
		$config['per_page'] = 8;
		$config['num_links'] = 3;
		$config['full_tag_open'] = '<div id="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		// $this->db->select('recipe_id, recipe_name, meal_time, description, num_of_likes');
		$data['records'] = $records;
		// $this->db->get('recipes', $config['per_page'], $this->uri->segment(3));
		$data['main_content']='admin_view_recipe';
		$this->load->view('templates/template',$data);
	}

	function make_admin($user_id)
	{
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}
		$this->load->model('admin_model');
		$this->admin_model->make_admin($user_id);
		$this->index_user();
	}

	function remove_admin($user_id)
	{
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}
		$this->load->model('admin_model');
		$this->admin_model->remove($user_id);
		$this->index_user();
	}


	function delete_recipe($recipe_id='') {
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}

		$id_delete=($recipe_id)?$recipe_id:$this->id_delete;

		$this->load->Model('admin_model');
		if ($this->admin_model->delete_recipe($id_delete)) {
			$data['deleted'] = TRUE;
			$data['main_content']='admin_view_recipe';
			$data['records']=$this->db->get('recipes');
			$date['id_deleted'] = $id_delete;
			$this->load->view('templates/template',$data);
		}
	}

	function edit_recipe($recipe_id='') {
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}
		//get parameter
		$id_edit=($recipe_id)?$recipe_id:$this->id_edit;

		//load view
		$data['id1'] = $id_edit;
		$data['main_content']='admin_view_edit_recipe';
		$this->load->view('templates/template',$data);
	}

	function update_recipe($recipe_id='') {
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}
		//load model
		$id_edit=($recipe_id)?$recipe_id:$this->id_edit;
		$this->load->Model('admin_model');
		if ($this->admin_model->edit_recipe($id_edit)) {
			$data['edited'] = TRUE;
			$data['main_content']='admin_view_recipe';
			$data['records']=$this->db->get('recipes');
			$this->load->view('templates/template',$data);
		}
	}

	//----------------USER SECTION-------------------//

	function index_user() {
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}
		$this->load->library('pagination');
		$this->load->library('table');
		$config['base_url'] = base_url().'index.php/Admin/index_user';
		$config['total_rows'] = $this->db->get('user_info')->num_rows();
		$config['per_page'] = 10;
		$config['num_links'] = 3;
		$config['full_tag_open'] = '<div id="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$data['records'] = $this->db->get('user_info', $config['per_page'], $this->uri->segment(3));
		$data['main_content']='admin_view_user';
		$this->load->view('templates/template',$data);
	}

	function delete_user($user_id='') {
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}

		$id_delete=($user_id)?$user_id:$this->id_delete;

		$this->load->model('admin_model');
		if ($this->admin_model->delete_user($id_delete)) {
			$data['deleted'] = TRUE;
			$data['main_content']='admin_view_user';
			$data['records']=$this->db->get('user_info');
			$this->load->view('templates/template',$data);
		}
	}

	function send_email_view($user_id) {
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}

		$id=($user_id)?$user_id:$this->id;
		//load view
		$data['main_content']='admin_view_send_email';
		$data['id1']=$id;
		$this->load->view('templates/template',$data);
	}

	function send_email($user_id) {
		if ($this->session->userdata['user']->admin_right ==  0)
		{
			redirect('home');
			return;
		}

		$id=($user_id)?$user_id:$this->id;
		//load model
		$this->load->model('admin_model');
		if ($this->admin_model->send_email($id)) {
			$data['sent_email']=TRUE;
			$data['main_content']='admin_view_user';
			$data['records']=$this->db->get('user_info');
			$this->load->view('templates/template',$data);
		}
	}
}