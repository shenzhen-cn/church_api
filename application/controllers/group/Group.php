<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Group extends MY_Controller {

	const DEFAULT_LIMIT = 10;
    const MAX_LIMIT = 20; 

	public function __construct()
	{
		parent::__construct();
		$this->load->model('group/group_model');

	}

	public function addGroup()
	{
		
		$groupName = $this->input->post('addGroupName');
//		var_dump($groupName);exit;
		$admin_id  = $this->input->post('admin_id');

	    $group_id = $this->group_model->addGroup($groupName,$admin_id);	  

	    if ($group_id) {
	         $this->response(array('status_code'=> 200 ,'groupName'=>$groupName));
	    }else{

	         $this->response(array('status_code'=> 400 ,'groupName'=>$groupName));
	    }
	      
	}

	public function get_group()
	{
		$results = $this->group_model->get_group();
		$this->response(array('results' => $results));	

	}

	public function find_user_by_group_id()
	{
        $group_id = $this->input->get('group_id');

        $results = $this->group_model->find_user_by_group_id($group_id);	
		if (!$results || empty($results)) {

		    $this->response(array('status_code'=> 400 ));
			return;
		}

	    $this->response(array('status_code'=> 200 ,'results'=>$results));
        
	}	

	public function del_group()
	{
        $group_id = $this->input->get('group_id'); 
        $results = $this->group_model->del_group($group_id);
        if (! $results || empty($results)) {

        	$this->response(array('status_code'=> 400));
			return;
        }

	    $this->response(array('status_code'=> 200));
        
		
	}

	public function groupEdit()
	{
		$group_id = $this->input->post('group_id');
		$group_name = $this->input->post('group_name');
		$group_leader_id = $this->input->post('group_leader_id');
		$group_id         =   $group_id ? $group_id : "";
		$group_name       =   $group_name ? $group_name : "";
		$group_leader_id  =   $group_leader_id ? $group_leader_id : "";
		$results          = $this->group_model->groupEdit($group_id,$group_name,$group_leader_id);

		if ( !$results ) {

        	$this->response(array('status_code'=> 400));
			return;
		}
		// var_dump($results);exit();

		$this->response(array('status_code'=> 200));
	}

	public function find_group_by_group_id()
	{
        $group_id =  $this->input->get('group_id');

		$results = $this->group_model->find_group_by_group_id($group_id);
		// var_dump($results);exit();
		if (!$results || empty($results)) {

			$this->response(array('status_code'=> 400 ));
			return;
		}

	    $this->response(array('status_code'=> 200 ,'results' => $results));
        
	}

	public function find_all_users_by_group_id()
	{
        $group_id =  $this->input->get('group_id');
		$results  = $this->group_model->find_all_users_by_group_id($group_id);

		if (!$results || empty($results)) {
			$this->response(array('status_code'=> 400 ));
			return;
		}
		
	    $this->response(array('status_code'=> 200 ,'results' => $results['data_array'] ,'group_name' => $results['group_name']));

	}

	public function spirituality()
	{
        $testament = $this->input->post('testament');	
        $book_id = $this->input->post('book_id') ;      
        $chapter_id =  $this->input->post('chapter_id');

		$results  = $this->group_model->spirituality($testament,$book_id,$chapter_id);
		// var_dump($results);exit;
		if (!$results || empty($results)) {
			$this->response(array('status_code'=> 400 ));
			return;
		}
		
	    $this->response(array('status_code'=> 200 ,'results' => $results));

		
	}

	public function setting_spirituality()
	{
		$testament =  $this->input->post('testament');
		$book_id =  $this->input->post('book_id');
		$chapter_id =  $this->input->post('chapter_id');
		$group_id =  $this->input->post('group_id');

		$results  = $this->group_model->setting_spirituality($testament,$book_id,$chapter_id,$group_id);
		// var_dump($results);exit();
		if (!$results || empty($results)) {
			$this->response(array('status_code'=> 400 ));
			return;
		}
		
	    $this->response(array('status_code'=> 200 ,'results' => $results));
	}

	public function check_spirituality_or_prayer()
	{
		$starttime = $this->post('starttime');
		$endtime = $this->post('endtime');
		$users_id = $this->post('users_id');
		$check_class = $this->post('check_class');

		$spirituality_results = array();
		$prayer_results = array();

		if($check_class == 'spirituality') {
			$spirituality_results = $this->group_model->check_spirituality($starttime,$endtime,$users_id);		

	        if (!$spirituality_results) {

	         $this->response( array('status_code' =>400 ));
	         return;
	        }
	        
		}else if($check_class ==  'prayer'){
			$prayer_results = $this->group_model->get_user_prayer_time_span($starttime,$endtime,$users_id);		

			if (!$prayer_results ) {

			 $this->response( array('status_code' =>400 ));
			 return;
			}
		}	      

        $this->response(array('status_code'=> 200,'spirituality_results' => $spirituality_results ,'prayer_results' => $prayer_results ));	

	}

	public function del_user_spirituality()
	{
		$s_id    = $this->input->post('s_id');
		$s_u_id    = $this->input->post('s_u_id');
		$user_id = $this->input->post('user_id');
			// var_dump($s_id);exit();
		$results =  $this->group_model->del_user_spirituality($s_id,$s_u_id,$user_id);

		if (!$results) {
			$this->response( array('status_code' =>400 ));
			return;
		}

        $this->response(array('status_code'=> 200,'results' => $results));	
	}

	public function check_nextday()
	{
		$group_id = $this->input->get('group_id');
		$user_info = $this->group_model->find_all_users_by_group_id($group_id);
		$day = $this->input->get('day');

		$day = $day ? $day : "";

		$today = date("Y-m-d",$day); 
		$lastday  =  date("Y-m-d",$day-24*3600);
		$date= null;
		$results =  $this->group_model->check_nextday($group_id,$today,$lastday,$user_info['data_array']);
		// var_dump($results);exit();

		if ($results['count'] <= 0) {

			$this->response( array('status_code' =>401 ));
			return;
		}

		if (! empty($results['today_created_at'])) {
			
			$today_created_at = $results['today_created_at'];
			$time = strtotime($today_created_at);
			$date = date('m/d Y',$time);
		}

		if (empty($results)) {

		 $this->response( array('status_code' =>400 ));
		 return;
		}

		$this->response(array('status_code'=> 200,'results' => $results['data_return'],'count_is_spirituality' => $results['count'],'date' =>$date ));

	}

	public function find_week_s_report()
	{	
		$group_id = $this->input->get('group_id');
		$group_id =  $group_id ? $group_id : "" ;

		$regtime   =  date("Y-m-d",time());
		$this_week_monday = $this->this_monday(0,false);
		$this_week_sunday = $this->this_sunday(0,false);		

		$results = $this->group_model->get_group_user_ranking($group_id,$this_week_monday,$this_week_sunday);

		if (!$results) {
			$this->response( array('status_code' =>400 ));
			return;
		}
        $this->response(array('status_code'=> 200,'results' => $results ,'week_firstday' => $this_week_monday ,'week_lastday' => $this_week_sunday));	
	}

	public function get_week_days($day){ 

	    if (!empty($day)) {

	     	$week_lastday  = date('Y-m-d',strtotime("$day Sunday")); 
	     	$week_firstday = date('Y-m-d',strtotime("$week_lastday -6 days")); 

	     	return array('week_firstday' => $week_firstday,'week_lastday' => $week_lastday);
	     } 
	} 
	
	public function setting_group_prayer()
	{
        $group_prayer_content = $this->input->post('group_prayer_content'); 
        $group_id = $this->input->post('group_id'); 
        // var_dump($group_id);exit;
        $results = $this->group_model->setting_group_prayer($group_prayer_content,$group_id);
        // var_dump($results);exit();
        if (!$results) {

            $this->response( array('status_code' =>400 ));
            return;
        }

        $this->response(array('status_code'=> 200,'results' => $results));
			
	}	

	public function get_today_group_prayer()
	{
		$group_id = $this->input->get('group_id');
		$group_id =  $group_id ? $group_id : "";

		$results = $this->group_model->get_today_group_prayer($group_id);
//		var_dump($results);exit;
		if (!$results) {

		    $this->response( array('status_code' =>400 ));
		    return;
		}

		$this->response(array('status_code'=> 200,'results' => $results));
	}
	
	public function see_member()
	{
		$group_user_id  = $this->get('group_user_id');
		$user_id        = $this->get('user_id');
		$count 			= $this->get('count');	
		$count          =  $count ? $count : 5;
		$limit = $this->get('limit');	
		$limit = $limit ? $limit : self::DEFAULT_LIMIT;
		if($limit > self::MAX_LIMIT) $limit = self::DEFAULT_LIMIT;
		$page = $this->get('page');
		$page = $page ? $page : 1;
		if($page == 0) $page = 1;

		$total = $this->group_model->count_spirituality_by_user_id($group_user_id);

		// if($total <= 0 || !$total ){
		//     $this->response(array('status_code'=>'400'));
		//     return;
		// }

		$this->load->helper('util_helper');
		$pagination = get_pagination($total, $limit, $page);    

		// $this_week_monday = $this->this_monday(0,false);
		// $this_week_sunday = $this->this_sunday(0,false);


		$results = $this->group_model->see_member($group_user_id,$user_id,$pagination['limit'], $pagination['offset'],$count,$page);

		if (!$results) {
		    $this->response( array('status_code' =>400 ));
		    return;
		}

		$this->response(array('status_code'=> 200,'total' => $total,'results' => $results));
	}

	public function get_notice_groups_results()
	{
		$user_id = $this->input->get('user_id');
		$results = $this->group_model->get_notice_groups_results($user_id);
		if (!$results) {

		    $this->response( array('status_code' =>400 ));
		    return;
		}

		$this->response(array('status_code'=> 200,'results' => $results));
	}

	public function get_rate_of_spirituality()
	{
		$start_date_of_lastweek = $this->last_monday(0,false);
		$end_date_of_lastweek   = $this->last_sunday(0,false);
		
		$start_date_of_lastmonth = $this->lastmonth_firstday(0,false);
		$end_date_of_lastmonth   = $this->lastmonth_lastday(0,false);

		$last_month_results = $this->group_model->get_rate_of_spirituality($start_date_of_lastmonth,$end_date_of_lastmonth);

		$last_week_results = $this->group_model->get_rate_of_spirituality($start_date_of_lastweek,$end_date_of_lastweek);

		$this->response(array('status_code'=> 200,'last_month_results' => $last_month_results,'last_week_results' => $last_week_results));
	}

	public function delete_spirituality()
	{
		$del_user_id = $this->get('del_user_id');
		$spirituality_id = $this->get('spirituality_id');
		// var_dump($spirituality_id);exit;
		$results = $this->group_model->delete_spirituality($del_user_id,$spirituality_id);

		if (is_numeric($results)) {
			$this->group_model->delete_alert_about_praise($spirituality_id,$del_user_id);
		}
		if (!$results) {
		    $this->response( array('status_code' =>400 ));
		    return;
		}
		$this->response(array('status_code'=> 200,'results' => $results));

	}

	public function send_comments()
	{
		$comments_contents = $this->post('comments_contents');
		$spirituality_id = $this->post('spirituality_id');
		$user_id = $this->post('user_id');
		$insert_id = $this->group_model->send_comments($comments_contents,$spirituality_id,$user_id);
		// var_dump($insert_id);exit;
		// $insert_id = 1;
		// var_dump($insert_id);exit;
		if (is_numeric($insert_id)) {
			$temp  = $this->group_model->alert_about_comments_of_spirituality($spirituality_id,$insert_id);
		}
		// var_dump($temp);exit;
		if (!$insert_id) {
		    $this->response( array('status_code' =>400 ));
		    return;
		}

		$this->response(array('status_code'=> 200,'results' => $insert_id));
	}

	public function send_reply()
	{
		$user_id = $this->post('user_id');
		$comments_id = $this->post('comments_id');
		$reply_content = $this->post('reply_content');

		$insert_id = $this->group_model->send_reply($reply_content,$comments_id,$user_id);
		if (is_numeric($insert_id)) {
			$this->group_model->alert_about_replies_of_spirituality($comments_id,$insert_id);
		}
		if (!$insert_id) {
		    $this->response( array('status_code' =>400 ));
		    return;
		}

		$this->response(array('status_code'=> 200,'results' => $insert_id));
	}

	public function del_comments_by_comment_id()
	{
		$user_id = $this->post('user_id');
		$comment_id = $this->post('comment_id');
		$affected_id =  $this->group_model->del_comments_by_comment_id($user_id,$comment_id);

		if(is_numeric($affected_id)){
			$results = $this->group_model->del_alert_comments_of_spirituality($comment_id);			
		}else{

			$this->response( array('status_code' =>400 ));
			return;
		}
		
		$this->response(array('status_code'=> 200,'results' => $affected_id));
	}

	public function get_users_by_group_id()
	{
		$group_id = $this->get('group_id');

		$results = $this->group_model->find_user_by_group_id($group_id);
		if(!$results){
			$this->response( array('status_code' =>400 ));
			return;
		}
		
		$this->response(array('status_code'=> 200,'results' => $results));		
	}


	public function del_prayer()
	{
		$prayer_id = $this->post('prayer_id');
		$user_id = $this->post('user_id');
		$admin_id = $this->post('admin_id');

		$user_id = !empty($user_id) ? $user_id : "";	
		$admin_id = !empty($admin_id) ? $admin_id : "";	
		$contentStyle = $this->post('contentStyle');	

		$deleted_by = null; $is_admin = 'N';

		if(!empty($user_id)){
			$deleted_by= $user_id;
		} else if(!empty($admin_id)){
			$deleted_by= $admin_id;
			$is_admin = 'Y';
		}

		if($contentStyle == 'urgent_prayer'){
			$results = $this->group_model->del_urgent_prayer($prayer_id,$deleted_by,$is_admin);
		}else if($contentStyle == 'group_prayer'){
			$results = $this->group_model->del_group_prayer($prayer_id,$deleted_by,$is_admin);
		}

		if(!$results){
			$this->response( array('status_code' =>400 ));
			return;
		}
		
		$this->response(array('status_code'=> 200,'results' => $results));	
	}

	//update 1/14 
	public function frozen_users_by_id()
	{
		$user_id   = $this->post("user_id");
		$admin_id  = $this->post("admin_id");

		$results = $this->group_model->frozen_users_by_id($user_id,$admin_id);

		if(!$results){
			$this->response(array('status_code' =>400 ));
			return;
		}
		
		$this->response(array('status_code'=> 200,'results' => $results));	

	}

}
