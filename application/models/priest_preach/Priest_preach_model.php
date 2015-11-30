<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Priest_preach_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
	}
	
	public function add_course_class($course_class_name,$admin_id)
	{
		if (! empty($course_class_name) && !empty($admin_id)) {
			$this->db->select('id');
			$this->db->from('class_priest_preach');
			$this->db->like('class_name' , $course_class_name, 'both');
			$is_have = $this->db->get()->first_row();
			// var_dump($is_have);exit();
			if (empty($is_have)) {

				$this->db->set('class_name', $course_class_name);
				$this->db->set('created_by', $admin_id);
				$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));

				$this->db->insert('class_priest_preach');
				// echo $this->db->last_query();exit();
				return $this->db->insert_id();	

			}else{
				return $is_have;
			}
			
		}else{
			return false;
		}
	}

	public function find_class_name_priest_preach()
	{
		$this->db->select('id,class_name');
		$this->db->from('class_priest_preach');
		$this->db->where('deleted_at is null');
		return	$this->db->get()->result();

	}
	
	public function getContent($c_p_p_id,$file_name,$full_path,$orig_name,$file_size,$admin_id,$course_title,$share_from,$course_keys)
	{
		if (!empty($c_p_p_id) && !empty($file_name) && !empty($full_path)  && !empty($orig_name)  && !empty($admin_id) && !empty($course_title) && !empty($share_from))  {

			$this->db->set('c_p_p_id', $c_p_p_id);
			$this->db->set('created_by', $admin_id);
			$this->db->set('course_title', $course_title);
			$this->db->set('share_from', $share_from);
			$this->db->set('course_keys', $course_keys);
			$this->db->set('file_name', $file_name);
			$this->db->set('full_path', $full_path);
			$this->db->set('orig_name', $orig_name);
			$this->db->set('file_size', $file_size);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			$this->db->insert('content_priest_preach');

			return $this->db->insert_id();	
			
		}else{

			return false;
		}
	}

	public function alert_upload_priest_preach($insert_id)
	{
		if (!empty($insert_id)) {
			$data_array  = array();
			$this->db->select('id');
			$this->db->from('user');
			$this->db->where('deleted_at is null');
			$users_id  = $this->db->get()->result_array();

			foreach ($users_id as $key => $value) {
				$user_id = $value['id'];
				$data_array[]= array(
								'table_name' => 'content_priest_preach' ,
							      'table_id' => $insert_id ,
							      'user_id' => $user_id,
							      'created_at' => mdate('%Y-%m-%d %H:%i:%s', now())
								);

			}

			$this->db->insert_batch('alert_message', $data_array); 
			return $this->db->affected_rows();

		}else {
			return false;
		}
	}

	public function get_priest_preach_by_id($id ,$limit, $offset)
	{
		$data_return = array(); 
		if (!empty($id)) {

			$this->db->select('content_priest_preach.id as content_p_p_id ,class_priest_preach.id as class_priest_id ,
			 class_name , course_title,share_from, course_keys,file_name,full_path,orig_name,file_size,content_priest_preach.created_at as content_p_p_created_at');
			$this->db->from('content_priest_preach');
			$this->db->join('class_priest_preach', 'class_priest_preach.id = content_priest_preach.c_p_p_id', 'left');
			$this->db->where('class_priest_preach.id' , $id);
			$this->db->where('content_priest_preach.deleted_at is null');
			$this->db->where('class_priest_preach.deleted_at is null');
			$this->db->limit($limit, $offset);
			$this->db->order_by('content_priest_preach.created_at', 'desc');
			$data_array =  $this->db->get();	

				foreach ($data_array->result_array() as $k => $v) {
					$file_size = $v['file_size'];

					$v['file_size'] = $this->_get_percentage($file_size,1024);
					$data_return[] = $v;
				}
			return 	$data_return;

		}else{
			return false;
		}
	}
	
	private function _get_percentage($val1,$val2,$decimal = 2)
	{
	    if ($val2==0) {
	        return "0";
	    }

	    return round($val1 / $val2 , $decimal);
	}


	public function pp_read_by_id($id)
	{
		if (!empty($id)) {

			$this->db->select('content_priest_preach.id as content_p_p_id ,class_priest_preach.id as class_priest_id , class_name , course_title,share_from, content,course_keys, content_priest_preach.created_at as content_p_p_created_at');
			$this->db->from('content_priest_preach');
			$this->db->join('class_priest_preach', 'class_priest_preach.id = content_priest_preach.c_p_p_id', 'left');
			$this->db->where('content_priest_preach.id' , $id);
			$this->db->where('content_priest_preach.deleted_at is null');
			$this->db->where('class_priest_preach.deleted_at is null');
			return 	$this->db->get()->first_row();

		}else {
			return false;
		}
	}

	public function count_priest_preach_by_id($id)
	{
		if (!empty($id)) {
			$this->db->select('count(*) as count ');
			$this->db->from('content_priest_preach');
			$this->db->where('c_p_p_id', $id);
			$this->db->where('deleted_at is null');
			return $this->db->get()->first_row();
		}else {
			return false;
		}
	}

	public function del_course($id,$admin_id)
	{
		$params  = array(
			'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
			'deleted_by' =>  $admin_id, 
			);

		$this->db->where('id', $id);
		$this->db->update('content_priest_preach', $params);
		return  $this->db->affected_rows();
	}

	

	// public function read_myEdit()
	// {
	// 	$this->db->select('id,edit_content,created_at ');
	// 	$this->db->from('editor_by_hand_of_document');
	// 	$this->db->where('deleted_at is null');
	// 	$this->db->order_by('created_at', 'desc');
	// 	return $this->db->get()->first_row();
	// }

	public function read_myEdit_by_id($document_id)
	{	
		$return_array = array();
		$this->db->select('id,edit_content,created_at,updated_at');
		$this->db->from('editor_by_hand_of_document');
		if (!empty($document_id)) {
			$this->db->where('id',$document_id);
		}
		$this->db->where('deleted_at is null');
		$this->db->order_by('id', 'desc');

		$return_array['rows'] = $this->db->get()->first_row();
		// var_dump($return_array['rows']);exit;
		$this->db->select('max(id) as max_id');
		$this->db->from('editor_by_hand_of_document');

		if (!empty($document_id)) {
			$this->db->where('id <' ,$document_id);
		}else {
			$document_id  = isset($return_array['rows']->id) ? $return_array['rows']->id : 1; 
			$this->db->where('id <' ,$document_id);
		}	

		$this->db->where('deleted_at is null');
		$max_id = $this->db->get()->first_row();

		$return_array['pre_id'] = !empty($max_id->max_id) ? $max_id->max_id : "" ;	

		
		$this->db->select('min(id) as min_id');
		$this->db->from('editor_by_hand_of_document');

		if (!empty($document_id)) {
			$this->db->where('id >' ,$document_id);
		}else {
			$document_id  = isset($return_array['rows']->id) ? $return_array['rows']->id : 1; 
			$this->db->where('id >' ,$document_id);
		}

		$this->db->where('deleted_at is null');
		$min_id = $this->db->get()->first_row();

		$return_array['next_id'] = !empty($min_id->min_id) ? $min_id->min_id : "" ;	

		return $return_array;
	}

	public function getmyEditor($myEditor,$admin_id,$document_id='')
	{
		if (!empty($myEditor)  && !empty($admin_id) && empty($document_id)) {
			$this->db->set('edit_content', $myEditor);
			$this->db->set('created_by', $admin_id);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			$this->db->insert('editor_by_hand_of_document');
			return $this->db->affected_rows();

		}else if (!empty($myEditor)  && !empty($admin_id) && !empty($document_id)) {
			$params = array(
				'edit_content' => $myEditor, 
				'updated_by' => $admin_id, 
				'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()), 
				);
			$this->db->where('id', $document_id);
			$this->db->update('editor_by_hand_of_document', $params);
			return $this->db->affected_rows();

		}else {
			return false;	
		}	
	}

	public function del_document($id,$admin_id)
	{
		$params  = array(
			'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
			'deleted_by' =>  $admin_id, 
			);

		$this->db->where('id', $id);
		$this->db->update('editor_by_hand_of_document', $params);
		return  $this->db->affected_rows();
	}
}
	
	
	
	
	
	
	
	
	
