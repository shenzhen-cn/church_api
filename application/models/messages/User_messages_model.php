<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_messages_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
	}

	public function del_alert_comments_by_spirituality_id($spirituality_id,$user_id)
	{
		if(!empty($spirituality_id) && !empty($user_id)){
			$this->db->select('id');
			$this->db->from('comments_of_spirituality ');
			$this->db->where('spirituality_id', $spirituality_id);
			$this->db->where('deleted_at is null');
			$id_results =  $this->db->get()->result();
			$affected_rows = array();
			if(!empty($id_results)){
				foreach ($id_results as $k => $v) {
					$comment_id = $v->id;
					$this->db->delete('alert_message', array('table_name'=>'comments_of_spirituality','table_id' => $comment_id)); 
					$affected_rows[] =  $this->db->affected_rows();	
				}	
			}
			
			return $affected_rows;
		}	
	}

	public function del_all_praise_alert($user_id)
	{
		if (!empty($user_id)) {
			
			$this->db->delete('alert_message', array('table_name'=>'praise_of_spirituality','user_id' => $user_id));			
			return $this->db->affected_rows();
		}else{
			return false;
		}
	}	


	public function del_prompt_alerts($user_id)
	{
		if (!empty($user_id)) {

			$this->db->where('user_id', $user_id);
			$this->db->where('table_name <>', 'praise_of_spirituality');
			$this->db->delete('alert_message'); 
			return $this->db->affected_rows();
		}
	}	

	public function remove_alert_by_id($alert_id)
	{
		if (!empty($alert_id)) {
			$this->db->where('id', $alert_id);
			$this->db->delete('alert_message'); 
			return $this->db->affected_rows();
		}else{
			return false;
		}
	}

	public function del_alert_content_priest_preach($table_id)
	{
		if(!empty($table_id)){
			$this->db->where('table_id', $table_id);
			$this->db->where('table_name', 'content_priest_preach');
			$this->db->delete('alert_message'); 
			return $this->db->affected_rows();
		}
	}

	public function del_alert_message_by_user_id($user_id)
	{
		if(!empty($user_id)){

			$this->db->where('user_id', $user_id);
			$this->db->delete('alert_message'); 
			return $this->db->affected_rows();
		}
	}

}