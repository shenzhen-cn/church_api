<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
	}
	
	// public function findReUserName()
	// {
	// 	$data_return  = array();

	// 	$sql = "SELECT * FROM re_user WHERE is_re  = ? "; 

	// 	$query =  $this->db->query($sql,array('0'));

	// 	foreach($query->result_array() as $row_cer){
	// 		$data_return=$row_cer;

	// 	}

	// 	return $data_return;

	// }

	public function findGroupName()
	{
		$data_return  = array();

		$this->db->select('id,group_name');
		$this->db->from('church.group');
		$this->db->where('deleted_at is null');
		$data_return = $this->db->get()->result();

		return $data_return;
	}

	
	public function register($re_user_id,$user_name, $password, $nick,$created_by_admin_id,$created_by='0')
	{	

		$sql = "SELECT id FROM user WHERE re_user_id  = ? ";
		$isset_id =  $this->db->query($sql,array($re_user_id))->result();
// var_dump($isset_id);exit();
		if (!empty($re_user_id) && !empty($password) && !empty($nick) && empty($isset_id)  ) {

			$this->db->set('re_user_id', $re_user_id);
			$this->db->set('email', $user_name);
			$this->db->set('password',  $password);
			$this->db->set('nick',  $nick);
			$this->db->set('created_by',  $created_by);
			$this->db->set('created_by_admin_id',  $created_by_admin_id);


			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));

			$this->db->insert('user');
			
			// echo $this->db->last_query();exit;
			
			return $this->db->insert_id();

		}else {

			return $isset_id;
		}
		
	}

/**
    update 2015/12/13
*/
	public function improveInformation($user_id,$sex,$user_nick)
	{	
		$data_return  	= array();
		$params 		= array();
		
		if (!empty($user_id) ) {

			if (!empty($user_nick)) {
				$params['nick']  = $user_nick;
			}

			$params['sex'] 		= $sex;

			$this->db->where('id', $user_id);
			$this->db->update('user', $params);
			$data_return['affected_id'] =  $this->db->affected_rows();
			
			// $this->db->set('user_id', $user_id);
			// $this->db->set('userHead_src',  $userHeadSrc);
			// $this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			// $this->db->set('update_at', mdate('%Y-%m-%d %H:%i:%s', now()));

			// $this->db->insert('userhead_src');
			// $data_return['userHead_src_id'] = $this->db->insert_id();


			return $data_return;

		}else {

			return false;
		}
	}

	public function update_user_group($user_id,$group_id)
	{
		if (! empty($user_id) && ! empty($group_id)) {
			
			$params = array('group_id' =>$group_id);
			$this->db->where('id', $user_id);
			$this->db->update('user', $params);
			return $this->db->affected_rows();

		}else{
			return false;
		}
	}	

	public function update_group_leader($user_id)
	{
		if (!empty($user_id)) {
			$this->db->select('id,group_leader_id');
			$this->db->from('group');
			$this->db->where('group_leader_id', $user_id);
			$is_exist =  $this->db->get()->first_row();
			if (!empty($is_exist)) {

				$params = array('group_leader_id' => 0);

				$this->db->where('id', $is_exist->id);
				$this->db->update('group', $params);
				return $this->db->affected_rows();
			}
		}
	}	

// 	public function checkLogin( $user_name,$password )
// 	{
// 		$data_return  = array();

// 		$this->db->select('user.id ,user.nick  ,user.group_id,userHead_src.userHead_src');
// 		$this->db->from('church.user');

// 		$this->db->join('church.re_user', 're_user.id = user.re_user_id','left');
// 		$this->db->join('church.userHead_src', 'userHead_src.user_id = user.id','left');

// 		$this->db->where('user.password', $password);
// 		$this->db->where('re_user.user_name', $user_name);
// 		$this->db->where('user.deleted_at is NULL');

// // echo $this->db->last_query();exit;
// 		foreach ($this->db->get()->result() as $row_cer) {
// 			$data_return = $row_cer;
			
// 		}

// 		return $data_return;
		
