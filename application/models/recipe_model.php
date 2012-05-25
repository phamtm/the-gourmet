<?php
class Recipe_model extends CI_Model
{
	var $gallery_path;
	var $gallery_path_thumb;
	var $gallery_path_url;

	function __construct()
	{
		parent::__construct();
		$this->gallery_path = realpath(APPPATH . '../media/recipe_imgs/');
		$this->gallery_path_url = base_url().'media/recipe_imgs/';
	}

	//===================== CREATE A NEW RECIPE ========================/
	function create_recipe()
	{
		$vege_value = 0; //unchecked

		if(isset($_POST['vegetarian'])) {
			$value = $_POST['vegetarian'];
			if ($value=='vegetarian')
				$vege_value = 1;
		}

		$calories = $this->input->post('num_cal');
		if ($calories == 0)
			$calories = $this->cal_calories($this->input->post('ingredients'));

		$new_recipe = array(
			'recipe_name' => $this->input->post('name'),
			'description' => $this->input->post('description'),
			'meal_time'   => $this->input->post('meal_time'),
			'vegetarian'  => $vege_value,
			'ingredients' => $this->input->post('ingredients'),
			'steps'       => $this->input->post('steps'),
			'calories' => $calories
			);

		$insert = $this->db->insert('recipes',$new_recipe);
		$cur_id = $this->db->insert_id();
		$this->upload_image($cur_id);

		// insert into uploaded table
		$upload_data = array(
			'user_id' => $this->session->userdata('user')->user_id,
			'recipe_id' => $cur_id,
			'time_uploaded' => $this->get_current_time(),
			);
		$this->db->insert('uploaded', $upload_data);

		return $cur_id;
	}

	function get_recipe($recipe_id)
	{
		$query_r = $this->db->get_where('recipes', array('recipe_id' => $recipe_id));
		if ($query_r->num_rows() == 0)
			return;

		return $query_r->row();
	}

	function get_recipe_id($recipe_name) {
		$query_str = 'SELECT recipe_id FROM recipes WHERE recipe_name = ?';
		$result = $this->db->query($query_str, $recipe_name);

		if ($result->num_rows == 1)
		{
			foreach ($result->result() as $row)
				$recipe_id = $row->recipe_id;
			return $recipe_id;
		}
	}

	function upload_image($recipe_id) {
		// upload images
		$config = array(
			'allowed_types' => 'jpg|jpeg|gif|png|bmp',
			'upload_path'   => $this->gallery_path,
			'file_name'     => $recipe_id,
			'overwrite'		=> TRUE,
			'max_size'      => 8000
			);

		$this->load->library('upload', $config);
		$q = $this->upload->do_upload(); 				// do upload

		$image_data = $this->upload->data(); 		//get image data
		// create a thumbnail
		$config = array(
			'source_image'    => $image_data['full_path'],
			'new_image'       => $this->gallery_path . '/thumbs',
			'maintain_ration' => true,
			'width'           => 500,
			'height'          => 313,
			);

		$this->load->library('image_lib', $config);
		$this->image_lib->resize();

		//resize original picture
		/*$config['source_image'] = $this->upload->upload_path.$this->upload->file_name;
        $config['maintain_ratio'] = FALSE;
        $config['width'] = 1024;
        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
		*/
	}


	function get_images($id)
	{
		$files = scandir($this->gallery_path);

		$files = array_diff($files, array('.', '..', 'thumbs'));

		$images = array();

		foreach ($files as $file) {
			$images []= array (
				'url' => $this->gallery_path_url . $file,
				'thumb_url' => $this->gallery_path_url . 'thumbs/' . $file
				);
		}

		return $images;
	}

	// return a timestamp YYYY-MM-DD HH:MM:SS
	function get_current_time()
	{
		$this->load->helper('date');
		$datestring = "%Y-%m-%d %h:%m:%s";
		$timezone = 'UP8';
		$upload_time = mdate($datestring, gmt_to_local(time(), $timezone, FALSE));

		return $upload_time;
	}

