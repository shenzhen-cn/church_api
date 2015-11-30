<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Register extends MY_Controller {

	const DEFAULT_LIMIT = 10;
	const MAX_LIMIT = 20;


	public function __construct()
	{
		parent::__construct();
		$this->load->model('user/user_model');
		$this->load->helper('util');

	}

	public function findReUserName()
	{
		$op = $this->input->get('op') ? $this->input->get('op') : "" ;
		$id = $this->input->get('id') ? $this->input->get('id') : "" ;
		$token = $this->input->get('token') ? $this->input->get('token') : "";
//		var_dump($token);exit;

		if ($op == 'active' ) {
			
			$find_re_user_by_token = $this->user_model->find_id_by_token($token);
			if ($find_re_user_by_token) {

				$re_user_id 			= $find_re_user_by_token->id;
				$re_user_name 			= $find_re_user_by_token->user_name;
				$re_user_token_exptime  = $find_re_user_by_token->token_exptime;
				$deleted_url_at  		= $find_re_user_by_token->deleted_url_at;
				// var_dump($re_user_token_exptime);exit();
				$regtime  =  date("Y-m-d H:i:s",time());

				if ($id != md5($re_user_id)) {

					$this->response(array('status_code' => 401,'op' =>$op));			
					return;
				} else if ($re_user_token_exptime < $regtime ) {

					$this->response(array('status_code' => 402,'op' =>$op));			
					return;
				} else if (! empty($deleted_url_at)) {

					$this->response(array('status_code' => 404,'op' =>$op));			
					return;	

				}else {

					$this->response(array('status_code' => 200,'find_re_user_by_token' => $find_re_user_by_token,'op' =>$op));			
					return;
				}

			}else{

				$this->response(array('status_code' => 400,'op' =>$op));			
				return;
			}

			
		}else if ($op == 'resetpwd'){

			$find_forget_pwd_token_by_token  = $this->user_model->find_forget_pwd_token_by_token($token);
			// var_dump($find_forget_pwd_token_by_token);exit();

			if ($find_forget_pwd_token_by_token) {

				$forget_pwd_token_id		= $find_forget_pwd_token_by_token->id;
				$re_user_id 				= $find_forget_pwd_token_by_token->re_user_id;
				$token_exptime 				= $find_forget_pwd_token_by_token->token_exptime;
				$deleted_at 				= $find_forget_pwd_token_by_token->deleted_at;

				$find_user_name 			= $this->user_model->find_user_name($re_user_id);
				// $affect_id   				= $this->user_model->update_forget_pwd_token($forget_pwd_token_id);	
				// var_dump($affect_id);exit();
				$regtime  =  date("Y-m-d H:i:s",time());

				if ($id != md5($forget_pwd_token_id)) {

					$this->response(array('status_code' => 401,'op' =>$op));			
					return;

				}else if ($token_exptime < $regtime) {
					
					$this->response(array('status_code' => 402,'op' =>$op));			
					return;

				} else if (!$find_user_name->email) {

					$this->response(array('status_code' => 405,'op' =>$op));			
					return;
				} 
				else if (! empty($deleted_at) ) {

					$this->response(array('status_code' => 404,'op' =>$op));			
					return;	

				}else{

					$this->response(array('status_code' => 200,'find_re_user_by_token' => $find_forget_pwd_token_by_token,'find_user_name' =>$find_user_name,'op' =>$op));			
					return;
				}			

			}else {

				$this->response(array('status_code' => 400,'op' =>$op));			
				return;
			}			

			
		}
		
		$this->response(array('status_code' => 403,'op' =>$op));

	}


	public function addPersonal()
	{
		$re_user_email = $this->input->post('re_user_email');
//		var_dump($re_user_email);exit();
		$admin_id = $this->input->post('admin_id');
		$active = 'active';
		$regtime  =  date("Y-m-d H:i:s",time());
		$token_exptime = date("Y-m-d H:i:s",time()+24*3600);
		
		$token = md5($re_user_email.$regtime);	

		$is_registered  = $this->user_model->check_user_email($re_user_email);

		if ( !  empty($is_registered) ) {

			$this->response(array('status_code' => 400 ,'message' => '此账号已注册过！'));			
			return;
		}

		$is_activated  = $this->user_model->check_user_is_activated($re_user_email); 	

		if ( !empty($is_activated)) {

			$is_activated_id   				= $is_activated->id; 	
			$is_activated_token_exptime     = $is_activated->token_exptime;
			$is_activated_deleted_url_at 	= $is_activated->deleted_url_at;

			if (empty($is_activated_deleted_url_at) &&  $is_activated_token_exptime > $regtime) {

				$this->response(array('status_code' => 401 ,'message' => '此账号已经申请，激活中！'));			
				return;			
			}else {

				$re_user_id = $this->user_model->addPersonal($re_user_email,$admin_id,$regtime,$token_exptime,$token,$is_activated_id);

				$this->send_email($re_user_id,$re_user_email ,$token,$active);
				
			}	
		}

		$re_user_id = $this->user_model->addPersonal($re_user_email,$admin_id,$regtime,$token_exptime,$token);	

		$this->send_email($re_user_id, $re_user_email, $token,$active);
		
	}

	public function send_email($re_user_id, $re_user_email, $token,$active)
	{
		if ($re_user_id) {

			$re_user_id=md5($re_user_id);
			$subject = "使命青年团契确认函：请完成您的绑定";
			$user_name = $re_user_email;

			$msg = smtp_mail( $re_user_email,$subject , "null" ,$re_user_id,$token,$user_name,$active);

			$this->response(array('status_code' => 200 , 'message' => $msg,'results' => $re_user_id));
			return;
			
		}else {

			$this->response(array('status_code' => 403 ,'message' => '申请失败！请重试！'));	
			return;	
		}
	}

	public function register()
	{

		$re_user_id 			= $this->input->post('re_user_id');
		$user_name 				= $this->input->post('user_name');
		$password 				= $this->input->post('password');
		$nick 					= $this->input->post('nick');
		$created_by_admin_id    = $this->input->post('created_by_admin_id');

		$get_all_group_name = $this->user_model->findGroupName();
		
		$user_id = $this->user_model->register($re_user_id,$user_name,$password,$nick,$created_by_admin_id);
		

		$is_bool = $this->user_model->update_re_user($re_user_id);

		if (! $is_bool) {
			if (!$del_user_id = $this->user_model->del_register($user_id)) {
				$this->response(array('status_code' => 400,'message' =>' 你的注册失败！'));
				return;
			}
		}
		
		$this->response(array('status_code' => 200,'message' =>' 欢迎您，弟兄/姊妹! 您已经注册成功！接下来，就完善你的个人资料吧！','user_id' => $user_id,'get_all_group_name'=>$get_all_group_name));


	}

	public function improveInformation()
	{
		$user_id 			= 	$this->input->post('user_id');
		$userHeadSrc 		= 	$this->input->post('userHeadSrc');
		$sex 				= 	$this->input->post('sex');
		$group_id 			= 	$this->input->post('group_id');
		$user_nick			=   $this->input->post('user_nick');
		$user_nick 			= 	isset($user_nick)? $user_nick : "";
		
		$is_bool = $this->user_model->update_user_group($user_id,$group_id);
		
		if ($is_bool &&  $is_bool > 0) {
			
			$is_affected = $this->user_model->update_group_leader($user_id);
		}	
		
		$result = $this->user_model->improveInformation($user_id,$userHeadSrc, $sex,$user_nick);
		
		$this->response(array('results' => $result));
	}

	public function resetpassword()
	{
		$user_id 				= 	$this->input->post('user_id');
		$currentPwd 			= 	$this->input->post('currentPwd');
		$confirmNewPwd 			= 	$this->input->post('confirmNewPwd');
		$user_email   			=   $this->input->post('user_email');
//		var_dump($user_email);exit;
		$active = 'resetpwd';

		if (isset($user_email) && ! empty($user_email)) {

			$is_del_user  = $this->user_model->is_deleted_user($user_email);
//			var_dump(!$is_del_user);exit;
			if (!empty($is_del_user)) {
				$this->response(array('status_code' => 400,'message' =>'用户名不存在！'));
				return;
			}

			$is_exist_user_id = $this->user_model->find_user_email_is_exist($user_email);
//			var_dump($is_exist_user_id);exit;
			if (empty($is_exist_user_id) || !$is_exist_user_id) {
				$this->response(array('status_code' => 400,'message' =>'用户名不存在！'));
				return;
			}

			$regtime  		=  	date("Y-m-d H:i:s",time());
			$token_exptime  = 	date("Y-m-d H:i:s",time()+24*3600);

			$token 			= 	md5(md5($user_email.$is_exist_user_id->id.$regtime));	

			$forget_pwd_token_id = $this->user_model->insert_forget_pwd_token($is_exist_user_id->id,$token,$token_exptime);
			
			if (isset($forget_pwd_token_id) && $forget_pwd_token_id ) {

				$this->send_email($forget_pwd_token_id,$user_email ,$token,$active);
				
			}else{

				$this->response(array('status_code' => 404 ,'message' => '已提交申请！请查看你的邮箱！'));	
			}

		}else if(!empty($user_id) && !empty($currentPwd) && !empty($confirmNewPwd)) {

			$is_reset=null;
			$currentPwd = md5(md5($currentPwd));
			$confirmNewPwd = md5(md5($confirmNewPwd));
			
			$isset_id = $this->user_model->checkCurrentPwd($currentPwd,$user_id);
			// var_dump($isset_id);exit;
			if (!empty($isset_id)) {

				$is_reset = $this->user_model->resetpassword($user_id,$confirmNewPwd);
				// var_dump($is_reset);exit;
			}

			$this->response(array('results' => $is_reset ));
		}	

	}

	public function resetpwd_for_forgetpwd()
	{
		$re_user_id 		= $this->input->post('re_user_id');
		$user_name 			= $this->input->post('user_name');
		$password 			= $this->input->post('password');

		// var_dump($re_user_id);exit();

		if (!empty($re_user_id) && !empty($user_name) && !empty($password) ) {
			
			$result 		= $this->user_model->resetpwd_for_forgetpwd($re_user_id,$user_name,$password);
					// var_dump($result);exit();
			$is_bool 		= $this->user_model->update_forget_pwd_token($re_user_id);

				// var_dump($is_bool);exit();
			if ($result && $is_bool) {
				$this->response(array('status_code' => 200 ));	
				return;
			}

			$this->response(array('status_code' => 400 ));
			return; 
		}
	}

	public function user_registered()
	{
		$limit = $this->get('limit') ? $this->get('limit') : self::DEFAULT_LIMIT;
		if($limit > self::MAX_LIMIT) $limit = self::DEFAULT_LIMIT;
		$page = $this->get('page') ? $this->get('page') : 1;
		if($page == 0) $page = 1;

		$total = $this->user_model->count_user_registeres();    

		if($total <= 0 ){
		    $this->response(array('status_code'=>'400'));
		    return;
		}

		$this->load->helper('util_helper');
		$pagination = get_pagination($total, $limit, $page); 

		$results = $this->user_model->user_registered($pagination['limit'], $pagination['offset']);


		$this->response(array('results' =>$results,'total'=>$total ));
	}

	public function checkCurrentPwd()
	{
		$currentPwd  = $this->input->post('currentPwd') ? $this->input->post('currentPwd') : "";
		$user_id  = $this->input->post('user_id') ? $this->input->post('user_id') : "";
		
		$is_bool = false;

		if (empty($currentPwd) || empty($user_id) ) {

			$this->response(array('results' => $is_bool ));	
			return;
		}

		$currentPwd = md5(md5($currentPwd));

		$isset_id = $this->user_model->checkCurrentPwd($currentPwd,$user_id);

		if (! empty($isset_id)) {
			$is_bool = true;
		}else{

			$is_bool = false;
		}

		$this->response(array('results' => $is_bool ));
	}
	
}