// 	}

	public function find_by_email($user_name_email)
	{
		return $this->db->get_where('user', array('email' => $user_name_email, 'deleted_at' => NULL))->row();
	}

	public function find_token_by_client_id_and_account_id($account_id)
	{
		if ($token = $this->db->get_where('user_access_token', array('user_account_id' => $account_id, 'deleted_at is null'))->row())
		{
			return $token;
		}
		$this->load->helper('date');
		$this->db->insert('user_access_token', array(
			'user_account_id' => $account_id,
			'access_token' => $this->generate_token(),
			'created_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
			'updated_at' => mdate('%Y-%m-%d %H:%i:%s', now())
		));
		
		return $this->db->get_where('user_access_token', array( 'user_account_id' => $account_id))->row();
	}
	
	/**
	 * Generate access token
	 */				
	public function generate_token()
	{
		return md5(base64_encode(pack('N6', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), uniqid())));												
	}

	public function check_password($user_name_email,$password)
	{

		return $this->db->get_where('user', array('email'=> $user_name_email, 'password' =>md5(md5($password))))->row();
	}

	public function check_user_email($re_user_email)
	{
	
		$this->db->select('*');
		$this->db->from('re_user');
		$this->db->where('user_name',$re_user_email);
		$this->db->where('status','1');

		return $this->db->get()->first_row();
// echo $this->db->last_query();

	}

	public function check_user_is_activated($re_user_email)
	{
		$this->db->select('id,token_exptime,created_url_at,deleted_url_at');
		$this->db->from('re_user');
		$this->db->where('user_name',$re_user_email);
		$this->db->where('status','0');
		$this->db->where('registered_at is null');
		

		return	$this->db->get()->first_row();
// $this->db->last_query();

	
	}

	public function del_re_user_url($is_activated_id)
	{
		$this->db->where('id', $is_activated_id);
		$this->db->delete('re_user');
		return $this->db->affected_rows();
	}

	public function addPersonal($re_user_email,$admin_id,$regtime,$token_exptime,$token,$is_activated_id='')
	{	

		if (! empty($is_activated_id)) {

			$this->db->where('id', $is_activated_id);
			$this->db->delete('re_user');
			$del_id = $this->db->affected_rows();
		}

			$this->db->set('user_name', $re_user_email);

			$this->db->set('token', $token);
			$this->db->set('token_exptime', $token_exptime);

			$this->db->set('created_by_admin_id', $admin_id);
			$this->db->set('created_url_at', $regtime);
			$this->db->insert('re_user');
			// echo $this->db->last_query();exit;
			return $this->db->insert_id();			
	}

	public function find_id_by_token($token)
	{
		if ( !empty($token)) {

			$this->db->select('id,user_name,token_exptime,created_by_admin_id,deleted_url_at');
			// $this->db->select('*');
			$this->db->from('re_user');
			$this->db->where('token',$token);
			// $this->db->where('registered_at is null');
			// $this->db->where('status','0');
			// $this->db->where('deleted_url_at is null');

			return $this->db->get()->row();
		}else{
			return false;
		}

	}

	public function update_re_user($re_user_id)
	{
		// var_dump($re_user_id);exit();
		if (!empty($re_user_id)) {

			$params = array(
				'registered_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
				'deleted_url_at' => mdate('%Y-%m-%d %H:%i:%s', now()),
				'status' =>'1'
			 );

			$this->db->where('id', $re_user_id);
			$this->db->update('re_user', $params);
			return  $this->db->affected_rows();

		} else {
			return false;
		}
	}

	public function del_register($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->delete('user');
		return $this->db->affected_rows();
	}

	public function find_user_email_is_exist($user_email)
	{
		if ( !empty($user_email)) {

			$this->db->select('id');
			$this->db->from('re_user');
			$this->db->where('status', '1');
			$this->db->where('user_name', $user_email);
			$this->db->where('registered_at is not null');
			return  $this->db->get()->first_row();

		}else{
			return false;
		}
	}

	public function is_deleted_user($user_email)
	{
		if(!empty($user_email)){
			$this->db->select('deleted_at');
			$this->db->from('user');
			$this->db->where('email', $user_email);
			$temp = $this->db->get()->first_row();
			$is_deleted = null;

			if(!empty($temp)){
				$is_deleted = $temp->deleted_at;
			}

			return $is_deleted ;

		}else{
			return false;
		}
	}

	public function insert_forget_pwd_token($re_user_id,$token,$now_token_exptime,$isadmin='')
	{	
		$regtime  		=  	date("Y-m-d H:i:s",time());
		if(empty($isadmin)){
			$isadmin='N';
		}	
		
		$this->db->select('*');
		$this->db->from('forget_pwd_token');
		$this->db->where('re_user_id', $re_user_id);
		$this->db->where('status', '0');
		$this->db->where('is_admin', $isadmin);
		$this->db->where('deleted_at is null');
		$isset = $this->db->get()->first_row();

		if(!empty($isset)){
			$token_exptime = $isset->token_exptime;
			$forget_pwd_token_id = $isset->id;
			if($token_exptime < $regtime){
				$params  = array(
					'token' => $token,
					'token_exptime' => $now_token_exptime,
					'is_admin' => $isadmin,
					);
				$this->db->where('id', $forget_pwd_token_id);
				$this->db->update('forget_pwd_token', $params);	
				return $forget_pwd_token_id;
			}else{
				return false;
			}

		}else{
			$this->db->set('is_admin',$isadmin);
			$this->db->set('re_user_id', $re_user_id);
			$this->db->set('token',  $token);
			$this->db->set('token_exptime',  $now_token_exptime);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));

			$this->db->insert('forget_pwd_token');
			return $this->db->insert_id();
		}		
	}

	public function find_forget_pwd_token_by_token($token)
	{
		if ( !empty($token)) {

			$this->db->select('id,re_user_id,token,token_exptime,deleted_at,is_admin');
			// $this->db->select('*');
			$this->db->from('forget_pwd_token');
			$this->db->where('token',$token);

			return $this->db->get()->row();
		}else{
			return false;
		}

	}

	public function find_user_name($re_user_id)
	{
		if (!empty($re_user_id)) {
			$this->db->select('email,nick');
			$this->db->from('user');
			$this->db->where('re_user_id',$re_user_id);
			return	$this->db->get()->first_row();
		}else{
			return false;
		}
	}

	public function resetpwd_for_forgetpwd($re_user_id,$user_name,$password)
	{
		$params  = array(
			'password' => $password,
			'password_reset_sent_at' => mdate('%Y-%m-%d %H:%i:%s', now())

			);
		$this->db->where('re_user_id', $re_user_id);
		$this->db->where('email', $user_name);
		$this->db->update('user', $params);
		return  $this->db->affected_rows();
	}

	public function update_forget_pwd_token($re_user_id)
	{
		if ( !empty($re_user_id) ) {
			$params  = array(
				'status' => '1', 
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) 
				);
			$this->db->where('re_user_id', $re_user_id);
			$this->db->update('forget_pwd_token', $params);

			return  $this->db->affected_rows();

		}else{
			return false;
		}
	}

	// public function user_registered($limit, $offset)
	// {
	// 	$this->db->select('id,user_name,status,created_url_at,token_exptime');
	// 	$this->db->from('re_user');
	// 	$this->db->order_by('id','desc');
	// 	$this->db->limit($limit, $offset);
	// 	return $this->db->get()->result();
	// }
	
	public function user_registered($limit, $offset)
	{
		$this->db->select('re_user.id AS re_user_id,user.id as group_user_id,user.nick AS user_nick,sex,group_name,user_name,status,created_url_at,token_exptime,user.deleted_at as user_deleted_at ,admin.nick as admin_nick,user.deleted_by_id');
		$this->db->from('re_user');
		$this->db->join('user', 'user.re_user_id = re_user.id', 'left');
		$this->db->join('admin', 'admin.id = re_user.created_by_admin_id', 'left');
		$this->db->join('group', 'group.id = user.group_id', 'left');
		$this->db->order_by('re_user.id','desc');
		$this->db->limit($limit, $offset);
		return $this->db->get()->result();		
	}

	public function count_user_registeres()
	{
		$this->db->select('count(*) as count');
		$this->db->from('re_user');
		$query = $this->db->get()->first_row();
		if(!empty($query)){
			$count =  $query->count;
			return $count;
		}else{
			return null;
		}
	}

	public function send_spirituality($gold_sentence,$heart_feeling,$response,$chapter_id,$book_id,$user_id)
	{	
        $regday  =  date("Y-m-d",time());    
        $is_bool = false;
        // var_dump($regday);exit();

		if (!empty($user_id)  && !empty($heart_feeling) && !empty($response) && !empty($chapter_id) && !empty($book_id)) {
			$this->db->select('id,status,created_at');
			$this->db->from('spirituality');
			$this->db->where('user_id' ,$user_id);
			$this->db->where('status' ,'1');
			$this->db->where('deleted_at is null');
			$this->db->order_by('created_at','DESC');
			$is_set = $this->db->get()->first_row();

			// var_dump($is_set);exit(); 
			if (!empty($is_set)) {
		        $is_bool = $regday >  $is_set->created_at;
			}
			// var_dump($is_bool);exit();
			if (empty($is_set)  || $is_bool) {

				$this->db->set('user_id', $user_id);
				$this->db->set('book_id', $book_id);
				$this->db->set('chapter_id',  $chapter_id);
				$this->db->set('gold_sentence',  $gold_sentence);
				$this->db->set('heart_feeling',  $heart_feeling);
				$this->db->set('response',  $response);
				$this->db->set('status',  '1');
				$this->db->set('response',  $response);


				$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));

				$this->db->insert('spirituality');
								
				return $this->db->insert_id();

			}else{

				return true;
			}

		}else{

			return false;
		}
	}

	// public function find_user_spirituality($group_id, $user_info,$chapter_id="",$book_id="")
	// {	
	// 	$regday   =  date("Y-m-d",time());
	// 	$nextday  =  date("Y-m-d",time()+24*3600);

	// 	// var_dump($user_info);exit();
	// 	$data_return = array();
	// 	$results_return = array();	
	// 	$count = 0;
	// 	$today_created_at = null;
			
	// 	if (!empty($group_id) && !empty($user_info) ) {
	// 		// var_dump($user_info);exit();
	// 		foreach ($user_info as $k => $row_cer) {
	// 			$user_id = $row_cer['user_id'];
	// 			// var_dump($user_info);exit();
	// 			$this->db->select('spirituality.id as spirituality_id ,gold_sentence,heart_feeling,response,spirituality.created_at');
	// 			$this->db->from('spirituality');
	// 			$this->db->join('user', 'user.id = spirituality.user_id', 'left');
	// 			$this->db->where('user_id ', $user_id);

	// 			if (!empty($chapter_id) && !empty($book_id)) {
	// 				$this->db->where('spirituality.book_id', $book_id);
	// 				$this->db->where('spirituality.chapter_id', $chapter_id);
	// 			}

	// 			$this->db->where('spirituality.created_at < ', $nextday);
	// 			$this->db->where('spirituality.created_at > ', $regday);
	// 			$this->db->where('user.group_id',$group_id);
	// 			$this->db->where('spirituality.deleted_at is null');
	// 			$this->db->where('user.deleted_at is null');
	// 			$this->db->order_by('spirituality.created_at','DESC');
	// 			$user_s =  $this->db->get();	
	// 			// echo $this->db->last_query();exit();
	// 			// var_dump($user_s->result_array());exit();
	// 			foreach ($user_s->result_array() as $row_cer1) {
					
	// 				$row_cer['spirituality_id'] = isset($row_cer1['spirituality_id']) ? $row_cer1['spirituality_id'] : "" ; 	
	// 				$row_cer['gold_sentence']   = isset($row_cer1['gold_sentence']) ? $row_cer1['gold_sentence'] : "";  	
	// 				$row_cer['heart_feeling']   = isset($row_cer1['heart_feeling']) ? $row_cer1['heart_feeling'] : "" ; 	
	// 				$row_cer['response']        = isset($row_cer1['response']) ? $row_cer1['response'] : "" ; 	
	// 				$row_cer['created_at']      = isset($row_cer1['created_at']) ? $row_cer1['created_at'] : "" ; 	
	// 				$count++ ; 
	// 				$today_created_at           = isset($row_cer1['created_at']) ? $row_cer1['created_at'] : "" ;  					
	// 			}

	// 			$data_return[] = $row_cer; 

	// 		}

	// 		$results_return['count'] = $count; 
	// 		$results_return['data_return'] = $data_return;
	// 		$results_return['today_created_at'] = $today_created_at;

	// 		// var_dump($results_return);exit();
	// 		return 	$results_return;
	// 	}else{
	// 		return false;
	// 	}
		
	// }

	public function find_user_spirituality($group_id,$chapter_id="",$book_id="",$using_user_id)
	{	
		

        $this->db->select('spirituality.id as spirituality_id,user_id,book_id,chapter_id,gold_sentence,heart_feeling,response,spirituality.created_at,group_id');
        $this->db->from('spirituality');
        $this->db->join('user', 'user.id = spirituality.user_id', 'left');
        $this->db->where('user.group_id', $group_id);
        $this->db->where('chapter_id',$chapter_id);
        $this->db->where('book_id',$book_id);
        $this->db->where('spirituality.deleted_at is null');
        $this->db->where('user.deleted_at is null');
        $this->db->order_by('spirituality.created_at', 'desc');
        $query = $this->db->get();
        $arr1 = array();
        if ($query->num_rows() > 0 ) {
        	$arr1 = $query->result();
        }
        // var_dump($arr1);exit;
        $arr2 = array();
        $arr3 = array();
        $data_return = array();      

        foreach ($arr1 as $key => $value) {

        	$user_id = $value->user_id;
        	$spirituality_id = $value->spirituality_id;
        	$book_id = $value->book_id;
        	$chapter_id = $value->chapter_id;
        	$gold_sentence = $value->gold_sentence;
        	$heart_feeling = $value->heart_feeling;
        	$response = $value->response;
        	$created_at = $value->created_at;
        	$created_at = $this->tranTime(strtotime($created_at));
        	$praise_result = $this->find_user_count_and_praiser_praise_of_spirituality($spirituality_id);

        	$praise_count = $praise_result['praise_count'];//$using_user_id
        	$arr3 = $praise_result['praise_result'];//$using_user_id
        	$is_praised = 'N';

        	foreach ($arr3 as $k => $v) {
        		$praiser = $v->praiser;
	        	$status  = $v->status;
        		if ($praiser == $using_user_id ) {
        			$is_praised = $status;
        			break;
        		}
        	}
        	// exit;
        	$arr2 = $this ->get_user_all_info($user_id);
        	$data_return[]  = 
        	array('user_id' => $user_id ,
				'nick' => $arr2['nick'] ,
				'userHead_src' => $arr2['userHead_src'] ,
				'spirituality_id' => $spirituality_id ,
				'book_id' => $book_id ,
				'chapter_id' => $chapter_id,
				'gold_sentence' => $gold_sentence,
				'heart_feeling' => $heart_feeling,
				'response' => $response,
				'created_at' => $created_at,
				'praise_count' => $praise_count,
				'is_praised' => $is_praised,
			 );
        }
        return $data_return;

		
	}

	public function find_user_count_and_praiser_praise_of_spirituality ($spirituality_id)
	{
		if (!empty($spirituality_id)) {
			$praise_result  = array();
			$this->db->select('praiser,status');
			$this->db->from('praise_of_spirituality');
			$this->db->where('status', 'Y');
			$this->db->where('spirituality_id', $spirituality_id);
			$this->db->where('praise_of_spirituality.deleted_at is null');
			$this->db->order_by('praise_of_spirituality.id', 'desc');
			$query = $this->db->get();
			// echo $this->db->last_query();exit;
			if ($query ->num_rows() > 0) {
				$praise_result = $query->result();

			}
			// var_dump($praise_result);exit;
			return array('praise_result' =>$praise_result , 
						 'praise_count'  =>count($praise_result),
				 );;		
		}
	}

	public function find_user_is_spirituality($user_id)
	{
		$regday   =  date("Y-m-d",time());
		$nextday  =  date("Y-m-d",time()+24*3600);

		if (!empty($user_id)) {
			$this->db->select('status');
			$this->db->from('spirituality');
			$this->db->where('created_at < ', $nextday);
			$this->db->where('created_at > ', $regday);
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			$temp =  $this->db->get()->first_row();
			if ( !empty($temp)) {
				return $temp->status ;
			}

		}else{
			return false;	
		}
	}

	public function checkCurrentPwd($currentPwd,$user_id)
	{
		$this->db->select('id');
		$this->db->from('user');
		$this->db->where('id', $user_id);
		$this->db->where('password', $currentPwd);
		$this->db->where('deleted_at is null');
		return $this->db->get()->result();
	}

	public function resetpassword($user_id,$confirmNewPwd)
	{	
		
		$params['password']    = $confirmNewPwd;

		$this->db->where('id', $user_id);
		$this->db->where('deleted_at is null');
		$this->db->update('user', $params);
		return $this->db->affected_rows();
	}

	public function reminder_spirituality_by_id($user_id,$regtime)
	{
		if (!empty($user_id)) {

			$this->db->select('max(created_at) as created_at');
			$this->db->from('spirituality');
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			$return_date =  $this->db->get()->first_row();
			$last_set_spirituality_date  = isset($return_date->created_at) && !empty($return_date->created_at) ? $return_date->created_at : "" ;
			if (!empty($last_set_spirituality_date)) {		

				$day =  $this->diffBetweenTwoDays(date("Y-m-d",strtotime($last_set_spirituality_date)) ,date("Y-m-d",strtotime($regtime)));
				return $day;
			}else {
				return $last_set_spirituality_date;
			}	

		}else {
			return false;
		}
	}

	
	public function get_user_head_src_and_nick($user_id)
	{
		$data_return  = array();

		$this->db->select('max(update_at) as update_at');
		$this->db->from('church.userhead_src');
		$this->db->where('user_id' ,$user_id);
		$this->db->where('deleted_at is null');
		$update_at = $this->db->get()->first_row();
		$last_update_at	 = isset($update_at->update_at) ? $update_at->update_at : "" ;
	
		$this->db->select('user.id as user_id,userhead_src,nick');
		$this->db->from('church.userhead_src');
		$this->db->join('user', 'user.id = userhead_src.user_id', 'left');
		$this->db->where('user_id', $user_id);
		$this->db->where('userhead_src.deleted_at is null');
		$this->db->where('userhead_src.update_at' ,$last_update_at);
		$this->db->where('user.deleted_at is null');
	    return	$this->db->get()->first_row();

	}

	public function get_all_events_for_json($user_id)
	{
		if (!empty($user_id)) {
			$this->db->select('user.created_at as created_at');
			$this->db->from('user');
			$this->db->where('id', $user_id);
			$this->db->where('deleted_at is null');
			$data1 = 	$this->db->get()->first_row();
			$user_create_at  = isset($data1->created_at) ? $data1->created_at : "";

			$this->db->select('spirituality.id,created_at,chapter_id,directory');
			$this->db->from('spirituality');
			$this->db->join('bibile_book', 'bibile_book.id = spirituality.book_id', 'left');
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			$spirituality = $this->db->get()->result_array();
			
			$this->db->select('id,created_at');
			$this->db->from('prayer_for_group');
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			$prayer_for_group = $this->db->get()->result_array();

			$this->db->select('id,created_at');
			$this->db->from('prayer_for_urgent');
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			$prayer_for_urgent = $this->db->get()->result_array();
			
			return  array('user_create_at' =>$user_create_at , 
							'spirituality' =>$spirituality ,
							'prayer_for_group' =>$prayer_for_group ,
							'prayer_for_urgent' =>$prayer_for_urgent ,
				);


		}else {
			return false;
		}
	}

	public function add_praise($user_id,$spirituality_id)
	{
		if (!empty($user_id) && !empty($spirituality_id)) {
			$this->db->select('id,spirituality_id,praiser,status');
			$this->db->from('praise_of_spirituality');
			$this->db->where('praiser',$user_id);
			$this->db->where('spirituality_id',$spirituality_id);
			$this->db->where('deleted_at is null');
			$query =  $this->db->get();

			// var_dump($query->num_rows() > 0);exit;
			if ($query->num_rows() <= 0) {				
				$this->db->set('spirituality_id', $spirituality_id);
				$this->db->set('praiser', $user_id);
				$this->db->set('status', 'Y');
				$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
				$this->db->insert('praise_of_spirituality');		
				return $this->db->insert_id();
			}else{
				$return_data1  = $query->first_row();

			}
			// var_dump($return_data1);exit;
			$id = $return_data1->id;
			$status = $return_data1->status;		
			// var_dump($status);exit;
				
			return $status;


		}else {
			return false;
		}
	}

	public function alert_user_for_praise($user_id,$spirituality_id,$insert_id)
	{
		if (!empty($user_id) && !empty($spirituality_id) && !empty($insert_id)) {
			$s_user_id = null;
			$this->db->select('user_id');
			$this->db->from('spirituality');
			$this->db->where('id', $spirituality_id);
			$this->db->where('spirituality.deleted_at is null');
			$query = $this->db->get();
			if ($query -> num_rows() > 0  ) {
				$s_user_id = $query->first_row()->user_id;
			}

			if (!empty($s_user_id)) {				
				$this->db->set('user_id',  $s_user_id);
				$this->db->set('table_id',  $insert_id);
				$this->db->set('table_name',  'praise_of_spirituality');
				$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
				$this->db->insert('alert_message');
			}

			return $this->db->insert_id();
		}
	}

	public function get_personal_data_for_spirituality($user_id,$praised_user_id = '',$limit='', $offset='')
	{	
		if (!empty($user_id)) {
			$data_return = array();
			$praised_user_id = empty($praised_user_id) ? $user_id : $praised_user_id;
			
			$data_results = $this->get_spirituality_by_user_id($user_id,$limit, $offset);

			foreach ($data_results as $row_cer) {
				$spirituality_id = $row_cer->spirituality_id;
				$tran_created_at = $this->tranTime(strtotime($row_cer->created_at));
				$praise_result = $this->find_user_count_and_praiser_praise_of_spirituality($spirituality_id);					
				
				$info_praises = array();
				$is_praised   = 'N';
				foreach ($praise_result['praise_result'] as $k => $v) {
					$praiser = $v->praiser;
					$praiser_info = $this->get_user_all_info($praiser);
					$praiser_user_id = $praiser_info['user_id'];
					if ($praiser_user_id == $praised_user_id) {
						$is_praised = 'Y';
					}					 
					$nick = $praiser_info['nick'];
					$info_praises[] = array('praiser_user_id' =>$praiser_user_id ,'nick' =>$nick );
				}
				$count_praises = $praise_result['praise_count'];

				//灵修评论
				$comments_of_spirituality_result = array();
				$comments_of_spirituality_result = $this->get_comments_and_replaies_by_spirituality_id($spirituality_id);					


				$data_return[]  = array(

					'spirituality_id' => $spirituality_id ,
					'spirituality_user_id' => $row_cer->user_id ,
					'book_id' => $row_cer->book_id ,
					'directory' => $row_cer->directory ,
					'chapter_id' => $row_cer->chapter_id ,
					'gold_sentence' => $row_cer->gold_sentence ,
					'heart_feeling' => $row_cer->heart_feeling ,
					'response' => $row_cer->response ,
					'created_at' => $tran_created_at,
					'count_praises' => $count_praises,
					'is_praised' => $is_praised,
					'info_praises' => $info_praises,
					'comments_of_spirituality_result' => $comments_of_spirituality_result,
				 );

			}
			
			return $data_return;
		}
	}

	//灵修评论
	public function get_comments_and_replaies_by_spirituality_id($spirituality_id)
	{
		if(!empty($spirituality_id)){
			$comments_of_spirituality_result = array();
			$temp_data2 = $this->get_comments_by_spirituality_id($spirituality_id);
			if (!empty($temp_data2)) {
				foreach ($temp_data2 as $key => $value) {

					$comments_id = $value['id'];
					$contents = $value['contents'];
					$commenter = $value['commenter'];
					$commenter_info = $this->get_user_all_info($commenter);
					$created_at = $value['created_at'];
					$comments_tran_created_at = $this->tranTime(strtotime($created_at));

					//回复灵修评论
					$this->db->select('id, comments_id, contents, replier, created_at');
					$this->db->from('replies_of_spirituality');
					$this->db->where('comments_id', $comments_id);
					$this->db->where('replies_of_spirituality.deleted_at is null');
					$reply_results = $this->db->get()->result();

					$replies_of_spirituality_result = array();
					if (!empty($reply_results)){							
						foreach ($reply_results as $k2 => $v2) {
							$reply_id = $v2->id;
							$reply_comments_id = $v2->comments_id;
							$reply_contents = $v2->contents;
							$replier = $v2->replier;
							$replier_info = $this->get_user_all_info($replier);
							$created_at = $v2->created_at;

							$reply_created_at = $this->tranTime(strtotime($created_at));
							$replies_of_spirituality_result[] = 
							array(							
								'reply_id' => $reply_id,
								'reply_comments_id' => $reply_comments_id,
								'reply_nick' => $replier_info['nick'],
								'replier_id' => $replier,
								'reply_contents' => $reply_contents,
								'reply_created_at' => $reply_created_at,								
							 );
						}
						
					}

					$comments_of_spirituality_result[] = 
					array(
						'comments_id' => $comments_id, 
						'contents' => $contents, 
						'commenter_id' => $commenter, 
						'commenter_nick' => $commenter_info['nick'], 
						'commenter_userHead_src' => $commenter_info['userHead_src'], 
						'comments_tran_created_at' => $comments_tran_created_at, 
						'replies_of_spirituality_result' => $replies_of_spirituality_result,
						);	

				}
			}

			return $comments_of_spirituality_result;	

		}else{
			return false;
		}
	}
		
	public function get_spirituality_by_user_id($user_id,$limit='', $offset='')
	{
		if (!empty($user_id)) {
			$this->db->select('spirituality.id as spirituality_id,user_id ,book_id,directory,chapter_id,gold_sentence,heart_feeling,response,spirituality.created_at');
			$this->db->from('spirituality');
			$this->db->join('bibile_book', 'bibile_book.id = spirituality.book_id', 'left');
			$this->db->where('user_id', $user_id);
			$this->db->where('spirituality.deleted_at is null');
			$this->db->limit($limit, $offset);
			$this->db->order_by('spirituality.id', 'desc');
			return  $this->db->get()->result();		
		}
	}

	public function get_comments_by_spirituality_id($spirituality_id)
	{
		if (!empty($spirituality_id)) {
			$temp_data1  = array();

			$this->db->select('comments_of_spirituality.id,contents,spirituality_id,commenter,comments_of_spirituality.created_at,directory,book_id,chapter_id');					
			$this->db->from('comments_of_spirituality');
			$this->db->join('spirituality', 'spirituality.id = comments_of_spirituality.spirituality_id', 'left');
			$this->db->join('bibile_book', 'bibile_book.id = spirituality.book_id', 'left');
			$this->db->where('spirituality.id', $spirituality_id);
			$this->db->where('comments_of_spirituality.deleted_at is null');
			$this->db->where('spirituality.deleted_at is null');
			$this->db->order_by('comments_of_spirituality.id', 'desc');
			$query = $this->db->get();

			if ($query->num_rows() > 0) {
				$temp_data1 = $query->result_array();
			}
			return $temp_data1;
		}
	}

	public function get_informations($user_id,$spirituality_id)
	{
		if(!empty($user_id) && !empty($spirituality_id)){

			$spirituality_result = $this->get_spirituality_by_spirituality_id($spirituality_id);

			if(!empty($spirituality_result)){
				$this->load->model('group/group_model');
				$spirituality_user_id = $spirituality_result['spirituality_user_id'];
				$this->group_model->del_prompt_by_spirituality_id($spirituality_id,$spirituality_user_id,$user_id);
			}

			$comments_and_replaies_result = $this->get_comments_and_replaies_by_spirituality_id($spirituality_id);
			// var_dump($comments_and_replaies_result);exit;
			$praise_result = $this->find_user_count_and_praiser_praise_of_spirituality($spirituality_id);

			$info_praises = array();
			$is_praised   = 'N';
			foreach ($praise_result['praise_result'] as $k => $v) {
				$praiser = $v->praiser;
				$praiser_info = $this->get_user_all_info($praiser);
				$praiser_user_id = $praiser_info['user_id'];
				if ($praiser_user_id == $user_id) {
					$is_praised = 'Y';
				}					 
				$nick = $praiser_info['nick'];
				$info_praises[] = array('praiser_user_id' =>$praiser_user_id ,'nick' =>$nick );
			}
			$count_praises = $praise_result['praise_count'];

			return  array('spirituality_result' => $spirituality_result,
						  'count_praises' =>$count_praises,	
						  'info_praises' =>$info_praises,
						  'is_praised' =>$is_praised,
						  'comments_and_replaies_result'=>$comments_and_replaies_result
						 );

		}else{
			return false;
		}
	}

	public function get_spirituality_by_spirituality_id($spirituality_id)
	{
		if(!empty($spirituality_id)){			
			$this->db->select('spirituality.id ,user_id,directory,book_id,chapter_id,gold_sentence,heart_feeling,response,created_at');
			$this->db->from('spirituality');		
			$this->db->join('bibile_book', 'bibile_book.id = spirituality.book_id', 'left');
			$this->db->where('spirituality.id', $spirituality_id);
			$this->db->where('spirituality.deleted_at is null');
			$rsults =  $this->db->get()->first_row();
			$data_return = array();

			if(!empty($rsults)){

				$user_id = $rsults->user_id;						
				$spirituality_user_info = $this->get_user_all_info($user_id);
				$spirituality_user_nick = $spirituality_user_info['nick'];
				$spirituality_userHead_src = $spirituality_user_info['userHead_src'];
				$created_at = $rsults->created_at;																
				$tran_created_at = $this->tranTime(strtotime($created_at));
				$data_return['tran_created_at'] = $tran_created_at;
				$data_return['spirituality_userHead_src'] = $spirituality_userHead_src;
				$data_return['spirituality_user_nick'] = $spirituality_user_nick;
				$data_return['spirituality_user_id'] = $user_id;						
				$data_return['spirituality_id'] = $rsults->id;	
				$data_return['directory'] = $rsults->directory;	
				$data_return['book_id'] = $rsults->book_id;	
				$data_return['chapter_id'] = $rsults->chapter_id;	
				$data_return['gold_sentence'] = $rsults->gold_sentence;	
				$data_return['heart_feeling'] = $rsults->heart_feeling;	
				$data_return['response'] = $rsults->response;	
			}

			return $data_return;
		}
	}


	public function del_users_by_id($user_id,$admin_id)
	{
		if(!empty($user_id) && !empty($admin_id)){

			$affected_id = $this->del_user_by_user_id($user_id,$admin_id);

			if($affected_id >= 1){
				$this->del_userhead_src_by_user_id($user_id);
				$this->del_user_album_by_user_id($user_id,$admin_id);

				$this->del_user_access_token_by_user_id($user_id);	

				$this->del_spirituality_by_user_id($user_id,$admin_id);

				$this->del_prayer_for_urgent_by_user_id($user_id,$admin_id);

				$this->del_prayer_for_group_by_user_id($user_id,$admin_id);	
				
				$this->load->model('messages/user_messages_model');
				$this->user_messages_model->del_alert_message_by_user_id($user_id);


				return true;	

			}else {
				return false;
			}
		}else{
			return false;
		}
	}

	// 通过id删除user表
	function del_user_by_user_id($user_id,$admin_id)
	{
		if(!empty($user_id) && !empty($admin_id)){

			$params['deleted_by'] 		= 'A';
			$params['deleted_at'] 		= mdate('%Y-%m-%d %H:%i:%s', now());
			$params['deleted_by_id'] 	= $admin_id;
			$this->db->where('id', $user_id);
			$this->db->update('user', $params);
			return  $this->db->affected_rows();	
		}else{
			return false;
		}
	}

	//通过user_id删除头像userhead_src表	
	public function del_userhead_src_by_user_id($user_id)
	{
		if(!empty($user_id)){
			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now())
			            );

			$this->db->where('user_id', $user_id);
			$this->db->update('userhead_src', $data); 

			return $this->db->affected_rows();
		}
	}

	//通过user_id 删除 相册 user_album
	public function del_user_album_by_user_id($user_id, $admin_id)
	{		
		if(!empty($user_id) && !empty($admin_id)){
			$this->db->select('id');
			$this->db->from('user_album');
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			$album_ids = $this->db->get()->result();

			if(!empty($album_ids)){
				foreach ($album_ids as $key => $value) {
						$album_id = $value->id;

						$this->del_user_album_src($album_id,$admin_id);
					}	
			}
			

			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),
			               'deleted_by' => $admin_id,
			               'deleted_by_admin' => 'Y',
			            );

			$this->db->where('user_id', $user_id);
			$this->db->update('user_album', $data); 	
		}
	}

	//通过user_id 删除 照片 user_album_src表
	public function del_user_album_src($album_id,$admin_id)
	{
		if(!empty($album_id) && !empty($admin_id)){

			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),
			               'deleted_by' => $admin_id,
			               'deleted_by_admin' => 'Y',
			            );
			$this->db->where('album_id', $album_id);
			$this->db->update('user_album_src', $data);
			return $this->db->affected_rows();
		}
	}

	//通过user_id 删除token
	public function del_user_access_token_by_user_id($user_id)
	{
		if(!empty($album_id)){
			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),			               
			            );
			$this->db->where('user_account_id', $user_id);
			$this->db->update('user_access_token', $data);
			return $this->db->affected_rows();
		}
	}

	//删除 用户灵修 spirituality
	public function del_spirituality_by_user_id($user_id,$admin_id)
	{
		if(!empty($user_id) && !empty($admin_id)){
			$this->db->select('id');	
			$this->db->from('spirituality');
			$this->db->where('user_id', $user_id);
			$this->db->where('deleted_at is null');
			$spirituality_ids =  $this->db->get()->result();

			if(!empty($spirituality_ids)){
				foreach ($spirituality_ids as $k => $v) {
						$spirituality_id = $v->id;

						$this->del_comments_of_spirituality_by_spirituality_id($spirituality_id,$user_id,$admin_id);
					}	
			}


			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),			               
			               'deleted_by' => $admin_id,
			               'admin' => 'Y',
			            );
			$this->db->where('user_id', $user_id);
			$this->db->update('spirituality', $data);
			return $this->db->affected_rows();	
		}
	}

	//删除赞 通过 $spirituality_id 
	public function del_praise_of_spirituality_by_spirituality_id($spirituality_id)
	{
		if(!empty($spirituality_id)){			

			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),			               			               
			            );
			$this->db->where('spirituality_id', $spirituality_id);
			$this->db->update('praise_of_spirituality', $data);
			return $this->db->affected_rows();		
		} 	
	} 

	//删除评论 通过 $spirituality_id
	function del_comments_of_spirituality_by_spirituality_id($spirituality_id,$user_id,$admin_id)
	{
		if(!empty($spirituality_id) && !empty($admin_id)){
			$this->db->select('id');	
			$this->db->from('comments_of_spirituality');
			$this->db->where('spirituality_id', $spirituality_id);
			$this->db->where('commenter',$user_id);
			$this->db->where('deleted_at is null');
			$comments_ids =  $this->db->get()->result();
			if(!empty($comments_ids)){
					foreach ($comments_ids as $k => $v) {
						$comments_id = $v->id;
						$this->del_replies_of_spirituality_by_comments_id($comments_id);
						
					}
			}

			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),			               
			               'deleted_by' => $admin_id,
			               'is_admin' => 'Y',
			            );
			$this->db->where('spirituality_id', $spirituality_id);
			$this->db->where('commenter', $user_id);
			$this->db->update('comments_of_spirituality', $data);
			return $this->db->affected_rows();		
		}
	}

	//删除回复
	public function del_replies_of_spirituality_by_comments_id($comments_id,$admin_id)
	{
		if(!empty($comments_id) && !empty($admin_id) ){

			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),			               
			               'deleted_by' => $admin_id,
			               'is_admin' => 'Y',
			            );

			$this->db->where('comments_id', $comments_id);
			$this->db->update('replies_of_spirituality', $data);
			return $this->db->affected_rows();	
		}		
	}

	//删除紧急代祷by_user_id
	public function del_prayer_for_urgent_by_user_id($user_id,$admin_id)
	{
		if(!empty($user_id) && !empty($admin_id)){

			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),			               
			               'deleted_by' => $admin_id,
			               'is_admin' => 'Y',
			            );

			$this->db->where('user_id', $user_id);
			$this->db->update('prayer_for_urgent', $data);
			return $this->db->affected_rows();		
		}
	}

	//删除小组代祷 by_user_id
	public function del_prayer_for_group_by_user_id($user_id,$admin_id)
	{
		if(!empty($user_id) && !empty($admin_id)){

			$data = array(
			               'deleted_at' =>mdate('%Y-%m-%d %H:%i:%s', now()),			               
			               'deleted_by' => $admin_id,
			               'is_admin' => 'Y',
			            );

			$this->db->where('user_id', $user_id);
			$this->db->update('prayer_for_group', $data);
			return $this->db->affected_rows();		
		}
	}

	public function upload_headSrc($user_id,$userHeadSrc)
	{
		if(!empty($user_id) && !empty($userHeadSrc)){

			$this->db->set('user_id', $user_id);
			$this->db->set('userHead_src',  $userHeadSrc);
			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));
			$this->db->set('update_at', mdate('%Y-%m-%d %H:%i:%s', now()));

			$this->db->insert('userhead_src');
			return $this->db->insert_id();

		}else{
			return false;
		}
	}

	public function modify_user_data($user_nick,$sex,$user_id)
	{
		if (!empty($user_nick) && !empty($sex) && !empty($user_id)) {

			$params = array(
					'nick' =>$user_nick,
					'sex' =>$sex,
					'updated_at' =>mdate('%Y-%m-%d %H:%i:%s', now())					
				);

			$this->db->where('id', $user_id);
			$this->db->update('user', $params);
			return $this->db->affected_rows();

		}else{
			return false;
		}
	}

	/**
		update 12-17
	*/		
	public function get_honor_list()
	{	
		$return_array = array();
		//灵修
		$get_count_spirit_results =$this->get_count_spirit();	
		$user_count_results  = $this->get_user_count($get_count_spirit_results);
		$user_info_count_spirit_results =$this->get_user_info_count_spirit($user_count_results);		

		//祷告
		$user_count_prayer = $this->get_count_prayer();
		$user_count_prayer_results  = $this->get_user_count($user_count_prayer);

		$user_info_count_prayer_results =$this->get_user_info_count_prayer($user_count_prayer_results);		

		$return_array = array('user_info_count_spirit_results' =>$user_info_count_spirit_results ,'user_info_count_prayer_results' => $user_info_count_prayer_results );
		return $return_array;

	}

	//灵修
	public function get_count_spirit()
	{
		$this->db->select('user_id');	
		$this->db->from('spirituality');
		$this->db->where('deleted_at is null');
		return  $this->db->get()->result_array();		
	}

	//祷告
	public function get_count_prayer()
	{	
		$results = array();	

		$this->db->select('user_id');	
		$this->db->from('prayer_for_group');
		$this->db->where('deleted_at is null');
		$results1 = $this->db->get()->result_array();

		$this->db->select('user_id');	
		$this->db->from('prayer_for_urgent');
		$this->db->where('deleted_at is null');
		$results2 = $this->db->get()->result_array();

		if(!empty($results1) && !empty($results1)){
			$results = array_merge($results1,$results2);
			return $results;
		}else if(empty($results1) && !empty($results2) ){
			return $results2;
		}else if(!empty($results1) && empty($results2)){
			return $results1;			
		}else {
			return 	$results;						
		}

	}

	public function get_user_count($results='')
	{
		$arr_user_id_count = array();
		$arr_temp = array();

		if (!empty($results)) {

			foreach ($results as $k => $v) {
				$arr_temp[] = $v['user_id'];
			}

			$arr_user_id_count = array_count_values($arr_temp);			
		}
			return $arr_user_id_count;
	}

	public function get_user_info_count_spirit($results='')
	{				
		$return_array  = array();
		arsort($results);
		foreach ($results as $key => $value) {
			$user_info = $this->get_user_all_info($key);
        	$user_created_at = date('Y-m-d',strtotime($user_info['user_created_at']));
			$regtime  		 = date("Y-m-d H:i:s",time());
			$already_reg_day =  $this->diffBetweenTwoDays($user_created_at ,date("Y-m-d",strtotime($regtime)));
			$already_reg_day = $already_reg_day+1; 

			$spirit_rate  = $this->get_percentage($value,$already_reg_day);
			$return_array[] = array(
					'user_id' => $user_info['user_id'], 
					'user_sex' => $user_info['sex'], 
					'user_nick' => $user_info['nick'], 
					'user_userHead_src' => $user_info['userHead_src'], 
					'user_group_name' => $user_info['group_name'], 
					'user_created_at' => $user_created_at, 
					'user_count_spirit' => $value, 
					'already_reg_day' => $already_reg_day,
					'spirit_rate' => $spirit_rate,					 
				);  			
		}

		$return_array = array_slice($return_array,0,10);
		return $return_array;
	}

	public function get_count_prayers($user_group_id ='')
	{
		$count_group_prayer = null;
		$count_urgent_prayer = null;		

		$this->db->select('count(*) as count');	
		$this->db->from('group_prayer');
		$this->db->where('group_id', $user_group_id);		
		$this->db->where('deleted_at is null');
		$temp_data1 =  $this->db->get()->first_row();

		if (!empty($temp_data1)) {
			$count_group_prayer = $temp_data1->count;
		}

		$this->db->select('count(*) as count');	
		$this->db->from('urgent_prayer');
		$this->db->where('deleted_at is null');
		$temp_data2 =  $this->db->get()->first_row();

		if (!empty($temp_data2)) {
			$count_urgent_prayer = $temp_data2->count;
		}		

		$count_prayers = $count_group_prayer +  $count_urgent_prayer;
		return $count_prayers;

	}
	public function get_user_info_count_prayer($results='')
	{				
		$return_array  = array();
		arsort($results);
		foreach ($results as $key => $value) {

			$user_info = $this->get_user_all_info($key);
			$user_group_id  = $user_info['group_id'];

        	$user_created_at = date('Y-m-d',strtotime($user_info['user_created_at']));
			$regtime  		 = date("Y-m-d H:i:s",time());
			$already_reg_day =  $this->diffBetweenTwoDays($user_created_at ,date("Y-m-d",strtotime($regtime)));
			$already_reg_day = $already_reg_day+1; 
			
			$count_prayers = $this->get_count_prayers($user_group_id);


			$prayer_rate  = $this->get_percentage($value,$count_prayers);
			$return_array[] = array(
					'user_id' => $user_info['user_id'], 
					'user_sex' => $user_info['sex'], 
					'user_nick' => $user_info['nick'], 
					'user_userHead_src' => $user_info['userHead_src'], 
					'user_group_name' => $user_info['group_name'], 
					'user_created_at' => $user_created_at, 
					'user_count_prayer' => $value, 
					'already_reg_day' => $already_reg_day,
					'prayer_rate' => $prayer_rate,					 
				);  			
		}

		$return_array = array_slice($return_array,0,10);
		return $return_array;
	}	

}
	
	
	
	
	
	
	
	
	
