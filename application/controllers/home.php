<?php
class Home extends CI_Controller {
	function index()
	{
		// $this->load->model('recipe_model');
		// echo $this->recipe_model->cal_calories("2 pounds ground pork");
		// $temp = array(1,null,2);
		// var_dump(count($temp));
		$data['main_content'] = 'home';
		$this->load->view('templates/template', $data);
	}
}
?>