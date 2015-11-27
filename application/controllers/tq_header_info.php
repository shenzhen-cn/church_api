<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class tq_header_info extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('tq_header_info_model');

	}

	public function find()
	{	
		$user_id  =	$_REQUEST['user_id'];

		$result['user_info'] 	= $this->tq_header_info_model->findUser($user_id);

		$group_id = isset($user_info->group_id) ? $user_info->group_id : "";
		$userHeadSrc_info = $this->tq_header_info_model->finduserHeadSrc($user_id);

		$userHead_src  = isset($userHeadSrc_info)? $userHeadSrc_info->userHead_src : "";
		$this->load->model('priest_preach/priest_preach_model');
        $clas_p_p = $this->priest_preach_model->find_class_name_priest_preach();
        
        $result['clas_p_p']  = null;
        if (! $userHead_src  || empty($userHead_src)) {
        	$result['userHead_src']   = null;	
        }else if (empty($clas_p_p) ) {

        	$result['clas_p_p']   = null;	
        }else{
        	$result['userHead_src'] = $userHead_src;
        	$result['clas_p_p']   = $clas_p_p;	
        }	


		$result['group_info'] = $this->tq_header_info_model->findGroup();
		// var_dump($result['group_info']);exit;

		$this->response(array('results' => $result));

	}

	public function find_tq_admin_header_info()
	{
		$result_array  = array();
		$admin_id  =	$_REQUEST['admin_id'];  

		$admin_info 	= $this->tq_header_info_model->get_admin_info($admin_id);
		$class_name_priest_preach = $this->tq_header_info_model->get_class_name_priest_preach();
		$group_info = $this->tq_header_info_model->findGroup();
		
		$result_array = array(
			'admin_info' => $admin_info,
			'class_name_priest_preach' => $class_name_priest_preach,
			'group_info' => $group_info,
			);

		$this->response(array('results' => $result_array));

	}

	public function get_tip_messages()
	{
		$user_id = $this->input->get('user_id');
		$results = $this->tq_header_info_model->get_tip_messages($user_id);
		// var_dump($results);exit;
		$this->response(array('results' => $results));

	}

	// public function remove_alert_by_id()
	// {
	// 	$alert_id = $this->input->get('alert_id');
	// 	$results = $this->tq_header_info_model->remove_alert_by_id($alert_id);
	// 	$this->response(array('results' => $results));
	// }

	public function remove_alert_by_user_id()
	{
		$user_id = $this->input->get('user_id');
		$table_name = $this->input->get('table_name');

		$results = $this->tq_header_info_model->remove_alert_by_user_id($user_id,$table_name);
		$this->response(array('results' => $results));	
	}

	
	
}
