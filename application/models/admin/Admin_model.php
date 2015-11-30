<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
	}

	public function find_by_email($admin_name)
	{
		return $this->db->get_where('admin', array('admin_name' => $admin_name, 'deleted_at' => NULL))->row();
	}

	public function find_token_by_client_id_and_account_id($account_id)
	{
		if ($token = $this->db->get_where('admin_access_token', array('admin_account_id' => $account_id))->row())
		{
			return $token;
		}
		$this->db->insert('admin_access_token', array(
			'admin_account_id' => $account_id,
			'access_token' => $this->generate_token(),
			'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
			'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now())
		));
		
		return $this->db->get_where('admin_access_token', array( 'admin_account_id' => $account_id))->row();
	}
	
	/**
	 * Generate access token
	 */				
	public function generate_token()
	{
		return md5(base64_encode(pack('N6', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), uniqid())));												
	}

	public function check_password($admin_name,$admin_pwd)
	{

		return $this->db->get_where('admin', array('admin_name' =>$admin_name,'admin_pwd' => md5(md5($admin_pwd))))->row();
	}

	public function improveInformation($admin_id,$adminHeadSrc, $gender, $admin_nick)
	{	
		$data_return  	= array();
		$params 		= array();
		if (!empty($admin_id) && !empty($adminHeadSrc) && !empty($gender) ) {

			if (!empty($admin_nick)) {
				$params['nick']  = $admin_nick;
			}

			$params['gender'] 		= $gender;

			$this->db->where('id', $admin_id);
			$this->db->update('church.admin', $params);
			$data_return['affected_id'] =  $this->db->affected_rows();

			// echo $this->db->last_query();exit;
			// var_dump($affected_id);exit;

			$this->db->set('admin_id', $admin_id);
			$this->db->set('adminHead_src',  $adminHeadSrc);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			$this->db->set('update_at', mdate('%Y-%m-%d %H:%i:%s', now()));

			$this->db->insert('adminhead_src');
			
			// echo $this->db->last_query();exit;
			
			$data_return['adminHead_src_id'] = $this->db->insert_id();

			return $data_return;

		}else {

			return false;
		}
	}

	public function home_inform($home_inform,$admin_id,$home_inform_days)
	{
		if ( !empty($home_inform) && !empty($admin_id)){        

			$this->db->select('id');
			$this->db->from('home_inform');
			$this->db->where('deleted_at is null');
			$data_array  =  $this->db->get()->result();

			if (!empty($data_array)) {

				foreach ($data_array as $k => $v) {
					 $array_id =  $v->id;
					 
					 $data = array(
					                'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now())
					             );
					  
					 $this->db->where('id', $array_id);
					 $this->db->update('home_inform', $data);
					 $affected_rows = $this->db->affected_rows();	
				}

			}

			$data = array(
			               'home_inform_content' => $home_inform,
			               'home_inform_days' => $home_inform_days,
			               'admin_id' => $admin_id,
			               'create_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
			               'overdue_at' => date("Y-m-d",time()+$home_inform_days*24*3600)
			            );

			$this->db->insert('home_inform', $data);
			return $this->db->insert_id();
				

		}else {
			return false;
		}
	}

	public function find_home_inform()
	{
//		 var_dump("expression");exit();
		$this->db->select('id,home_inform_content,overdue_at,create_at');
		$this->db->from('home_inform');
		$this->db->where('deleted_at is null');
		$this->db->order_by('overdue_at','DESC');
		return $this->db->get()->first_row();
	}

	public function urgentPrayer($urgent_prayer_days,$urgent_prayer_content,$admin_id)
	{
		if (! empty($urgent_prayer_days) && ! empty($urgent_prayer_content) && ! empty($admin_id)) {

			$this->db->select('id,status');
			$this->db->from('urgent_prayer');
			$this->db->where('deleted_at is null');
			$data_array  =  $this->db->get()->result();

			if (!empty($data_array)) {
				
				foreach ($data_array as $k => $v) {
					$id =  $v->id;
					
					$data = array(
					               'status' => '1'
					            );
					 
					$this->db->where('id', $id);
					$this->db->update('urgent_prayer', $data);
					$affected_rows = $this->db->affected_rows();
				}
			}

			$data =array(
			           'urgent_prayer_content' => $urgent_prayer_content,
			           'admin_id' => $admin_id,
			           'urgent_prayer_days' => $urgent_prayer_days,
			           'create_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
			           'overdue_at' => date("Y-m-d",time()+$urgent_prayer_days*24*3600)
				);

			$this->db->insert('urgent_prayer', $data);
			return $this->db->insert_id();


		}else{
			return false;
		}
	}


	public function find_urgent_prayer()
	{
		$this->db->select('id,urgent_prayer_content,overdue_at,create_at');
		$this->db->from('urgent_prayer');
		$this->db->where('deleted_at is null');
		$this->db->where('status','0');
		$this->db->order_by('overdue_at','DESC');
		return $this->db->get()->first_row();
	}


	public function find_spirituality($group_id)
	{		
		$data_array  = array();
		$regtime  =  date("Y-m-d H:i:s",time());

		if (!empty($group_id)) {

			$this->db->select('book_id,chapter_id,updated_at');
			$this->db->from('setting_spirituality');
			$this->db->where('group_id', $group_id);
			$temp1 = $this->db->get()->result_array();
			// var_dump($temp1);exit();	
			if (!empty($temp1)) {
				
				// var_dump($temp1);exit();
				$updated_at = $temp1[0]['updated_at'] ;				

				if ($updated_at > $regtime) {

					foreach ($temp1 as $k => $v) {
						$data_array['book_id'] = $v['book_id'];
						$data_array['chapter_id'] = $v['chapter_id'];
					}	

				}else{

					$book_id = isset($temp1[0]['book_id']) ? $temp1[0]['book_id'] : "1"; 
					$chapter_id = isset($temp1[0]['chapter_id']) ? $temp1[0]['chapter_id'] : "1"; 
					$data_array = $this->count_chapters($book_id,$chapter_id);	
					// var_dump($data_array);exit();
					$data = array(
	   				               'book_id' => $data_array['book_id'],
	   				               'chapter_id' => $data_array['chapter_id'],
	   				               'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
	   				               'updated_at' => date("Y-m-d",time()+24*3600)
					            );
					 
					$this->db->where('group_id', $group_id);
					$this->db->update('setting_spirituality', $data);
					// return $this->db->affected_rows();

				}	
					
			}else{
				$data_array = $this->count_chapters();
			}	

			return $data_array;
		}else{
			 return false;			 
		}
		
	}

	public function count_chapters($book_id="",$chapter_id="")
	{
		$book_id = empty($book_id) ? rand(1,66) : $book_id;
		
		$this->db->select('count(distinct chapter_id) as count');
		$this->db->from('bible_section');
		$this->db->where('book_id', $book_id);
		
		$temp2 =  $this->db->get()->first_row();
		$chapter_id = empty($chapter_id) ? rand(1,$temp2->count) : $chapter_id+1 ; 

		$chapter_id = $chapter_id > $temp2->count ? 1 : $chapter_id;  	

		// var_dump($chapter_id);exit();
		if ($chapter_id == 1 ) {

			$book_id = ($book_id+1) > 66 ? 1  : $book_id+1;	
		}else{
			
			$book_id = $book_id > 66 ? 1  : $book_id;	
		}	

		$data_array['book_id'] = $book_id;
		$data_array['chapter_id'] =  $chapter_id;
		
		return $data_array;

	}	

	public function checkCurrentadminPwd($currentPwd,$admin_id)
	{
		$this->db->select('id');
		$this->db->from('admin');
		$this->db->where('id', $admin_id);
		$this->db->where('admin_pwd', $currentPwd);
		$this->db->where('deleted_at is null');
		return $this->db->get()->result();
	}

	public function resetadminpassword($admin_id,$confirmNewPwd)
	{
		$params['admin_pwd']    = $confirmNewPwd;

		$this->db->where('id', $admin_id);
		$this->db->where('deleted_at is null');
		$this->db->update('admin', $params);
		return $this->db->affected_rows();
	}

	public function find_admin_email_is_exist($user_email)
	{
		if ( !empty($user_email)) {

			$this->db->select('id');
			$this->db->from('admin');
			$this->db->where('admin_name', $user_email);
			$this->db->where('deleted_at is null');
			$query =  $this->db->get()->first_row();
			$admin_id = null;
			if(!empty($query)){
				$admin_id = $query->id;
			}

			return $admin_id;
		}else{
			return false;
		}
	}

	public function search_bibile($testament,$book_id,$chapter_id,$form_key)
	{
		if (!empty($testament) && !empty($book_id) && !empty($chapter_id)) {
			$data_temp  = array();
			$data2 = array();
			$this->db->select('bible_section.chapter_id,bible_section.section,bible_section.content as section_content ');
			$this->db->from('bible_section');
			$this->db->where('bible_section.testament', $testament);
			$this->db->where('bible_section.book_id', $book_id);

			if (!empty($form_key)) {				
				$this->db->like('bible_section.content', $form_key, 'both');
			}
			$this->db->where('bible_section.chapter_id', $chapter_id);
			$data1 = $this->db->get()->result();
			foreach ($data1 as $row_cer) {

				$section_id = $row_cer->section;
				$this->db->select('book_id,chapter_id,section,note_title,content');
				$this->db->from('bible_note');
				$this->db->where('section', $section_id);
				$this->db->where('book_id', $book_id);
				$this->db->where('chapter_id', $chapter_id);
				$data_temp = $this->db->get()->result_array();
				foreach ($data_temp as $row_cer2) {
					$data2[] = array_merge($row_cer2); 
				}

			}
			
			return  array('section_result' =>$data1 , 
						'note_result' =>$data2
				    );

		}else{
			return false;
		}
	}	

	public function setting_todayScriptures($data)
	{
		if (!empty($data)) {
			
			$this->db->empty_table('setting_todayscriptures'); 
			$this->db->insert_batch('setting_todayscriptures', $data); 
			return $this->db->affected_rows();
		}else{
			return false;
		}
	}

	public function find_todayScriptures()
	{
		$data_return = null;
		$this->db->select('id,setting_todayscriptures_id,created_at');
		$this->db->from('swap_todayscriptures');
		$this->db->where('deleted_at is null');
		$today_swap =  $this->db->get()->first_row();
		$setting_todayscriptures_id = isset($today_swap->setting_todayscriptures_id) ? $today_swap->setting_todayscriptures_id: "";
		$swap_todayscriptures_id    = isset($today_swap->id) ? $today_swap->id: "";
		$swap_todayscriptures_created_at = isset($today_swap->created_at) ? $today_swap->created_at: "";


		$this->db->select('setting_todayscriptures.id as setting_todayscriptures_id,created_by,directory,setting_todayscriptures.chapter_id,setting_todayscriptures.section_id,content,setting_todayscriptures.created_at');
		$this->db->from('setting_todayscriptures');
		$this->db->join('bibile_book', 'bibile_book.id = setting_todayscriptures.book_id', 'left');
		$this->db->join('bible_section', 'bible_section.book_id = setting_todayscriptures.book_id', 'left');
		$this->db->where( 'bible_section.chapter_id = setting_todayscriptures.chapter_id');
		$this->db->where( 'bible_section.section = setting_todayscriptures.section_id');
		$this->db->where('setting_todayscriptures.deleted_at is null ' );
		if (!empty($setting_todayscriptures_id)) {			
			$this->db->where('setting_todayscriptures.id', $setting_todayscriptures_id);
		}
		$data_return = $this->db->get()->first_row();
			

		$setting_todayscriptures_id = isset($data_return->setting_todayscriptures_id) ? $data_return->setting_todayscriptures_id : "";
		$regtime  =  date("Y-m-d",time());

		if ($regtime > $swap_todayscriptures_created_at) {
			
			$this->db->select('min(id) as id');
			$this->db->from('setting_todayscriptures');
			$this->db->where('id >',$setting_todayscriptures_id);
			$this->db->where('deleted_at is null');
			$min_id = $this->db->get()->first_row();
			$min_id = isset($min_id->id) ? $min_id->id : "";		

			$this->db->delete('swap_todayscriptures', array('id' => $swap_todayscriptures_id)); 
			$affected_id = $this->db->affected_rows();
			if (!$affected_id) {
				$this->db->empty_table('swap_todayscriptures'); 
			}
			$this->db->set('setting_todayscriptures_id',  $min_id);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			$this->db->insert('swap_todayscriptures');
		}

		return $data_return;
	}

	public function notice_groups($group_id_str,$admin_id,$notice_contents)
	{
		if (!empty($group_id_str) && !empty($admin_id) && !empty($notice_contents)) {

			$insert_id_array = array();
			$data_array = explode(',',$group_id_str); 
			// var_dump($data_array);exit;
			$data = array(
			               'contents' => $notice_contents ,
			               'created_by' => $admin_id,
			               'created_at'=>mdate('%Y-%m-%d %H:%i:%s', now())
			            );

			$this->db->insert('notice_groups', $data);
			$insert_id = $this->db->insert_id();
			// var_dump($insert_id);exit;
			foreach ($data_array as $key=> $group_id ) {
				$this->db->select('group_leader_id');	
				$this->db->from('group');
				$this->db->where('id', $group_id);
				$this->db->where('deleted_at is null');
				$temp_data1 = $this->db->get()->first_row();				
				$user_id  = isset($temp_data1->group_leader_id) ? $temp_data1->group_leader_id : "";

				if (!empty($user_id)) {
					$data = array(
					               'table_name' => 'notice_groups' ,
					               'table_id' => $insert_id,
					               'user_id' => $user_id,
					               'created_at'=>mdate('%Y-%m-%d %H:%i:%s', now())
					            );
					$this->db->insert('alert_message', $data);
					$insert_id_array[] = $this->db->insert_id();
				}
				
			}

			return $insert_id;
		}
	}

	public function get_admin_info($admin_id)
	{
		if(!empty($admin_id)){
			 return $this->get_admin_all_info($admin_id);
		}else{
			return null;
		}
	}	

	public function reset_admin_pwd($pwd,$admin_id)
	{
		if(!empty($pwd) && !empty($admin_id)){
			$params  = array(
				'admin_pwd' => $pwd,
				'reset_pwd_at' => mdate('%Y-%m-%d %H:%i:%s', now())
				 );
			$this->db->where('id', $admin_id);
			$this->db->update('church.admin', $params);
			return $this->db->affected_rows();
		}else{
			return false;
		}
	}

	public function is_send_email($forget_pwd_token_id)
	{
		if(!empty($forget_pwd_token_id)){
			$data = array(
			               'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now())
			            );

			$this->db->where('id', $forget_pwd_token_id);
			$this->db->update('forget_pwd_token', $data); 				
			return $this->db->affected_rows();
		}
	}

}
	
	
	
	
	
	
	
	
	
