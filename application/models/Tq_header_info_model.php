<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tq_header_info_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
	}

	public function findUser($user_id)
	{		
		$this->db->select('user.id as user_id ,email,nick,sex,group_id,user.created_by_admin_id,group_name ,group_leader_id');
		$this->db->from('church.user');
		$this->db->join('group', 'group.id  = user.group_id ', 'left');
		$this->db->where('user.id' , $user_id);
		$this->db->where('user.deleted_at is NULL');
		$this->db->where('group.deleted_at is NULL');
		return $this->db->get()->row();		
		// echo $this->db->last_query();exit;
	}

	public function findGroup()
	{
		$data_return  = array();

		$this->db->select('id,group_name');
		$this->db->from('church.group');
		$this->db->where('deleted_at is null');
		$data_return = $this->db->get()->result();
		return $data_return;

	}

	

	public function get_admin_info($admin_id)
	{
		if(!empty($admin_id)){
			$admin_info = $this->get_admin_all_info($admin_id);					

			return $admin_info;
		}else{
			return false;
		}
	}

	public function get_class_name_priest_preach()
	{
		$this->load->model('priest_preach/priest_preach_model');
        return $this->priest_preach_model->find_class_name_priest_preach();

	}
	

	public function get_tip_messages($user_id)
	{
		$data_return_of_content_priest_preach = array();
		$data_return_of_user_album_src = array();
		$count_c_p_p_id = array();
		$temp_data2 = array();
		$is_readed_count_user_album_src = array();
		$data_return_of_notice_groups = array();
		$is_readed_count_notice_groups = array();
		$data_return_of_praise_of_spirituality = array();
		$is_readed_count_praise_of_spirituality = array();
		$count_onself_praised = null;
		$data_return_of_comments_of_spirituality =  array();
		$data_return_of_replies_of_spirituality = array();

		$this->db->select('id, table_name, table_id');
		$this->db->from('alert_message');
		$this->db->where('is_readed', 'N');
		$this->db->where('user_id', $user_id);
		$this->db->where('deleted_at is null ');
		$this->db->order_by('id', 'desc');
		$data1 = $this->db->get()->result_array();

		foreach ($data1 as $row_cer) {
			$table_id = $row_cer['table_id'];
			$table_name = $row_cer['table_name'];
			$alert_id = $row_cer['id'];

			switch ($table_name) {

				case $table_name == 'content_priest_preach':				
					$this->db->select('c_p_p_id');
					$this->db->from($table_name);	
					$this->db->where('id', $table_id);
					$this->db->where('deleted_at is null');
					$data2 = $this->db->get()->first_row();

					$class_priest_preach_id = isset($data2->c_p_p_id) ? $data2->c_p_p_id : "";
					$row_cer['class_priest_preach_id'] = $class_priest_preach_id;  							
					$data_return_of_content_priest_preach[] = $row_cer;									

					$this->db->select('count(*) as count');
					$this->db->from($table_name);
					$this->db->join('alert_message', 'alert_message.table_id = content_priest_preach.id', 'left');
					$this->db->where('c_p_p_id', $class_priest_preach_id);
					$this->db->where('alert_message.user_id', $user_id);
					$this->db->where('content_priest_preach.deleted_at is null');
					$this->db->where('alert_message.deleted_at is null');
					$data4 = $this->db->get()->first_row();

					$count_class_priest_preach = isset($data4->count) ? $data4->count : "";				
					$count_c_p_p_id[$class_priest_preach_id] = $count_class_priest_preach;
					break;

				case $table_name == 'user_album_src':
					$this->db->select('album_id');
					$this->db->from($table_name);
					$this->db->where('id', $table_id);
					$this->db->where('deleted_at is null');
					$data3 = $this->db->get()->first_row();
					$album_id = isset($data3->album_id) ? $data3->album_id : "";
					$row_cer['album_id'] = $album_id;  							
					$data_return_of_user_album_src[] = $row_cer;									

					$this->db->select('count(*) as count');
					$this->db->from($table_name);
					$this->db->join('alert_message', 'alert_message.table_id = user_album_src.id', 'left');
					$this->db->where('album_id', $album_id);
					$this->db->where('alert_message.user_id', $user_id);
					$this->db->where('user_album_src.deleted_at is null');
					$this->db->where('alert_message.deleted_at is null');
					$data5 = $this->db->get()->first_row();

					$count_user_album_src = isset($data5->count) ? $data5->count : "";				
					$is_readed_count_user_album_src[$album_id] = $count_user_album_src;
					break;					

				case $table_name == 'notice_groups':												
					$data_return_of_notice_groups[] = $row_cer;									

					$this->db->select('count(*) as count');
					$this->db->from($table_name);
					$this->db->join('alert_message', 'alert_message.table_id = notice_groups.id', 'left');
					$this->db->where('notice_groups.id', $table_id);
					$this->db->where('alert_message.user_id', $user_id);
					$this->db->where('notice_groups.deleted_at is null');
					$this->db->where('alert_message.deleted_at is null');
					$temp_data7 = $this->db->get()->first_row();
					$count_notice_groups = isset($temp_data7->count) ? $temp_data7->count : "";				
					$is_readed_count_notice_groups[$table_id] = $count_notice_groups;					
					break;
					
				case $table_name == 'praise_of_spirituality':																

					$this->db->select('praise_of_spirituality.id,praiser,spirituality_id,directory,book_id,chapter_id ,praise_of_spirituality.created_at');									
					$this->db->from($table_name);
					$this->db->join('spirituality', 'spirituality.id = praise_of_spirituality.spirituality_id', 'left');
					$this->db->join('bibile_book', 'bibile_book.id = spirituality.book_id', 'left');
					$this->db->where('praise_of_spirituality.id', $table_id);
					$this->db->where('praise_of_spirituality.deleted_at is null');
					$this->db->where('spirituality.deleted_at is null');
					$this->db->where('spirituality.deleted_by is null');
					$this->db->order_by('praise_of_spirituality.id', 'desc');
					$query = $this->db->get();					
					if ($query->num_rows() > 0) {
						$temp_data3 = $query->first_row(); 
					}
					$praiser = $temp_data3->praiser;
					$temp_data4 = $this->get_user_all_info($praiser);
					$nick = $temp_data4['nick'];
					$userHead_src = $temp_data4['userHead_src'];
				

					$created_at = $temp_data3->created_at;
					$tranTime_created_at = $this->tranTime(strtotime($created_at));

					$this->db->where('praiser', $user_id);
					$this->db->where('id', $table_id);
					$this->db->where('deleted_at is null');
					$this->db->from($table_name);
					$temp_data6 = $this->db->count_all_results();
					$count_onself_praised += $temp_data6;

					$data_return_of_praise_of_spirituality[] = 
					array(
						'praise_of_spirituality_id' => $temp_data3->id,
						'praiser' => $temp_data3->praiser,
						'nick' => $nick,
						'userHead_src' => $userHead_src,				
						'spirituality_id' => $temp_data3->spirituality_id,
						'directory' => $temp_data3->directory,
						'chapter_id' => $temp_data3->chapter_id,
						'created_at' => $tranTime_created_at,
						 );								

					$this->db->select('count(*) as count');
					$this->db->from($table_name);
					$this->db->join('alert_message', 'alert_message.table_id = praise_of_spirituality.id', 'left');
					$this->db->where('praise_of_spirituality.id', $table_id);
					$this->db->where('alert_message.user_id', $user_id);
					$this->db->where('praise_of_spirituality.deleted_at is null');
					$this->db->where('alert_message.deleted_at is null');
					$temp_data5 = $this->db->get()->first_row();
					// var_dump($temp_data5);exit;

					$count_praise_of_spirituality = isset($temp_data5->count) ? $temp_data5->count : "";				
					$is_readed_count_praise_of_spirituality[$table_id] = $count_praise_of_spirituality;					
					break;		

				//灵修评论
				case $table_name == 'comments_of_spirituality':												
					$temp_data1 = array();

					$this->db->select('comments_of_spirituality.id,contents,spirituality_id,commenter,comments_of_spirituality.created_at,directory,book_id,chapter_id');					
					$this->db->from($table_name);
					$this->db->join('spirituality', 'spirituality.id = comments_of_spirituality.spirituality_id', 'left');
					$this->db->join('bibile_book', 'bibile_book.id = spirituality.book_id', 'left');
					$this->db->where('comments_of_spirituality.id', $table_id);
					$this->db->where('comments_of_spirituality.deleted_at is null');
					$this->db->where('spirituality.deleted_at is null');
					$this->db->order_by('comments_of_spirituality.id', 'desc');
					$query = $this->db->get();
					// echo $this->db->last_query();exit;
					if ($query->num_rows() > 0) {
						$temp_data1 = $query->first_row();
					}				
					// var_dump($temp_data1);exit;										
					if (!empty($temp_data1)) {
						if (isset($temp_data1->commenter)) {
							$commenter_id = $temp_data1->commenter;
							$commenter_data = $this->get_user_all_info($commenter_id);
							$created_at = $temp_data1->created_at;
						}

						$tranTime_created_at = $this->tranTime(strtotime($created_at));					

						$data_return_of_comments_of_spirituality[]  =
						array(
							'commenter_id' => $commenter_id,
							'nick' => $commenter_data['nick'],
							'userHead_src' => $commenter_data['userHead_src'],
							'comment_id' => $temp_data1->id,
							'comment_contents' => $temp_data1->contents,
							'spirituality_id' => $temp_data1->spirituality_id,
							'tranTime_created_at' => $tranTime_created_at,
							'directory' => $temp_data1->directory,
							'book_id' => $temp_data1->book_id,
							'chapter_id' => $temp_data1->chapter_id,
							'alert_id' => $alert_id,
						 );												
					}											
					break;
				
				//回复提醒
				// $temp_data2 = array();
				case $table_name == 'replies_of_spirituality':												
					$this->db->select('replier,replies_of_spirituality.created_at,comments_id,spirituality_id,book_id,chapter_id,directory');
					$this->db->from($table_name);
					$this->db->join('comments_of_spirituality', 'comments_of_spirituality.id = replies_of_spirituality.comments_id', 'left');
					$this->db->join('spirituality', 'spirituality.id = comments_of_spirituality.spirituality_id', 'left');
					$this->db->join('bibile_book', 'bibile_book.id   = spirituality.book_id', 'left');
					$this->db->where('replies_of_spirituality.id', $table_id);
					$this->db->where('replies_of_spirituality.deleted_at is null');
					$this->db->where('comments_of_spirituality.deleted_at is null');
					$this->db->where('spirituality.deleted_at is null');
					$this->db->order_by('replies_of_spirituality.id', 'desc');
					$query = $this->db->get();

					if ($query->num_rows() > 0 ) {
						$temp_data2	= $query->result_array();					
					}
					
					// var_dump($temp_data2);exit;
					foreach ($temp_data2 as $key => $value) {
						$replier = $value['replier'];
						$replier_info = $this->get_user_all_info($replier);
						// var_dump($replier_info);exit;
						$created_at = $value['created_at'];
						$tranTime_created_at = $this->tranTime(strtotime($created_at));					
						
						$data_return_of_replies_of_spirituality[] = 
						array(
							'replier_id' => $replier,
							'replier_nick' => $replier_info['nick'],
							'replier_userHead_src' => $replier_info['userHead_src'],
							'tranTime_created_at' => $tranTime_created_at,
							'comments_id' => $value['comments_id'],
							'spirituality_id' => $value['spirituality_id'],
							'directory' => $value['directory'],
							'chapter_id' => $value['chapter_id'],
						);

					}


					break;		

				default:
					break;
			}
			
		}
		// var_dump($data_return_of_comments_of_spirituality);exit;
		// exit;
		return  array(  
					//牧师课程
					'data_return_of_content_priest_preach' => $data_return_of_content_priest_preach,
					'count_content_priest_preach_messages' => count($data_return_of_content_priest_preach),
					'is_readed_count_c_p_p_id' => $count_c_p_p_id,
					//新上传的照片通知
					'data_return_of_user_album_src' => $data_return_of_user_album_src,
					'count_user_album_src_messages' => count($data_return_of_user_album_src),
					'is_readed_count_user_album_src' => $is_readed_count_user_album_src,
					//小组通知
					'data_return_of_notice_groups' => $data_return_of_notice_groups,
					'count_notice_groups_messages' => count($data_return_of_notice_groups),
					'is_readed_count_notice_groups' => $is_readed_count_notice_groups,
					//赞通知
					'data_return_of_praise_of_spirituality' => $data_return_of_praise_of_spirituality,
					'count_praise_of_spirituality_messages' => count($data_return_of_praise_of_spirituality)-$count_onself_praised,
					'is_readed_count_praise_of_spirituality' => $is_readed_count_praise_of_spirituality,
					//灵修评论
					'data_return_of_comments_of_spirituality' => $data_return_of_comments_of_spirituality,
					'count_praise_of_comments_of_spirituality_messages' => count($data_return_of_comments_of_spirituality),
					//回复评论
					'data_return_of_replies_of_spirituality' => $data_return_of_replies_of_spirituality,
					'count_praise_of_replies_of_spirituality_messages' => count($data_return_of_replies_of_spirituality),
		   );
	}

	// public function remove_alert_by_id($alert_id)
	// {
	// 	if (!empty($alert_id)) {
	// 		// var_dump($alert_id);exit;
	// 		$this->db->where('id', $alert_id);
	// 		$this->db->delete('alert_message'); 
	// 		return $this->db->affected_rows();
	// 	}
	// }

	public function remove_alert_by_user_id($user_id,$table_name)
	{
		if (!empty($user_id) && !empty($table_name)) {
			$this->db->where('user_id', $user_id);
			$this->db->where('table_name', $table_name);
			$this->db->delete('alert_message'); 
			return $this->db->affected_rows();	
		}
	}

	public function del_all_alert_by_user_id($user_id)
	{
		if (!empty($user_id)) {

			$this->db->where('user_id', $user_id);
			$this->db->delete('alert_message'); 
			return $this->db->affected_rows();
		}
	}

	/**
			update 12-21
	*/		
	public function admin_login_log($admin_id)
	{
		if (!empty($admin_id)) {

			$data = array(
			               'admin_id' => $admin_id ,
			               'login_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
			            );

			$this->db->insert('admin_login_log', $data); 	
			return true;	
		}else{
			return false;
		}
	}		

	
}