	//======================= GET NUTRITIENTS INFO ==========================/
	function cal_calories($ingredients)
	{
		$ingred_list = explode('\n\r',trim($ingredients));
		//print_r($ingred_list);
		$total_cal = 0;
		foreach($ingred_list as $string)
		{
			$ingredient = explode(' ',$string);
			$quantity=0;
			$element=0;
			for(;$element<count($ingredient) &&floatval($ingredient[$element]) !=0;$element++ ){
				$math_result = $this->calculate_string($ingredient[$element]);
				$quantity += floatval($math_result);
			}
			//get the rest of the string;
			if($element<count($ingredient)){
				$unit = $ingredient[$element];//get unit
				$element++;}
			if($element<count($ingredient)){
				if($ingredient[$element]=='of') $element++;
				$ingred=array_slice($ingredient,$element,count($ingredient)-$element);
				$temp_cal = $this->get_ingredient($quantity,$unit,$ingred);
				$total_cal += ($temp_cal==0? 50 : $temp_cal);
			}
		}
		return $total_cal;
	}

	private function get_ingredient($get_quantity,$get_unit,$get_ingred)
	{
		//echo $get_quantity.$get_unit;
		//print_r($get_ingred);
		$standard_unit = $this->convert_unit($get_unit);
		$ingreds=implode(' ',$get_ingred);
		// echo $ingreds;
		$query = 'SELECT num_cal FROM calories WHERE name_ingredient LIKE ?';
		$ingred_num = $this->db->query($query,'%'.$ingreds.'%');

		if($ingred_num->num_rows == 1)
		{
			// echo 'Entering num_rows==1here';
			// echo $standard_unit;
			$row =$ingred_num->row();
			// print_r($row);
			$ingred_cal = $get_quantity*$standard_unit*$row->num_cal;
			return $ingred_cal;
		}
		else if($ingred_num->num_rows >1)
		{
			// echo 'Entering num_rows>1 here';
			$ingred_cal_total=0;
			foreach($ingred_num->result() as $row)
				$ingred_cal_total += $row->num_cal;
			$ingred_cal=$ingred_cal_total/$ingred_num->num_rows;
			return $ingred_cal;
		}else{
			$non_ingred_words=array('canned','bottled','dried','fresh','raw','cooked'.'boiled','whipped','blend','dehydrated','chopped','chop','roasted','salted','crushed','diced','peeled');
			$total_calories=0;
			$total_matches=0;
			for($element=0;$element<count($get_ingred);$element++){
				if(! in_array($get_ingred[$element],$non_ingred_words)){
					$query='SELECT num_cal FROM calories WHERE name_ingredient LIKE ?';
					$recipes=$this->db->query($query,'%'.$get_ingred[$element].'%');
					foreach($recipes->result() as $row)
						$total_calories += $row->num_cal;

					$total_matches +=count($recipes->result());
				}
			}
			return $get_quantity*$standard_unit*($total_matches>0 ? $total_calories/$total_matches: 0);
		}
	}

	private function convert_unit($raw_unit)
	{
		switch($raw_unit)
		{
			case "ounce":
			case "ounces":
			case "oz":
			return 1;
			case "g":
			case "grams":
			case "gram":
			case "ml":
			return 0.035;
			case "kg":
			case "kgs":
			case "l":
			case "litre":
			return 35;
			case "tablespoon":
			case "tablespoons":
			return 0.5;
			case "cup":
			case "cups":
			return 8;
			case "teaspoon":
			case "teaspoons":
			case "tsp":
			return 0.17;
			case "bottle":
			case "bottles":
			return 12;
			case "pakage":
			case "pakages":
			return 16;
			case "jar":
			case "jars":
			return 26;
			case "can":
			case "cans":
			return 15;
			case "pound":
			case "pounds":
			return 16;
			default:
			return 1;
		}
	}

	private function calculate_string($mathString)
	{
   		$mathString = trim($mathString);     // trim white spaces
    	$mathString = preg_replace("/[^0-9\/*+\-]/", '', $mathString);
    	$compute = create_function("", "return (" . $mathString . ");" );
    	return 0+ $compute();
	}

	//======================= SEARCH FOR A RECIPE ==========================/
	function search_recipe()
	{
		$this->init_pagination();

		$query_str = 'SELECT *
		FROM recipes
		WHERE Match(recipe_name, description, ingredients, steps) Against (? IN BOOLEAN MODE)';
	}

