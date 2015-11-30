<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Login extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/admin_model');

	}

	public function checkLogin()
	{	
		$admin_name        =   $_REQUEST['admin_name'];
        $admin_pwd         =   $_REQUEST['admin_pwd'];

        if ( ! $admin_name || ! $admin_pwd)
        {        
            $this->response(array('status_code'=> 400 ,'message' => '输入错误！'), TQ_PARAMETER_MISSING_INVALID);
            return;

        }

         $account = $this->admin_model->find_by_email($admin_name);
         // var_dump($account);exit;
         // $temp = md5(md5($admin_pwd));
         // var_dump($temp);exit;
         // exit;
         if ( ! $account)
         {
             $this->response(array('status_code'=> 401 ,'message' => '用户名或者密码错误！'), TQ_PARAMETER_MISSING_INVALID);
             return;
         }

         if ( ! $this->admin_model->check_password($admin_name,$admin_pwd))
         {
             $this->response(array('status_code'=> 402 ,'message' => '用户名或者密码错误！'), TQ_PARAMETER_MISSING_INVALID);
             return;
         }

        $token = $this->admin_model->find_token_by_client_id_and_account_id($account->id);

        $this->response(array('status_code'=> 200,'results' => array('account_id' => $account->id, 'access_token' => $token->access_token)));


	}
	
    public function improveInformation()
    {

        $admin_id            =   $this->input->post('admin_id');
        $adminHeadSrc        =   $this->input->post('adminHeadSrc');
        $gender              =   $this->input->post('gender');
        $admin_nick          =   $this->input->post('admin_nick') ? $this->input->post('admin_nick') : "";
        
        $result = $this->admin_model->improveInformation($admin_id,$adminHeadSrc, $gender, $admin_nick);
            // var_dump($result);exit;
        $this->response(array('results' => $result));
    }
}
