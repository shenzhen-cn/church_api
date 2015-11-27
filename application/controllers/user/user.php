<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class User extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user/user_model');

	}

	public function send_spirituality()
	{	
		$gold_sentence = $this->input->post('gold_sentence') ? $this->input->post('gold_sentence') : "";
        $heart_feeling = $this->input->post('heart_feeling') ? $this->input->post('heart_feeling') : "";
        $response = $this->input->post('response') ? $this->input->post('response') : "";
        $chapter_id = $this->input->post('current_chapter_id') ? $this->input->post('current_chapter_id') : "";
        $book_id = $this->input->post('current_book_id') ? $this->input->post('current_book_id') : "";
		$user_id = $this->input->post('user_id') ? $this->input->post('user_id') : "";

		$result = $this->user_model->send_spirituality($gold_sentence,$heart_feeling,$response,$chapter_id,$book_id,$user_id);

        if (!$result) {
            
            $this->response(array('status_code'=> 400));
            return ;
        }

		$this->response(array('status_code'=> 200, 'results' => $result));

	}

    // public function  find_user_spirituality()
    // {
    //     $group_id = $this->input->get('group_id') ? $this->input->get('group_id') : "" ;
    //     $user_id = $this->input->get('user_id') ? $this->input->get('user_id') : "" ;
    //     $chapter_id = $this->input->get('chapter_id') ? $this->input->get('chapter_id') : "" ;
    //     $book_id = $this->input->get('book_id') ? $this->input->get('book_id') : "" ;

    //     $this->load->model('group/group_model');
    //     $user_info = $this->group_model->find_all_users_by_group_id($group_id);
    //     $results = $this->user_model->find_user_spirituality($group_id,$user_info['data_array'],$chapter_id,$book_id);

    //     $is_spirituality  = $this->user_model->find_user_is_spirituality($user_id);
    

    //     if (empty($results)) {

    //      $this->response( array('status_code' =>400 ));
    //      return;
    //     }

    //     $this->response(array('status_code'=> 200,'results' => $results['data_return'],'count_is_spirituality' => $results['count'], 'is_spirituality' => $is_spirituality));
    // } 

    public function  find_user_spirituality()
    {
        $group_id = $this->input->get('group_id') ? $this->input->get('group_id') : "" ;
        $this->load->model('group/group_model');
        $count_users_group = null;
        if (!empty($group_id)) {            
            $get_group_users = $this->group_model->find_user_by_group_id($group_id);
            $count_users_group = count($get_group_users);
        }
        
        $user_id = $this->input->get('user_id') ? $this->input->get('user_id') : "" ;
        $chapter_id = $this->input->get('chapter_id') ? $this->input->get('chapter_id') : "" ;
        $book_id = $this->input->get('book_id') ? $this->input->get('book_id') : "" ;
        $results = $this->user_model->find_user_spirituality($group_id,$chapter_id,$book_id,$user_id);

        $is_spirituality  = $this->user_model->find_user_is_spirituality($user_id);
        if (empty($results)) {

         $this->response( array('status_code' =>400,'is_spirituality' => $is_spirituality,'count_users_group' =>$count_users_group ));
         return;
        }

        $this->response(array('status_code'=> 200,'results' => $results, 'is_spirituality' => $is_spirituality, 'count_users_group' => $count_users_group));
    }    
	
    public function reminder_spirituality_by_id()
    {
        $user_id = $this->input->get('user_id');
        // var_dump($user_id);exit;
        $regtime  =  date("Y-m-d H:i:s",time());
        
        $results = $this->user_model->reminder_spirituality_by_id($user_id,$regtime);
        // var_dump($results);exit;

        $this->response(array('results' => $results));

    }
    
    public function get_all_events_for_json()
    {
        $user_id   = $this->input->get('user_id');

        $results = $this->user_model->get_all_events_for_json($user_id);

        $this->response(array('results' => $results));
    }	

    public function add_praise()
    {
        $user_id           = $this->get('user_id');
        $spirituality_id   = $this->get('spirituality_id');

        $insert_id = $this->user_model->add_praise($user_id,$spirituality_id);

        if (is_numeric($insert_id)) {            
            $this->user_model->alert_user_for_praise($user_id,$spirituality_id,$insert_id);
        }
        // $insert_id = 1;
        $this->response(array('results' => $insert_id));
    }

    public function get_personal_data_for_spirituality($limit='', $offset='')
    {
        $user_id = $this->get('user_id');

        $results = $this->user_model->get_personal_data_for_spirituality($user_id,$limit, $offset);
        $this->response(array('results' => $results));

    }

    public function get_informations()
    {
        $user_id          =  $this->get('user_id');
        $spirituality_id  =  $this->get('spirituality_id');
        $results = $this->user_model->get_informations($user_id,$spirituality_id);

        if(!$results){
           $this->response( array('status_code' =>400));
           return; 
        }

        $this->response(array('status_code' =>200,'results' => $results));

    }

    public function  del_users_by_id()
    {
        $user_id = $this->post('user_id');
        $admin_id = $this->post('admin_id');

        $results = $this->user_model->del_users_by_id($user_id,$admin_id);
        // var_dump($results);exit;   

        if(!$results){
           $this->response( array('status_code' =>400));
           return; 
        }

        $this->response(array('status_code' =>200,'results' => $results));
    }
}
