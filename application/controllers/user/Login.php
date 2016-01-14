<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Login extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user/user_model');

	}

	public function find()
	{
		$user_name_email  =	$_REQUEST['user_name_email'];
        $password		  =	$_REQUEST['password'];

        if ( ! $user_name_email || ! $password)
        {        
            $this->response(array('status_code'=> 400 ,'message' => '输入错误！'), TQ_PARAMETER_MISSING_INVALID);
            return;

        }

        $account = $this->user_model->find_by_email($user_name_email);

        // Check for valid user
        if ( ! $account)
        {
            $this->response(array('status_code'=> 401 ,'message' => '用户名或密码错误！'), TQ_PARAMETER_MISSING_INVALID);
            return;
        }

        if ( ! $this->user_model->check_password($user_name_email,$password))
        {
            $this->response(array('status_code'=> 402 ,'message' => '用户名或密码错误！'), TQ_PARAMETER_MISSING_INVALID);
            return;
        }

        //冻结账号
        $use_status = $account->use_status ;

        if (empty($use_status) || $use_status != 'A') {

            $this->response(array('status_code'=> 402 ,'message' => '登录异常，请联系管理员！'), TQ_PARAMETER_MISSING_INVALID);            
            return;
        }
        

        $token = $this->user_model->find_token_by_client_id_and_account_id($account->id);

        // Output 
        $this->response(array('status_code'=> 200,'results' => array('account_id' => $account->id, 'access_token' => $token->access_token)));

	}
	
}