	function meal_time($q)
	{
		$q_int = 0;
		if ($q=="lunch") $q_int = 1;
		else if ($q=="dinner") $q_int = 2;

		$this->init_pagination();
		$query_str = 'SELECT *
					  FROM recipes
					  WHERE meal_time = ?
					  ORDER BY num_of_likes DESC';
		$query_r   = $this->db->query($query_str, $q_int);
		return $query_r;
	}

	function add_to_favorite($user_id, $recipe_id)
	{
		$data = array('user_id' => $user_id, 'recipe_id' => $recipe_id);
		$this->db->insert('user_favorites', $data);
		$query_str = "UPDATE recipes SET num_of_likes = num_of_likes+1 WHERE recipe_id = ?";
		$this->db->query($query_str, $recipe_id);
	}

	function unfavorite($user_id, $recipe_id)
	{
		$query_str = "DELETE FROM user_favorites
					  WHERE user_id = $user_id AND recipe_id = ?";
		$this->db->query($query_str, $recipe_id);
	}

	//======================= MEAL PLANNER ==========================/
	/*
	2 funciton are created here

	1st is to suggest 3 meals for a user
		the parameter is a known user tuple from user_info
		it returns a array of recipes id of index breakfast,lunch and dinner

	2nd function is create a string for reason of recommendation which is determinated
	by the user's bmi which defines the user as underweight, normal or overweight.
		the parameter is also a known user tuple from user_info
		it returns a string of text
	*/
	function recipes_suggestion($user_info)
	{
		$v = $user_info->vegetarian;
		$weight = $this->calculate_bmi($user_info);
		return $this->daily_meal($weight, $v);
	}

	function suggesting_reason($user_info)
	{
		$i = $this->calculate_bmi($user_info);
		$reason = '';
		switch($i)
		{
			case'0':
				$reason ='You are underweight! <br>And encouraged to take more than 300 calories each meal';
				break;
			case'2':
				$reason ='You are overweight! <br>And encouraged to take less than 450 calories each mean';
				break;
			default:
				$reason ='You are healthy and enjoy your meal';
				break;
		}
		return $reason;
	}

	private function calculate_bmi($user_info)
	{
		$h = $user_info->height/100;
		$w = $user_info->weight;


		if($h==0||$w==0)
			$bmi = 1;
		else
			$bmi = $w/$h/$h;

		$overweight = 1;
		if($bmi<40 && $bmi>25)
			$overweight = 2;
		else if($bmi<18 && $bmi >4)
			$overweight = 0;
		return $overweight;
	}

	private function daily_meal($ow,$veg)
	{
		$lossweight = 450;
		$gainweight = 300;
		$query = 'SELECT MAX(recipe_id) max FROM recipes';
		$max_id = $this->db->query($query)->row()->max;
		$meal = array(	'breakfast'=> -1,
						'lunch'    => -1,
						'dinner'   => -1);
		for($i=0; $i<3; $i++)
		{

			$moreloop = true;
			$random = rand(1,$max_id);
			for(; $random<=$max_id; $random++)
			{
				$q1 = 'SELECT recipe_id,recipe_name,calories,meal_time,vegetarian FROM recipes WHERE recipe_id = ?';
				$get = $this->db->query($q1,$random);
				$recipe = $get->row();
				if(!$recipe)
				{
					if($random == $max_id && $moreloop == true)
					{
						$random = 0;
						$moreloop = false;
					}
					continue;
				}
				if (($ow==0&&$recipe->calories<$gainweight)||($ow==2&&$recipe->calories>$gainweight)||($veg&&$recipe->vegetarian==0))
				{
					if($random == $max_id && $moreloop == true)
					{
						$random = 0;
						$moreloop = false;
					}
					continue;
				}

				if($meal['breakfast']== -1&& $recipe->meal_time==0)
				{
					$meal['breakfast']=$recipe->recipe_id;
					break;
				}
				else if($meal['lunch']== -1&& $recipe->meal_time>0)
				{
					$meal['lunch']=$recipe->recipe_id;
					$lunch = $recipe->recipe_id;
					break;
				}
				else if($meal['dinner']== -1&& $recipe->meal_time>0)
				{
					if($recipe->recipe_id==$lunch)
						continue;
					else
						$meal['dinner']=$recipe->recipe_id;
					break;
				}
				if($random == $max_id && $moreloop == true)
				{
					$random = 0;
					$moreloop = false;
				}

			}
		}
		return $meal;
	}
}

?>