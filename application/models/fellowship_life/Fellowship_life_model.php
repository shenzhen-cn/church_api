<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fellowship_life_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
	}
	
	public function create_album($user_id,$album_name,$group_id)
	{
		if (! empty($user_id) && ! empty($album_name) && ! empty($group_id) ) {
			
			$this->db->select('id');
			$this->db->from('user_album');
			// $this->db->where('user_id', $user_id);
			$this->db->where('group_id', $group_id);
			$this->db->like('album_name', $album_name, 'both');
			$this->db->where('deleted_at is null');
			$return_id  = $this->db->get()->first_row();
			$isset_album_name = isset($return_id->id) &&  ! empty($return_id->id) ? "Y" : "N" ;  

			// var_dump($album_id);exit();
			if ($isset_album_name == "N") {

				$this->db->set('user_id', $user_id);
				$this->db->set('group_id', $group_id);
				$this->db->set('album_name', $album_name);
				$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
				$this->db->insert('user_album');

				return $this->db->insert_id();	

			}else{

				return $isset_album_name;
			}
			

		}else {
			return false;
		}
	}	
	
	public function get_user_album_name($user_id)
	{
		if (!empty($user_id)) {
			
			$this->db->select('id,album_name');
			$this->db->from('user_album');
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			return $this->db->get()->result();			

		}else {
			return false;
		}
	}

	public function save_data($album_id,$paths)
	{	

		if (is_string($paths)) {
			$insert_id_array = array(); 
			$_paths	  =  explode(',',$paths);

			foreach ($_paths as $key => $v_paths) {
				$this->db->set('album_id', $album_id);
				$this->db->set('paths', $v_paths);
				$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
				$this->db->insert('user_album_src');
				$insert_id_array[] = $this->db->insert_id();
			}

			return $insert_id_array;
					
		} else {
			return false;
		}	
	}

	public function rename_album_name($album_id,$album_name)
	{
		if(!empty($album_id) && !empty($album_name)){
			$params = array('album_name' => $album_name);
			$this->db->where('id', $album_id);
			$this->db->update('user_album', $params);
			return $this->db->affected_rows();
		}else{
			return false;
		}
	}

	public function group_albums($group_id)
	{
		if (!empty($group_id)) {
			$data_return = array();

			$this->db->select('user_album.id as album_id,album_name,user_id');
			$this->db->from('user_album');
			$this->db->where('group_id', $group_id);
			$this->db->where('deleted_at is null');
			$this->db->order_by('id', 'desc');
			$data1 =  $this->db->get();			

			foreach ($data1->result() as $k => $v) {
				// var_dump($v);exit;
				$album_id = $v->album_id;
				$user_id = $v->user_id;
				// var_dump($user_id);exit;
				$data_return[$k]['album_id']   =  $album_id; 
				$data_return[$k]['user_id']   =  $user_id; 
				$data_return[$k]['album_name'] =  $v->album_name; 

				$this->db->select('count(*) as count');
				$this->db->from('user_album_src');				
				$this->db->where('album_id', $album_id);
				$this->db->where('deleted_at is null');
				// $this->db->order_by('id', 'desc');
				$data2  = $this->db->get()->first_row();
				$data_return[$k]['photos_count'] =  isset($data2->count) ? $data2->count : "" ; 

				$this->db->select('nick');
				$this->db->from('user');
				$this->db->where('id', $user_id);
				$this->db->where('deleted_at is null');
				$data3 =  $this->db->get()->first_row();

				$data_return[$k]['nick'] =  isset($data3->nick) ? $data3->nick : "" ;
				// var_dump($data3);exit;
			}
					
			return $data_return;

		}else {
			return false;
		}
	}

	public function see_user_albums($user_id)
	{
		if (!empty($user_id)) {
			$data_return = array();
			$this->db->select('id,album_name');
			$this->db->from('user_album');
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			$this->db->order_by('id', 'desc');
			$data1  =  $this->db->get()->result();
			// var_dump($data1);exit;
			foreach ($data1 as $k => $v1) {
				$album_id = $v1->id;
				$album_name = $v1->album_name;
				$data_return[$k]['album_id'] = $album_id;  
				$data_return[$k]['album_name'] = $album_name;  
				$this->db->select('count(*) as count');
				$this->db->from('user_album_src');
				$this->db->where('album_id', $album_id);
				$this->db->where('deleted_at is null');
				$data2 = $this->db->get()->first_row();
				$data_return[$k]['photos_count'] = isset($data2->count) ? $data2->count : "" ;

				// var_dump($photos_count);exit;

			}

			return $data_return;
			
		}else {
			return false;
		}	
	}
	
	public function see_user_photos($album_id,$limit, $offset)
	{
		if (!empty($album_id)) {		
			$data_return = array();
			$this->db->select('user_album_src.id as src_id ,user_id,album_id,album_name,paths,user_album_src.created_at as src_created_at');
			$this->db->from('user_album_src');
			$this->db->join('user_album', 'user_album.id = user_album_src.album_id', 'left');
			$this->db->where('album_id', $album_id);
			$this->db->where('user_album_src.deleted_at is null');
			$this->db->where('user_album.deleted_at is null');
			$this->db->limit($limit, $offset);			
			$this->db->order_by('user_album_src.created_at', 'desc');
			$temp_data =  $this->db->get()->result();
			if(!empty($temp_data)){
				foreach ($temp_data as $k => $v) {
					// var_dump($v);exit;
					$src_created_at = $v->src_created_at;
					$src_created_at = $this->tranTime(strtotime($src_created_at));
					$data_return[] = array(
							'src_id' =>$v->src_id ,
							'created_user_id' =>$v->user_id ,
							'album_id' =>$v->album_id ,
							'album_name' =>$v->album_name,
							'paths' =>$v->paths ,
							'src_created_at' =>$src_created_at ,
						);
				}
			}
			return $data_return;
			
		}else {
			return false;
		}		
	}

	public function get_photos_count($group_info)
	{
		$data_return = array();
		if (!empty($group_info)) {
			foreach ($group_info as $k => $v) {
				$group_id = $v->id;
				$data_return[$k]['group_name'] = $v->group_name;
				$data_return[$k]['group_id'] = $group_id;
				
				$this->db->select('count(*) as count');						
				$this->db->from('user_album');
				$this->db->where('group_id', $group_id);
				$this->db->where('deleted_at is null');
				$data = $this->db->get()->first_row();
				$data_return[$k]['group_album_count'] = !empty($data->count) ? $data->count : "" ;
			}
			
			return $data_return;
		}else {
			return false;
		}
	}

	public function del_photos($src_id,$user_id='',$admin_id="")
	{
		if (!empty($user_id)) {
			
			$params = array(

				'deleted_by' => $user_id,
				'deleted_by_user' =>'Y',
				'deleted_by_group_leader' =>'N',
				'deleted_by_admin' =>'N',
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now())
			 );
		}else if (!empty($admin_id)) {

			$params = array(

				'deleted_by' => $admin_id,
				'deleted_by_user' =>'N',
				'deleted_by_group_leader' =>'N',
				'deleted_by_admin' =>'Y',
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now())
			 );
		}else {
			return false;
		}
		$this->db->where('id', $src_id);
		$this->db->update('user_album_src', $params);
		return  $this->db->affected_rows();	
	}

	public function del_alert_user_album_src($src_id)
	{		
		$this->db->where('table_name', 'user_album_src');
		$this->db->where('table_id', $src_id);
		$this->db->delete('alert_message'); 
		return $this->db->affected_rows();

	}

	public function recently_fellowship_photos($regtime)
	{	
		$data_array = array();
		$this->db->select('id,paths,album_id');
		$this->db->from('user_album_src');
		$this->db->where('created_at<', $regtime);
		$this->db->where('deleted_at is null');
		$this->db->order_by('id', 'desc');
		$data1 = $this->db->get('', '4')->result();

		if (!empty($data1)) {			
			foreach ($data1 as $k => $v) {
				// var_dump($v);exit;
				$album_id = $v->album_id;
				$data_array[$k]['src_id'] = $v->id;
				$data_array[$k]['paths'] = $v->paths;
				$data_array[$k]['album_id'] = $album_id;

				$this->db->select('album_name,nick');
				$this->db->from('user_album');
				$this->db->join('user', 'user.id = user_album.user_id', 'left');
				$this->db->where('user_album.id', $album_id);
				$this->db->where('user_album.deleted_at is null');
				$data2 = $this->db->get()->first_row();
				// var_dump($data2);exit;
				// echo $this->db->last_query();exit;
				$data_array[$k]['album_name'] = isset($data2->album_name) ? $data2->album_name : "" ;
				$data_array[$k]['nick'] = isset($data2->nick) ? $data2->nick : "" ;


			}
			return $data_array;
				// var_dump($data_array);exit;
		}else {
			return false;
		}
	}

	public function get_today_user_photos($prev_day='')
	{
        $regtime        =  date("Y-m-d",time());        
		$this->db->select('max(created_at) as created_at');
		$this->db->from('user_album_src');

		if (empty($prev_day)) {			
			$this->db->where('created_at <', $regtime);
		}else {	
			$prev_day =  date("Y-m-d",strtotime($prev_day));
			$this->db->where('created_at <', $prev_day);
		}

		$this->db->where('deleted_at is null');
		$data1 = $this->db->get()->first_row();
		$prev_created_at =  isset($data1->created_at) ? $data1->created_at :""; 
		// echo $this->db->last_query();exit;
		// var_dump($data1);exit; 
		$this->db->select('user_album_src.id as album_src_id, album_id,user_album_src.created_at ');
		$this->db->from('user_album_src');
		if (empty($prev_day)) {			
			$this->db->where('created_at > ', $regtime);
		}else {
			$prev_prev_day    =  date("Y-m-d",strtotime($prev_day)-24*3600);
			// var_dump($prev_prev_day);exit;
			$this->db->where('created_at < ', $prev_day);
			$this->db->where('created_at > ', $prev_prev_day);

		}
		$this->db->where('deleted_at is null');
		$this->db->order_by('user_album_src.created_at', 'desc');
		$data2 = $this->db->get()->result();
		// echo $this->db->last_query();exit;
		// var_dump($data2);exit;
		$temp_array1 = array();

		foreach ($data2 as $k1 => $v2) {
			$created_at = $v2->created_at;
			$temp_array1[] = $created_at; 
		}
		
		$temp_array2 = array_unique($temp_array1);
		$data3 = array();
		$data4 = array();
		foreach ($temp_array2 as $k2 => $v2) {

			$v2 =  date("Y-m-d H:i",strtotime($v2));
			$this->db->select('user_album_src.id as src_id,album_id,album_name,paths,user_album_src.created_at as src_created_at,nick,user_id');
			$this->db->from('user_album_src');
			$this->db->join('user_album', 'user_album.id = user_album_src.album_id', 'left');
			$this->db->join('user', 'user.id = user_album.user_id', 'left');
			$this->db->like('user_album_src.created_at', $v2.':', 'after');
			$this->db->where('user_album_src.deleted_at is null ');
			$this->db->where('user_album.deleted_at is null ');
			$this->db->where('user.deleted_at is null ');
			$this->db->order_by('src_created_at', 'desc');
			$data3[$k2][$v2] = $this->db->get()->result_array();
			$data3[$k2]['nick'] = $data3[$k2][$v2]['0']['nick'];
			$data3[$k2]['user_id'] = $data3[$k2][$v2]['0']['user_id'];

			for ($i=0; $i < count($data3[$k2][$v2]) ; $i++) { 
				$conversion_time = $data3[$k2][$v2][$i]['src_created_at'];
				$conversion_time = $this->tranTime(strtotime($conversion_time));
				$data3[$k2]['src_created_at'] = $conversion_time; 
			}
		}

		return  array('prev_day' => $prev_created_at,'results'  => $data3);					


	}

	// public function tranTime($time) {

	//     $rtime = date("m-d H:i", $time);
	//     $htime = date("H:i", $time);
	//     $time = time() - $time;
	 
	//     if ($time < 60) {
	//         $str = '刚刚';
	//     } elseif ($time < 60 * 60) {
	//         $min = floor($time / 60);
	//         $str = $min.'分钟前';
	//     } elseif ($time < 60 * 60 * 24) {
	//         $h = floor($time / (60 * 60));
	//         $str = $h.'小时前 '.$htime;
	//     } elseif ($time < 60 * 60 * 24 * 3) {
	//         $d = floor($time / (60 * 60 * 24));
	//         $str = ($d == 1) ? '昨天 '.$rtime : '前天 '.$rtime;
	//     } else {
	//         $str = $rtime;
	//     }
	 
	//     return $str;
	// }

	public function alert_user_album_src($insert_id,$using_user_id)
	{
		if (!empty($insert_id)) {
			
			foreach ($insert_id as $k => $v) {
				$data_array  = array();
				$this->db->select('id');
				$this->db->from('user');
				$this->db->where('id<>', $using_user_id);
				$this->db->where('deleted_at is null');
				$users_id  = $this->db->get()->result_array();
				foreach ($users_id as $key => $value) {
					$user_id = $value['id'];
					// if($using_user_id == $user_id)
					$data_array[]= array(
									'table_name' => 'user_album_src' ,
								      'table_id' => $v ,
								      'user_id' => $user_id,
								      'created_at' => mdate('%Y-%m-%d %H:%i:%s', now())
									);
				}

				$this->db->insert_batch('alert_message', $data_array); 
				$data_temp[] =  $this->db->affected_rows();				
			}		

			return $data_temp;	

		}
	}

	public function get_all_user_photos($limit, $offset)
	{
		$data_return = array();
		$this->db->select('user_album_src.id,paths,album_id,user_album_src.created_at,album_name,user_id');
		$this->db->from('user_album_src');
		$this->db->join('user_album', 'user_album.id = user_album_src.album_id', 'left');
		$this->db->where('user_album_src.deleted_at is null');
		$this->db->where('user_album.deleted_at is null');
		$this->db->limit($limit, $offset);
		$this->db->order_by('user_album_src.id', 'desc');
		$result = $this->db->get()->result();

		if (!empty($result)) {
			foreach ($result as $k => $v) {
				$album_user_id = $v->user_id;
				$created_at = $v->created_at;
				$album_src_created_at = $this->tranTime(strtotime($created_at));
				$album_user_info = $this->get_user_all_info($album_user_id);	
				// var_dump($album_user_info);exit;
				$data_return[] = array(
								'user_album_src_id' => $v->id,
								'user_album_src' => $v->paths,
								'user_album_id' => $v->album_id,
								'album_src_created_at' => $album_src_created_at,
								'user_album_name' => $v->album_name,
								'album_user_id' => $album_user_id,
								'user_nick' => $album_user_info['nick'],
								'userHead_src' => $album_user_info['userHead_src'],
								 );				

			}
		}

		return $data_return;
	}

	public function count_user_photos()
	{
		$this->db->select('count(*) as counts');
		$this->db->from('user_album_src');
		$this->db->where('deleted_at is null');
		$count = $this->db->get()->first_row();
		if(!empty($count)){
			return $count->counts;
		}else{
			return false;
		}
	}

	public function count_user_photos_by_album_id($album_id)
	{
		if(!empty($album_id)){
				$this->db->select('count(*) as count');
				$this->db->from('user_album_src');
				$this->db->where('album_id', $album_id);
				$this->db->where('user_album_src.deleted_at is null');
				$count = $this->db->get()->first_row();
				$count = isset($count) ? $count->count : "";	
				return $count;
		}else {
			return false;
		}
	}
}
	
	
	
	
	
	
	
	
	
