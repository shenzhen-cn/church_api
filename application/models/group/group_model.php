<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Group_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
		$this->load->model('/tq_header_info_model');		
	}
	
	public function addGroup($groupName,$admin_id)
	{	
		if (!empty($groupName)) {

			$this->db->select('id');
			$this->db->from('church.group');
			$this->db->where('group_name', $groupName);

			$group_id = $this->db->get()->result();

			if (empty($group_id)) {

				$this->db->set('group_name', $groupName);
				$this->db->set('created_by_admin_id', $admin_id);
				$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));

				$this->db->insert('group');

				return $this->db->insert_id();
			}else {

				return false;
			}

		}else{
			return false;
		}
	}

	public function get_group()
	{
		$this->db->select('group.id as group_id, user.id as user_id,nick, group_name,group_leader_id,group.created_at');
		$this->db->from('group');	
		$this->db->join('user', 'user.id = group.group_leader_id ', 'left');
		$this->db->where('group.deleted_at is null');
		$this->db->where('user.deleted_at is null');
		$this->db->order_by('group.id','DESC');
		return $this->db->get()->result_array();
		// echo $this->db->last_query();exit();
	}
	
	public function find_user_by_group_id($group_id)
	{
		if (!empty($group_id)) {
			
			$this->db->select('id,nick');
			$this->db->from('user');
			$this->db->where('group_id', $group_id);
			$this->db->where('deleted_at is null ');
			return 	$this->db->get()->result();

		}else{

			return false;
		}
	}

	public function del_group($group_id)
	{
		if (!empty($group_id)) {

			$params  = array(
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
				'group_leader_id' => '0' , 
				);

			$this->db->where('id', $group_id);
			$this->db->update('church.group', $params);
			return  $this->db->affected_rows();

		}else{
			return false;
		}
	}
	
	public function groupEdit($group_id,$group_name,$group_leader_id)
	{
		if (!empty($group_id) && !empty($group_name) && !empty($group_leader_id)) {

			$params  = array(
				'group_leader_id' 	=>  $group_leader_id, 
				'group_name' 		=>  $group_name, 
				'updated_at' 		=> mdate('%Y-%m-%d %H:%i:%s', now()) , 
				);

			$this->db->where('id', $group_id);
			$this->db->update('church.group', $params);
			return  $this->db->affected_rows();

		}else {
			return false;
		}
	}
	
	public function find_group_by_group_id($group_id)
	{
		if (! empty($group_id)) {

			$data_return  = array();

			$this->db->select('id,group_name,group_leader_id');
			$this->db->from('church.group');
			$this->db->where('id',$group_id);
			$this->db->where('deleted_at is null');
			$data_return = $this->db->get()->first_row();

			return $data_return;

		}else{
			return false;
		}
	}

	public function find_all_users_by_group_id($group_id)
	{
		$data_return  = array();
		$data_array   = array();

		if (!empty($group_id)) {

			$this->db->select('group_name');
			$this->db->from('group');
			$this->db->where('id', $group_id);
			$this->db->where('group.deleted_at is null');
			$data3 = $this->db->get()->first_row();
			$group_name = isset($data3->group_name) ? $data3->group_name : "";
			// var_dump($group_name);exit();
			$this->db->select('user.id as user_id,nick');
			$this->db->from('user');
			$this->db->where('group_id', $group_id);
			$this->db->where('user.deleted_at is null');
			$data =  $this->db->get();
			// echo $this->db->last_query(); exit();
			// var_dump($data->result_array());exit();
			foreach($data->result_array() as $row_cer){
				$user_id = $row_cer['user_id'];

				if ($user_id > 0 ) {

					$data1 = $this->get_user_all_info($user_id);
//					var_dump($data1);exit();
//					$sql= '
//							SELECT
//							    user.id AS user_id, nick, group_id, userHead_src,sex,user.created_at
//							FROM
//							    church.user
//							        LEFT JOIN
//							    church.`group` ON `group`.id = user.group_id
//							        LEFT JOIN
//							    church.userhead_src ON userhead_src.user_id = user.id
//							WHERE
//							    (update_at = (SELECT
//							            MAX(update_at)
//							        FROM
//							            church.userhead_src
//							        WHERE
//							            user_id = '.$user_id.' AND deleted_at IS NULL))
//							        AND user.deleted_at IS NULL AND `group`.deleted_at IS NULL AND userhead_src.deleted_at IS NULL and user.group_id = '.$group_id;
//						$data1 = $this->db->query($sql)->result();

//					echo $sql;exit;
//				var_dump($data1);exit;
					if(!empty($data1)){
						$row_cer['userHead_src'] = $data1['userHead_src'];
						$row_cer['created_at']   = $data1['user_created_at'];
						$row_cer['sex'] = $data1['sex'];

//						var_dump($row_cer);exit;
//							var_dump($data1);exit;
//						foreach ($data1 as $row_cer2) {
//							if(!empty($row_cer2['userHead_src'])){
//
//								$row_cer['userHead_src'] = $row_cer2['userHead_src'];
//							}
//							var_dump($row_cer);exit;
//							$row_cer['created_at'] = $row_cer2['created_at'];
//							$row_cer['sex'] = $row_cer2['sex'];
//
							$this->db->select('count(*) as count_spirituality');
							$this->db->from('spirituality');
							$this->db->where('user_id ', $user_id );
							$this->db->where('deleted_at  is  null');
							$data2 = $this->db->get()->first_row();
//							var_dump($data2);exit;
							$row_cer['count_spirituality'] = $data2->count_spirituality;
							$data_array[] = $row_cer;
//						}

//						var_dump();exit;
					}

//					echo "end";exit;
				}

			}
//			var_dump($data_array);exit;
			$data_array = $this->arraySort($data_array, 'count_spirituality', 'desc');

			return $data_return = array(
				'group_name' => $group_name,
				'data_array' => $data_array
				);


		}else{
			return false;
		}
	}

	public function arraySort($arr, $keys, $type = 'asc') {

        $keysvalue = $new_array = array();

        foreach ($arr as $k => $v){
        	// var_dump($v[$keys]);exit();
            $keysvalue[$k] = $v[$keys];
        }
        $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);

        reset($keysvalue);

        foreach ($keysvalue as $k => $v) {
           $new_array[] = $arr[$k];
        }

        return $new_array;
	}


	public function spirituality($testament,$book_id,$chapter_id)
	{
		if (!empty($testament) && !empty($book_id) && !empty($chapter_id)) {
			$this->db->select('name,chapter_id,section,content');
			$this->db->from('bible_section');
			$this->db->join('bibile_book', 'bibile_book.id = bible_section.book_id', 'left');
			$this->db->where('bibile_book.testament', $testament);
			$this->db->where('book_id', $book_id);
			$this->db->where('chapter_id', $chapter_id);
			return $this->db->get()->result();

		}else{
			return false;
		}
	}

	public function setting_spirituality($testament,$book_id,$chapter_id,$group_id)
	{
		if (!empty($testament) && !empty($book_id) && !empty($chapter_id) && !empty($group_id)) {
			
			$this->db->select('id');
			$this->db->from('setting_spirituality');
			$this->db->where('group_id', $group_id);

			$setting_s_id = $this->db->get()->first_row();

			if (empty($setting_s_id->id)) {
				
				$data = array(
				               'testament' => $testament,
				               'book_id' => $book_id,
				               'chapter_id' => $chapter_id,
				               'group_id' => $group_id,
				               'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
				               'updated_at' => date("Y-m-d",time()+24*3600)
				               
				            );

				$this->db->insert('setting_spirituality', $data);
				return $this->db->insert_id();

			}else{

				$data = array(
				               'testament' => $testament,
   				               'book_id' => $book_id,
   				               'chapter_id' => $chapter_id,
   				               'group_id' => $group_id,
   				               'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
   				               'updated_at' => date("Y-m-d",time()+24*3600)
				            );
				 
				$this->db->where('id', $setting_s_id->id);
				$this->db->update('setting_spirituality', $data);
				return $this->db->affected_rows();
			}
			
		}else{
			return false;
		}
	}

	public function del_user_spirituality($s_id,$s_u_id,$user_id)
	{
		if (!empty($s_id) && !empty($s_u_id) && !empty($user_id)) {
			
			$params  = array(
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
				'deleted_by' =>  $user_id, 
				);

			$this->db->where('id', $s_id);
			$this->db->where('user_id', $s_u_id);
			$this->db->update('spirituality', $params);
			return  $this->db->affected_rows();

		}else{
			return false;
		}
	}

	public function check_nextday($group_id,$today,$lastday,$user_info)
	{
		$data_return = array();
		$results_return = array();	
		$count = 0;
		$today_created_at = null;
			
		if (!empty($group_id) && !empty($user_info) ) {

			foreach ($user_info as $k => $row_cer) {
				$user_id = $row_cer['user_id'];

				$this->db->select('id as spirituality_id ,gold_sentence,heart_feeling,response,created_at');
				$this->db->from('spirituality');
				$this->db->where('user_id ', $user_id);
				$this->db->where('created_at < ', $today);
				$this->db->where('created_at > ', $lastday);
				// $this->db->where('group_id',$group_id);
				$this->db->where('deleted_at is null');
				$user_s =  $this->db->get();
				
				foreach ($user_s->result_array() as $row_cer1) {
					$row_cer['spirituality_id'] = isset($row_cer1['spirituality_id']) ? $row_cer1['spirituality_id'] : "" ; 	
					$row_cer['gold_sentence'] = isset($row_cer1['gold_sentence']) ? $row_cer1['gold_sentence'] : "";  	
					$row_cer['heart_feeling'] = isset($row_cer1['heart_feeling']) ? $row_cer1['heart_feeling'] : "" ; 	
					$row_cer['response'] = isset($row_cer1['response']) ? $row_cer1['response'] : "" ; 	
					$row_cer['created_at'] = isset($row_cer1['created_at']) ? $row_cer1['created_at'] : "" ; 	
					$count++ ; 
					$today_created_at = isset($row_cer1['created_at']) ? $row_cer1['created_at'] : "" ;  					
				}

				$data_return[] = $row_cer; 

			}

			$results_return['count'] = $count; 
			$results_return['data_return'] = $data_return;
			$results_return['today_created_at'] = $today_created_at;
			 
			// return $count;	
			return 	$results_return;
		}else{
			return false;
		}

	}

	public function find_week_s_report($group_id,$user_info,$week )
	{
		if (!empty($group_id)) {
			
			
			$data_return  = array();
			// var_dump($user_info);exit;
			// $month  =  $this->get_month_days($regtime);
			foreach ($user_info as $k => $row_cer) {
				$user_id = $row_cer['user_id'];

				$this->db->select('count(*) as s_count_week');
				$this->db->from('spirituality');
				$this->db->where('user_id', $user_id);
				$this->db->where('created_at > ', $week['week_firstday']);
				$this->db->where('created_at < ', $week['week_lastday']);
				$this->db->where('deleted_at is null');
				$data = $this->db->get();

				foreach ($data->result_array() as $k => $v) {
					$row_cer['s_count_week'] = is_numeric($v['s_count_week']) ? $v['s_count_week']  : "" ;
					$row_cer['s_rate_week']  = $this->get_percentage($row_cer['s_count_week'],7);

				}

				$data_return[] = $row_cer;
			}

				// var_dump($data_return);exit();
				$data_array = $this->arraySort($data_return, 's_count_week', 'desc');
				// var_dump($data_array);exit();
				$arr = $this->get_ranking($data_array, 's_rate_week');
				// var_dump($arr);exit();
				$data_array1 = array();
				foreach ($data_array as $k => $row_cer) {
						// var_dump($k);exit();
					$row_cer['s_rank_week'] = $arr[$k]['s_rank_week'];
				
					$data_array1[] = $row_cer;					
				}
				// var_dump($arr);exit();

			// var_dump($data_array1);exit();
			return $data_array1;
		}else{
			return false;
		}
	}		

	public function get_ranking($data,$keys)
	{
		$keysvalue = $new_array = array();
		foreach ($data as $k => $v) {
			$keysvalue[$k]= $v[$keys];
		}

		$temp2 = array();

		for ($i=0,$m=0; $i < count($keysvalue); $i++) { 

				switch ($keysvalue[$i]) {
					case !isset($keysvalue[$i+1]):
						$new_array[]['s_rank_week'] = $m;
						break;
					case $keysvalue[$i] > $keysvalue[$i+1]:
						$new_array[]['s_rank_week'] = $m;
						$m++;
						break;
					case $keysvalue[$i] == $keysvalue[$i+1]:
						$new_array[]['s_rank_week'] = $m;
						break;
					default:
						break;
				}
			
		}
		return $new_array;

	}

	public function setting_group_prayer($group_prayer_content,$group_id)
	{
		if (! empty($group_prayer_content) && ! empty($group_id)) {

			$regtime = date("Y-m-d",time()); 

			$this->db->select('max(created_at) as created_at');
			$this->db->from('group_prayer');
			$this->db->where('created_at >', $regtime);
			$this->db->where('group_id', $group_id);
			$this->db->where('deleted_at is null');
			$data1 = $this->db->get()->first_row();
			$max_created_at =  isset($data1->created_at) ? $data1->created_at : "";
			// var_dump($max_created_at);exit;
			if (empty($max_created_at)) {
				$data =array(
				           'group_prayer_content' => $group_prayer_content,
				           'group_id' => $group_id,
				           'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
				           'overdue_at' => date("Y-m-d",time()+24*3600)
					);

				$this->db->insert('group_prayer', $data);
				return $this->db->insert_id();
				
			}else {

				$params  = array(
					'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
					'group_prayer_content' => $group_prayer_content , 
					);

				$this->db->where('group_id', $group_id);
				$this->db->where('created_at > ', $regtime);
				$this->db->update('group_prayer', $params);
				return  $this->db->affected_rows();
			}


		}else{
			return false;
		}	
	}
	
	public function get_today_group_prayer($group_id)
	{
		if (!empty($group_id)) {
			$regtime = date("Y-m-d",time()); 
			$this->db->select('id,group_prayer_content,created_at');
			$this->db->from('group_prayer');
			$this->db->where('group_id', $group_id);
			$this->db->where('created_at >', $regtime);
			return $this->db->get()->first_row();
		}else {
			return false;
		}
	}

	public function see_member($group_user_id,$user_id='',$limit='', $offset='',$count='',$page='')
	{	
		if (!empty($group_user_id)) {
			$spirituality_results = array(); 
			$temp_data5 = array();
			$userHead_src = $this->tq_header_info_model->finduserHeadSrc($group_user_id);
			$group_user_info = $this->tq_header_info_model->findUser($group_user_id);

			$this->load->model('user/user_model');
			$spirituality_results  = $this->user_model->get_personal_data_for_spirituality($group_user_id,$user_id,$limit, $offset);

			$prayer_results = $this->get_all_user_prayer($group_user_id,$count,$page);
			// var_dump($prayer_results);exit;
			//灵修评论
			// var_dump($spirituality_results);exit;
			$this->db->select('count(*) as count');
			$this->db->from('spirituality');
			$this->db->where('user_id', $group_user_id);
			$this->db->where('deleted_at is null');
			$data2 = $this->db->get()->first_row();
			$spiri_total_count = !empty($data2->count) ? $data2->count : "";  
			// var_dump($spiri_total_count);exit;
			// 
			$regtime   =  date("Y-m-d",time());
			$date_array = $this->get_week_days($regtime);
			// var_dump($group_user_id);

			$this->db->select('count(*) as count');
			$this->db->from('spirituality');
			$this->db->where('user_id', $group_user_id);			
			$this->db->where('created_at <', $date_array['week_lastday']);
			$this->db->where('created_at >', $date_array['week_firstday']);
			$this->db->where('deleted_at is null');
			$data3 = $this->db->get()->first_row();
			$spiri_week_count = !empty($data3->count) ? $data3->count : "";
			// var_dump($spiri_week_count);
			
			$this->db->select('count(*) as count');
			$this->db->from('prayer_for_group');
			$this->db->where('user_id', $group_user_id);			
			$this->db->where('created_at <', $date_array['week_lastday']);
			$this->db->where('created_at >', $date_array['week_firstday']);
			$this->db->where('deleted_at is null');
			$data4 = $this->db->get()->first_row();
			$prayer_group_week_count = !empty($data4->count) ? $data4->count : "";

			$this->db->select('count(*) as count');
			$this->db->from('prayer_for_urgent');
			$this->db->where('user_id', $group_user_id);			
			$this->db->where('created_at <', $date_array['week_lastday']);
			$this->db->where('created_at >', $date_array['week_firstday']);
			$this->db->where('deleted_at is null');
			$data5 = $this->db->get()->first_row();
			$urgent_group_week_count = !empty($data5->count) ? $data5->count : "";			

			// var_dump($urgent_group_week_count);exit;

			$this->db->select('count(*) as count');
			$this->db->from('prayer_for_group');
			$this->db->where('user_id', $group_user_id);			
			$this->db->where('deleted_at is null');
			$data6 = $this->db->get()->first_row();
			$prayer_group_total_count = !empty($data6->count) ? $data6->count : "";
			// echo $this->db->last_query();exit;
			// var_dump($prayer_group_total_count);exit;

			$this->db->select('count(*) as count');
			$this->db->from('prayer_for_urgent');
			$this->db->where('user_id', $group_user_id);			
			$this->db->where('deleted_at is null');
			$data6 = $this->db->get()->first_row();
			$urgent_group_total_count = !empty($data6->count) ? $data6->count : "";						

			$group_ranking_result = $this->spirituality_group_ranking($group_user_id);
			$tq_ranking_result = $this->spirituality_tq_ranking($group_user_id);			

			return  array(  
							'userHead_src' => $userHead_src,
							'group_user_info' => $group_user_info,
							'spirituality_results' =>$spirituality_results,
							'prayer_results' => $prayer_results['praies'],
							'page_array' =>$prayer_results['page_array'],
							'spiri_total_count' => $spiri_total_count,
							'spiri_week_count' => $spiri_week_count,
							'prayer_group_week_count' => $prayer_group_week_count,
							'urgent_group_week_count' => $urgent_group_week_count,
							'prayer_group_total_count' => $prayer_group_total_count,
							'urgent_group_total_count' => $urgent_group_total_count,
							'group_ranking_result' => $group_ranking_result,
							'tq_ranking_result' => $tq_ranking_result,							
						 );



		}else {
			return false;
		}
	}

	public function count_spirituality_by_user_id($group_user_id)
	{
		if (!empty($group_user_id)) {
			$count_spirituality = null;
			$this->db->select('count(*) as count ');
			$this->db->from('spirituality');
			$this->db->where('user_id', $group_user_id);
			$this->db->where('deleted_at is null');
			$result = $this->db->get()->first_row();

			if (!empty($result)) {
				$count_spirituality = $result->count;
			}

			return $count_spirituality;

		}
	}

	public function get_week_days($day){ 

	    if (!empty($day)) {

	     	$week_lastday  = date('Y-m-d',strtotime("$day Sunday")); 
	     	$week_firstday = date('Y-m-d',strtotime("$week_lastday -6 days")); 

	     	return array('week_firstday' => $week_firstday,'week_lastday' => $week_lastday);
	     } 
	} 

	public function get_all_user_prayer($user_id,$count='',$page='')
	{
		$data_return1 = array();
		$this->db->select('prayer_for_urgent.id as urgent_id ,prayer_for_urgent.created_at, content_prayer');
		$this->db->from('prayer_for_urgent');
		$this->db->where('user_id',$user_id);
		$this->db->where('prayer_for_urgent.deleted_at is  null');
		$this->db->order_by('urgent_id', 'desc');
		$data1 =  $this->db->get()->result_array();

		foreach ($data1 as $row_cer) {
			$created_at = $row_cer['created_at'];
			$row_cer['conversion_time'] = $this->tranTime(strtotime($created_at));	
        	$data_return1[] = $row_cer; 
		}

		$data_return2 = array();
		$this->db->select('prayer_for_group.id as group_prayer_id ,prayer_for_group.created_at, group_prayer_contents');
		$this->db->from('prayer_for_group');
		$this->db->where('user_id',$user_id);
		$this->db->where('prayer_for_group.deleted_at is  null');
		$this->db->order_by('group_prayer_id', 'desc');
		$data2 =  $this->db->get()->result_array();

		foreach ($data2 as $row_cer) {

			$created_at = $row_cer['created_at'];
			$row_cer['conversion_time'] = $this->tranTime(strtotime($created_at));	
			
        	$data_return2[] = $row_cer; 
		}

		$data_return3 = array_merge($data_return1,$data_return2);
				
		usort($data_return3, function($a, $b) {
	        $al = $a['created_at'];
	        $bl = $b['created_at'];
	        if ($al == $bl)
	            return 0;
	        return ($al > $bl) ? -1 : 1;
		}); 		 
		$this->load->model('prayer/prayer_model');

		global $countpage;
		$temp_data1 = array();
		$temp_data2 = array();

		$temp_data1 = $this->prayer_model->page_array($count,$page,$data_return3,0);
		$temp_data2 = $this->prayer_model->show_array($countpage,$url='');


		return 	array('praies' =>$temp_data1,'page_array' =>$temp_data2);
	}


	
	public function spirituality_group_ranking($user_id,$start_date='',$end_date='')
	{
		$sql = "select group_concat(a.user_id) as  user_id,a.num
				from 
				(
					SELECT spirituality.user_id,count(*) as num 
						FROM church.spirituality
						left join user on user.id = spirituality.user_id
						where user.group_id = (select group_id from church.user where user.id = '$user_id')
						and  spirituality.deleted_at is null
						and  spirituality.created_at >= '$start_date 00:00:00'
						and  spirituality.created_at <= '$end_date 23:59:59'						
							group by user_id	
							order by num desc,user_id asc
				) as a 
					group by a.num
				    order by a.num desc;
				";
		$query =  $this->db->query($sql)->result_array();
		// var_dump($query);exit;
		if (!empty($user_id)) {
			foreach ($query as $key => $row) {
				$temp_str  = $row['user_id'];
				$bool   =   strpos($temp_str, ',');     						
				if ($bool) {
					$temp_array=explode(',',$temp_str); 
					foreach ($temp_array as $k => $value) {
						if ($value == $user_id) {
							return 	$key+1;
						}
					}
				}else if($temp_str == $user_id){
					return $key+1;
				}
			}
		}else {
			return $query;
		}
	}	

	public function spirituality_tq_ranking($user_id)
	{
		$sql = 'select group_concat(a.user_id) as  user_id,a.num
				from 
				(
					SELECT spirituality.user_id,count(*) as num 
						FROM church.spirituality
						left join user on user.id = spirituality.user_id
						where spirituality.deleted_at is null						
							group by user_id
							order by num desc,user_id asc
				) as a 
					group by a.num
				    order by a.num desc;
				';
		$query =  $this->db->query($sql)->result_array();

		if (!empty($user_id)) {
			foreach ($query as $key => $row) {
				$temp_str  = $row['user_id'];
				$bool   =   strpos($temp_str, ',');     						
				if ($bool) {
					$temp_array=explode(',',$temp_str); 
					foreach ($temp_array as $k => $value) {
						if ($value == $user_id) {
							return 	$key+1;
						}
					}
				}else if($temp_str == $user_id){
					return $key+1;
				}
			}
		}else {
			return $query;
		}
	}	

	public function get_notice_groups_results($user_id)
	{
		if (!empty($user_id)) {
			$this->db->select('notice_groups.id as notice_groups_id,contents,notice_groups.created_at,alert_message.id as alert_message_id');
			$this->db->from('notice_groups');
			$this->db->join('alert_message', 'alert_message.table_id  = notice_groups.id ', 'left');
			$this->db->where('user_id', $user_id);
			$this->db->where('is_readed', 'N');
			$this->db->where('notice_groups.deleted_at is null');
			$this->db->where('alert_message.deleted_at is null');
			$this->db->order_by('notice_groups.created_at', 'desc');
			return $this->db->get()->result_array();
			// echo $this->db->last_query();exit;

		}else {
			return false;
		}
	}

	public function get_rate_of_spirituality($start_date,$end_date)
	{
		
		//获取所有小组 在该时间段 实际参加次数
 		$this->db->select("`group`.id as group_id,`group`.group_name");
 		$this->db->from('church.spirituality');
 		$this->db->join('user', 'user.id = spirituality.user_id');
 		$this->db->join('group', 'group.id = user.group_id', 'left');
 		$this->db->where("date(spirituality.created_at) >= ", $start_date);
 		$this->db->where('date(spirituality.created_at) <= ', $end_date);
 		$this->db->where('spirituality.deleted_at is null');
 		$this->db->where('user.deleted_at is null');
 		$this->db->where('group.deleted_at is null');

 		$this->db->order_by('group.id', 'desc');
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		if($query->num_rows() > 0){
			$result = $query->result();
		}

		$arr = array();
		foreach ($result as $key => $value) {

			if(!isset($arr[$value->group_id])){
				$arr[$value->group_id] = 1;
			}else{
				$arr[$value->group_id] += 1;
			}
		}
		
		//获取 指定小组在该时间段内总次数
		$arr2 = array();
		foreach ($arr as $key => $value) {
			// var_dump($value);
			$val1 = $this->_get_group_attence($key,$start_date,$end_date);
			$arr2[$key] = $this->get_percentage($value,$val1);						
		}
		natsort($arr2);
		$rank_by_month= array();

		foreach ($arr2 as $k => $v) {
			$temp_data2 = 	$this->find_user_by_group_id($k);			
			$all_attencers = count($temp_data2);
			$data1 = $this->find_group_by_group_id($k);
			$group_leader_id = $data1->group_leader_id;

			$this->load->model('tq_header_info_model');
			$leade_info = $this->tq_header_info_model->findUser($group_leader_id);
			$nick = $leade_info->nick;
			$group_name = $leade_info->group_name;

			$rank_by_month[] = array( 
						'group_id' => $k,
						'rate' => $v,
						'group_name' => $group_name,
						'nick' => $nick,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'count_attencers' => $all_attencers,
						);
		}		

		return $rank_by_month;
	}

	private function _get_group_attence($group_id,$start_date,$end_date){
		$users = $this->find_all_users_by_group_id($group_id);
//		var_dump('sdfsdf');exit;
//		var_dump($users);exit;
		$counter = 0;
		for ($i=0; $i < count($users['data_array']); $i++) { 
			$joinDate = date('Y-m-d',strtotime($users['data_array'][$i]['created_at']));		
			//一个user指定时间段内 参加的次数
			$c = $this->_get_user_attence($joinDate,$start_date,$end_date);

			$counter += $c;
		}
		return $counter;
	}


	private function _get_user_attence($joinDate,$start_date,$end_date){
		if($joinDate <= $start_date){
			$days = $this->diffBetweenTwoDays($end_date,$start_date);

			if ($days < 7) {
				return 7;
			}else {
				return date('t',strtotime($start_date));
			}

		}else if($joinDate > $start_date && $joinDate <= $end_date){
			$differ_date = $this->diffBetweenTwoDays($end_date,$joinDate);
			return $differ_date + 1;
		}else{
			return 0;
		}
	}

	public function delete_spirituality($del_user_id,$spirituality_id)
	{
		if (!empty($del_user_id) && !empty($spirituality_id)) {

			$params  = array(
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
				'deleted_by' =>  $del_user_id, 
				);

			$this->db->where('id', $spirituality_id);
			$this->db->update('spirituality', $params);
			return  $this->db->affected_rows();
			
		}else {
			return false;
		}
	}

	public function delete_alert_about_praise($spirituality_id,$user_id)
	{		
		if(!empty($spirituality_id) && !empty($user_id)){
			//删除赞表
			$this->del_praise_by_pirituality_id($spirituality_id);	

			//删除评论
			$this->del_comments_by_pirituality_id($spirituality_id,$user_id);		

			//删除回复
			$this->del_replies_by_spirituality_id($spirituality_id,$user_id);		

			//删除提示
			$this->del_prompt_by_spirituality_id($spirituality_id);			
			return true;
		
		}else{
			return false;
		}	

	}

	//删除赞表
	public function del_praise_by_pirituality_id($spirituality_id)
	{
		if(!empty($spirituality_id)){
			$this->db->where('spirituality_id', $spirituality_id);
			$this->db->update('praise_of_spirituality', array('deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now())));	
			return $this->db->affected_rows();
		}
	}

	//删除评论
	public function del_comments_by_pirituality_id($spirituality_id,$user_id)
	{
		if(!empty($spirituality_id) && !empty($user_id) ){
			$this->db->where('spirituality_id', $spirituality_id);
			$this->db->update('comments_of_spirituality', array('deleted_by'=>$user_id,'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now())));		
			return $this->db->affected_rows();
		}
	}	

	//删除回复
	public function del_replies_by_spirituality_id($spirituality_id,$user_id)
	{
		if(!empty($spirituality_id) && !empty($user_id)){
			$this->db->select('comments_of_spirituality.id as comments_id ');
			$this->db->from('comments_of_spirituality');
			$this->db->where('spirituality_id', $spirituality_id);
			// $this->db->where('comments_of_spirituality.deleted_at is null');

		 	$comments_ids = $this->db->get()->result();
		 	if(!empty($comments_ids)){
		 		foreach ($comments_ids as $key => $value) {
		 			$comments_id = $value->comments_id;

					$data = array(
					               'deleted_by' => $user_id,
					               'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
					            );
					$this->db->where('comments_id', $comments_id);
					$this->db->update('replies_of_spirituality', $data); 	
		 		}	
		 	}
		 	return true;	
		}
	}

	//删除提示
	public function del_prompt_by_spirituality_id($spirituality_id,$spirituality_user_id='',$user_id=''	)
	{		
		if(!empty($spirituality_id)){
			$temp_data1 = array();
			$temp_data2 = array();
			$temp_data3 = array();
			$temp_array = array();

			//删除赞提示
			$this->db->select('alert_message.id as alert_message_id');	
			$this->db->from('alert_message');
			$this->db->join('praise_of_spirituality', 'praise_of_spirituality.id = alert_message.table_id', 'left');
			$this->db->where('praise_of_spirituality.spirituality_id',$spirituality_id );
			$this->db->where('table_name', 'praise_of_spirituality');	
			// $this->db->where('praise_of_spirituality.deleted_at is null');			
			$this->db->where('alert_message.deleted_at is null');
			$temp_data1 = $this->db->get()->result();
			// var_dump($temp_data1);exit;
			//删除评论提示
			$this->db->select('alert_message.id as alert_message_id');	
			$this->db->from('alert_message');
			$this->db->join('comments_of_spirituality', 'comments_of_spirituality.id = alert_message.table_id', 'left');
			$this->db->where('comments_of_spirituality.spirituality_id', $spirituality_id);
			$this->db->where('alert_message.deleted_at is null');
			// $this->db->where('comments_of_spirituality.deleted_at is null');
			$temp_data2 = $this->db->get()->result();
			// var_dump($temp_data2);exit;	
			
			//删除回复提示
			if($spirituality_user_id != $user_id){
				$this->db->select('alert_message.id as alert_message_id');
				$this->db->from('alert_message');
				$this->db->join('replies_of_spirituality', 'replies_of_spirituality.id = alert_message.table_id', 'left');
				$this->db->join('comments_of_spirituality', 'comments_of_spirituality.id = replies_of_spirituality.comments_id', 'left');
				$this->db->where('comments_of_spirituality.spirituality_id', $spirituality_id);
				$this->db->where('alert_message.table_name', 'replies_of_spirituality');			
				$this->db->where('alert_message.deleted_at is null');
				// $this->db->where('replies_of_spirituality.deleted_at is null');
				// $this->db->where('comments_of_spirituality.deleted_at is null');
				$temp_data3 = $this->db->get()->result();
			}		

			$temp_array  = array('temp_data1' => $temp_data1, 'temp_data2'=> $temp_data2,'temp_data3' =>$temp_data3);
			$temp_affected_array = array();

			if(!empty($temp_array)){
				foreach ($temp_array as $key => $data_arr) {
					if(!empty($data_arr)){
						foreach ($data_arr as $key => $message_ids) {
							$id = $message_ids->alert_message_id;							
							$this->db->delete('alert_message', array('id'=>$id)); 
							$temp_affected_array = $this->db->affected_rows();
						}								
					}
				}
			}				

			return $temp_affected_array;
		}

	}	

	public function send_comments($comments_contents,$spirituality_id,$user_id)
	{
		if (!empty($comments_contents) && !empty($spirituality_id) && !empty($user_id) ) {

			$this->db->set('spirituality_id', $spirituality_id);
			$this->db->set('contents', $comments_contents);
			$this->db->set('commenter', $user_id);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			$this->db->insert('comments_of_spirituality');
			return $this->db->insert_id();					
		}else {
			return false;
		}
	}

	public function alert_about_comments_of_spirituality($spirituality_id,$insert_id)
	{
		if (!empty($spirituality_id) && !empty($insert_id) ) {
			$spirituality_user = $this->get_spirituality_by_spirituality_id($spirituality_id);			
			if (!empty($spirituality_user)) {

				$user_id = $spirituality_user->user_id;							

				$this->db->set('table_name', 'comments_of_spirituality');
				$this->db->set('table_id', $insert_id);
				$this->db->set('user_id', $user_id);
				$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
				$this->db->insert('alert_message');
				return $this->db->insert_id();	
			}

		}else {
			return false;
		}
	}

	public function get_spirituality_by_spirituality_id($spirituality_id)
	{
		if (!empty($spirituality_id)) {
			$this->db->select('user_id');
			$this->db->from('spirituality');
			$this->db->where('id', $spirituality_id);
			$this->db->where('spirituality.deleted_at is null');
			return  $this->db->get()->first_row();

		}
	}

	public function send_reply($reply_content,$comments_id,$user_id)
	{
		if (!empty($reply_content) && !empty($comments_id) && !empty($user_id)) {

			$this->db->set('comments_id', $comments_id);
			$this->db->set('contents', $reply_content);
			$this->db->set('replier', $user_id);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			$this->db->insert('replies_of_spirituality');
			return $this->db->insert_id();					
		}else{
			return false;
		}
	}

	public function alert_about_replies_of_spirituality($comments_id,$insert_id)
	{
		if (!empty($comments_id) && !empty($insert_id)) {
			$this->db->select('commenter');
			$this->db->from('comments_of_spirituality');
			$this->db->where('id', $comments_id);
			$this->db->where('deleted_at is null');
			$query = $this->db->get();
			if($query->num_rows() > 0){
				$commenter = $query->first_row()->commenter;  
			}
			
			$this->db->set('table_id', $insert_id);
			$this->db->set('table_name', 'replies_of_spirituality');
			$this->db->set('user_id', $commenter);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			$this->db->insert('alert_message');
			return $this->db->insert_id();	


		}
	}

	public function get_group_user_ranking($group_id,$this_week_start_date,$this_week_end_date)
	{
		if (!empty($group_id)) {
			$data_return = array();

			$users_results = $this->find_user_by_group_id($group_id);
			if(!empty($users_results)) {
				foreach ($users_results as $key => $value) {
					$group_user_id = $value->id;
					$this_week_count = $this->get_count_in_time_of_user_spirituality($group_user_id,$this_week_start_date,$this_week_end_date);
					$group_user_info = $this->get_user_all_info($group_user_id);					
					
					$joinDate = date('Y-m-d',strtotime($group_user_info['user_created_at']));		

					$temp_rank = $this->spirituality_group_ranking($group_user_id,$this_week_start_date,$this_week_end_date);
					$temp_rank = !empty($temp_rank) ? $temp_rank : "0";

					$should_completed_counts = $this->_get_user_attence($joinDate,$this_week_start_date,$this_week_end_date);
					$progress = '0.00%';
					if(is_numeric($should_completed_counts) && is_numeric($this_week_count)){
						$progress = $this->get_percentage($this_week_count,$should_completed_counts);
					}
					$data_return[] = 
					array(
						'group_user_id' => $group_user_id, 
						'group_user_nick' => $group_user_info['nick'], 
						'this_week_count' => $this_week_count, 
						'should_completed_counts' => $should_completed_counts, 
						'progress' => $progress, 
						'group_user_rank' => $temp_rank, 
						);						
				}
				return $data_return;
				exit;	
			}
		}else {
			return false;
		}
	}

	public function get_count_in_time_of_user_spirituality($group_user_id,$start_date,$end_date)
	{
		if (!empty($group_user_id) && !empty($start_date) && !empty($end_date)) {
			
			$this->db->select('count(*) as count');
			$this->db->from('spirituality');
			$this->db->where('user_id', $group_user_id);			
			$this->db->where('created_at <', $end_date." 23:59:59");
			$this->db->where('created_at >', $start_date." 00:00:00");
			$this->db->where('deleted_at is null');
			$count_result = $this->db->get()->first_row();
			$spirituality_group_week_count = !empty($count_result->count) ? $count_result->count : "";
			return $spirituality_group_week_count;			

		}
	}

	public function del_comments_by_comment_id($user_id,$comment_id)
	{
		if(!empty($user_id)&& !empty($comment_id)){
			$params  = array(
				'deleted_by' =>$user_id,
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
			);
			$this->db->where('id', $comment_id);
			$this->db->update('comments_of_spirituality', $params);
			return  $this->db->affected_rows();
		}else{
			return false;
		}
	}	

	public function del_alert_comments_of_spirituality($comment_id)
	{
		if(!empty($comment_id)){
			$this->db->delete('alert_message', array('table_name' =>'comments_of_spirituality','table_id'=>$comment_id)); 
			return $this->db->affected_rows();
		}
	}	

	public function check_spirituality($starttime,$endtime,$users_id)
	{
		if(!empty($starttime) && !empty($endtime) && !empty($users_id)){
			$data_return = array();
			$this->db->select('spirituality.id,directory,user_id,book_id,chapter_id,gold_sentence,heart_feeling,response,created_at');
			$this->db->from('spirituality');
			$this->db->join('bibile_book', 'bibile_book.id = spirituality.book_id', 'left');
			$this->db->where('user_id', $users_id);
			$this->db->where('created_at >', $starttime.' 00:00:00');
			$this->db->where('created_at <', $endtime.' 24:59:59');
			$this->db->where('spirituality.deleted_at is null');
			$spirituality_results = $this->db->get()->result_array();
			// var_dump($spirituality_results);exit;
			if(!empty($spirituality_results)) {
				foreach ($spirituality_results as $k => $row_cer) {
					$created_at = $row_cer['created_at'];
					$row_cer['conversion_time'] = $this->tranTime(strtotime($created_at));	

					$user_id = $row_cer['user_id'];
					$user_info = $this->get_user_all_info($user_id);
					$row_cer['user_userHead_src'] = $user_info['userHead_src']; 
					$row_cer['group_user_nick']    = $user_info['nick']; 
					
					$spirituality_id = $row_cer['id'];							
					$this->load->model('user/user_model');		
					$result_array = $this->user_model->get_comments_and_replaies_by_spirituality_id($spirituality_id);
					$row_cer['results_comments_and_replaies'] = $result_array; 
					
					$data_return[] = $row_cer; 
				}
			}				
			return 	 $data_return;

			
		}else{
			return false;			
		}
	}

	public function get_user_prayer_time_span($starttime,$endtime,$user_id)
	{
		if(!empty($starttime) && !empty($endtime) && !empty($user_id)){
			$data_return = array();
			$tmp_data1   = array();
			$tmp_data2   = array();
			$data_temp   = array();

			$this->db->select('prayer_for_group.id,group_prayer_contents,created_at');
			$this->db->from('prayer_for_group');
			$this->db->where('prayer_for_group.user_id', $user_id);
			$this->db->where('created_at >', $starttime.' 00:00:00');
			$this->db->where('created_at <', $endtime.' 24:59:59');
			$this->db->where('prayer_for_group.deleted_at is null');
			$tmp_data1 = $this->db->get()->result_array();

			$this->db->select('prayer_for_urgent.id,content_prayer,created_at');
			$this->db->from('prayer_for_urgent');
			$this->db->where('prayer_for_urgent.user_id', $user_id);
			$this->db->where('created_at >', $starttime.' 00:00:00');
			$this->db->where('created_at <', $endtime.' 24:59:59');
			$this->db->where('prayer_for_urgent.deleted_at is null');
			$tmp_data2 = $this->db->get()->result_array();

			$data_temp =  array_merge($tmp_data1,$tmp_data2);
			if(!empty($data_temp)){
				foreach ($data_temp as $k => $row_cer) {
					$created_at = $row_cer['created_at'];
					$user_info = $this->get_user_all_info($user_id);
					$row_cer['prayer_userHead_src'] =  $user_info['userHead_src'];
					$row_cer['prayer_nick'] =  $user_info['nick'];
					$row_cer['conversion_time'] = $this->tranTime(strtotime($created_at));	
					$data_return[] = $row_cer;	
				}
			}

			return $data_return;
		}else{
			return false;
		}
	}


	public function del_urgent_prayer($prayer_id,$deleted_by,$is_admin)
	{		
		if(!empty($prayer_id) && !empty($deleted_by) ){
			
			$params  = array(
				'deleted_at' =>  mdate('%Y-%m-%d %H:%i:%s', now()) , 
				'deleted_by' =>  $deleted_by, 
				'is_admin'   =>  $is_admin, 				
				);
			$this->db->where('id', $prayer_id);
			$this->db->update('church.prayer_for_urgent', $params);
			return  $this->db->affected_rows();

		}else{
			return false;
		}
	}

	public function del_group_prayer($prayer_id,$deleted_by,$is_admin)
	{
		if(!empty($prayer_id) && !empty($deleted_by)){

			$params  = array(
				'deleted_at' =>  mdate('%Y-%m-%d %H:%i:%s', now()) , 
				'deleted_by' =>  $deleted_by, 
				'is_admin'   =>  $is_admin, 								
				);

			$this->db->where('id', $prayer_id);
			$this->db->update('church.prayer_for_group', $params);
			return  $this->db->affected_rows();
		}else{
			return false;
		}
	}

}
	
	
	
	
	
	
	
	
	
