<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_alert_messages extends MY_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('messages/user_messages_model');

	}

	public function del_alert_comments_by_spirituality_id()
	{
		$spirituality_id = $this->input->post('spirituality_id');
		$user_id         = $this->input->post('user_id');

		$result = $this->user_messages_model->del_alert_comments_by_spirituality_id($spirituality_id,$user_id);

		if (! $result) {
		    $this->response(array('status_code'=> 400));
		    return;     
		}        
		
		$this->response(array('status_code'=> 200 , 'results' =>$result));	
	}

	public function del_all_praise_alert()
	{
		$user_id = $this->get("user_id");
		$result = $this->user_messages_model->del_all_praise_alert($user_id);	
		if (! $result) {
		    $this->response(array('status_code'=> 400));
		    return;     
		}        
		
		$this->response(array('status_code'=> 200 , 'results' =>$result));	

	}

	public function del_prompt_alerts()
	{
		$user_id = $this->input->get('user_id');
		$results = $this->user_messages_model->del_prompt_alerts($user_id);
		// $results = 1;

		if (! $results) {
		    $this->response(array('status_code'=> 400));
		    return;     
		}

		$this->response(array('status_code'=> 200,'results' => $results));	
	}

	public function remove_alert_by_id()
	{
		$alert_id = $this->get('alert_id');
		$results = $this->user_messages_model->remove_alert_by_id($alert_id);
		// $results = 1;

		if(!$results){
			$this->response(array('status_code'=> 400));
			return;
		}

		$this->response(array('status_code'=> 200,'results' => $results));
	}

	public function del_alert_message_by_user_id($user_id)
	{
		$results = $this->user_messages_model->del_alert_message_by_user_id($user_id);

		if(!$results){
			$this->response(array('status_code'=> 400));
			return;
		}

		$this->response(array('status_code'=> 200,'results' => $results));
	}
}
