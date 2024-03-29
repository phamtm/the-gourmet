<?php
class Recipe extends CI_Controller
{
	function index()
	{
		$this->browse_recipe();
	}

	function search_recipe()
	{
		$config['total_rows']     = 6;
		$config['per_page']       = 6;
		$config['num_links']      = 3;
		$config['base_url']       = base_url().'index.php/recipe/search_recipe';
		$config['full_tag_open']  = '<div class="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$search_str = $this->input->post('query');
		$this->db->where("MATCH(recipe_name, description, ingredients, steps) AGAINST ('".$search_str."' IN BOOLEAN MODE)");
		$data['records'] = $this->db->get('recipes', $config['per_page'], $this->uri->segment(3));

		// load browse_recipe view
		$data['cur_search_str']	= $search_str;
		$data['main_content'] = 'search_recipe';
		$this->load->view('templates/template', $data);
	}

	function browse_recipe()
	{
		$config['total_rows']     = $this->db->get('recipes')->num_rows();
		$config['per_page']       = 6;
		$config['num_links']      = 3;
		$config['base_url']       = base_url().'index.php/recipe/browse_recipe';
		$config['full_tag_open']  = '<div class="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$data['records'] = $this->db->get('recipes', $config['per_page'], $this->uri->segment(3));
		$data['main_content'] = 'search_recipe';
		$this->load->view('templates/template', $data);
	}

	function top_rated()
	{
		$config['total_rows']     = $this->db->get('recipes')->num_rows();
		$config['per_page']       = 6;
		$config['num_links']      = 3;
		$config['base_url']       = base_url().'index.php/recipe/top_rated';
		$config['full_tag_open']  = '<div class="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$this->db->order_by("num_of_likes", "desc");
		$data['records'] = $this->db->get('recipes', $config['per_page'], $this->uri->segment(3));

		$data['main_content'] = 'search_recipe';
		$this->load->view('templates/template', $data);
	}

	function recent_upload()
	{
		$config['total_rows']     = $this->db->get('recipes')->num_rows();
		$config['per_page']       = 6;
		$config['num_links']      = 3;
		$config['base_url']       = base_url().'index.php/recipe/recent_upload';
		$config['full_tag_open']  = '<div class="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$this->db->from('uploaded', 'recipes');
		$this->db->join('recipes', 'recipes.recipe_id = uploaded.recipe_id');
		$this->db->order_by("time_uploaded", "desc");
		$this->db->limit($config['per_page'], $this->uri->segment(3));
		$data['records'] = $this->db->get();

		$data['main_content'] = 'search_recipe';
		$this->load->view('templates/template', $data);
	}

	function meal_time($q)
	{
		$q_int = 0;
		if ($q=="lunch") $q_int = 1;
		else if ($q=="dinner") $q_int = 2;

		$config['total_rows']     = $this->count_row_with_id('recipes', $q_int);
		$config['per_page']       = 6;
		$config['num_links']      = 3;
		$config['base_url']       = base_url().'index.php/recipe/meal_time';
		$config['full_tag_open']  = '<div class="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$this->db->where('meal_time', $q_int);
		$this->db->order_by("num_of_likes", "desc");
		$data['records'] = $this->db->get('recipes', $config['per_page'], $this->uri->segment(3));

		$data['main_content'] = 'search_recipe';
		$this->load->view('templates/template', $data);
	}

	function meal_type($q)
	{
		$meal_type = ($q == 'vegetarian') ? 'vegetarian' : 'halal' ;

		$config['total_rows']     = $this->db->get('recipes')->num_rows();
		$config['per_page']       = 6;
		$config['num_links']      = 3;
		$config['base_url']       = base_url().'index.php/recipe/meal_type';
		$config['full_tag_open']  = '<div class="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);

		$this->db->where($meal_type, 1);
		$this->db->order_by("num_of_likes", "desc");
		$data['records'] = $this->db->get('recipes', $config['per_page'], $this->uri->segment(3));

		$data['main_content'] = 'search_recipe';
		$this->load->view('templates/template', $data);

	}

	function init_pagination()
	{
		$config['total_rows']     = $this->db->get('recipes')->num_rows();
		$config['per_page']       = 6;
		$config['num_links']      = 5;
		$config['base_url']       = base_url().'index.php/home/browse_recipe';
		$config['full_tag_open']  = '<div class="pagination-link">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);
	}

	function view_recipe($recipe_id)
	{
		$query_str = '	SELECT * FROM recipes WHERE recipe_id = ?';
		$query_r = $this->db->query($query_str, $recipe_id)->row();

		// if recipe not exist, return to home page
		if ($query_r == null)
		{
					$data['main_content'] = 'home';
		$this->load->view('templates/template', $data);
			return;
		}

		$data['cur_recipe'] = $query_r;
		$this_recipe_str =  	 $query_r->recipe_name
							." ".$query_r->description
							." ".$query_r->steps;

		// select this recipe and similar recipes
		$query_str = '	SELECT *
						FROM recipes AS R
						WHERE R.recipe_id <> ? AND
							  MATCH(R.recipe_name, R.description, R.ingredients) AGAINST (? IN BOOLEAN MODE)
						ORDER BY R.num_of_likes DESC
						LIMIT 1,4';
		$query_data = array($recipe_id, $this_recipe_str);


		// check if this recipe was favorited by user
		if ($this->session->userdata('is_logged_in'))
		{
			$this->db->where('user_id', $this->session->userdata('user')->user_id);
			$this->db->where('recipe_id', $recipe_id);
			$was_favorited = $this->db->get('user_favorites')->num_rows() == 1;
			$data['recipe_was_favorited'] = $was_favorited;
		}

		// load display_recipe view

		$data['records'] =  $this->db->query($query_str, $query_data);

		$data['main_content'] = 'display_recipe';
		$this->load->view('templates/template', $data);
	}

	// add a recipe to favorite list
	function add_to_favorite($recipe_id)
	{
		$this->load->model('recipe_model');
		$user_id = $this->session->userdata('user')->user_id;
		$this->recipe_model->add_to_favorite($user_id, $recipe_id);
		$this->view_recipe($recipe_id);
	}
	//
	function unfavorite($recipe_id)
	{
		$id=($recipe_id)?$recipe_id:$this->id;

		$this->load->model('recipe_model');
		$user_id = $this->session->userdata('user')->user_id;
		$this->recipe_model->unfavorite($user_id, $id);

		$this->load->model('user_model');
		$data['records']=$this->user_model->get_favorites($user_id, $this->uri->segment(3), 4);
		$data['main_content']='view_favorites';
		$this->load->view('templates/template',$data);
	}

	function count_row_with_id($table, $meal_time)
	{
		$query_str = "SELECT * FROM ".$table." WHERE meal_time = $meal_time";
		$query_r = $this->db->query($query_str);
		return $query_r->num_rows();
	}
}
?>