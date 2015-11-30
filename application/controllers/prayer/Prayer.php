<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Prayer extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('prayer/prayer_model');

	}

	public function get_tq_content_prayer()
	{	
        $tq_prayer_id = $this->input->get('tq_prayer_id') ? $this->input->get('tq_prayer_id'): "" ;
		$user_id = $this->input->get('user_id') ? $this->input->get('user_id'): "" ;

		$result = $this->prayer_model->get_tq_content_prayer($tq_prayer_id,$user_id);
        // var_dump($result);exit;
        if (! $result) {            
            $this->response(array('status_code' => 404 ));
        }

        $this->response(array('status_code'=> 200,'results' => $result['data_return'],'is_send' => $result['is_send'] , 'total'=>count($result['data_return'])));

	}

	public function send_prayer()
    {
        $user_id            = $this->input->post('user_id') ? $this->input->post('user_id')  : "";
        $urgent_prayer_id   = $this->input->post('urgent_prayer_id') ? $this->input->post('urgent_prayer_id') : "" ;
        $content_prayer     = $this->input->post('content_prayer') ? $this->input->post('content_prayer')  : "";

        $result = $this->prayer_model->send_prayer($user_id,$urgent_prayer_id,$content_prayer);

        if (! $result) {            
            $this->response(array('status_code' => 404 ));
        }

        $this->response(array('status_code'=> 200,'results' => $result));

    }

    public function del_payer()
    {
        $urgent_id = $this->input->get('urgent_id') ? $this->input->get('urgent_id') : "" ;
        $user_id   = $this->input->get('user_id') ? $this->input->get('user_id') : "" ; 
        $del_by    = $this->input->get('del_by') ? $this->input->get('del_by') : "" ;


        $results = $this->prayer_model->del_payer($urgent_id,$user_id,$del_by);
        // var_dump($results);exit;

        if (! $results || empty($results)) {

            $this->response(array('status_code'=> 400 ));
            return;
        }

        $this->response(array('status_code'=> 200 ));
        
    }
	
    public function send_group_prayer()
    {
        $user_id                 = $this->input->post('user_id') ? $this->input->post('user_id')  : "";
        $group_prayer_contents   = $this->input->post('group_prayer_contents') ? $this->input->post('group_prayer_contents') : "" ;
        $group_prayer_id         = $this->input->post('group_prayer_id') ? $this->input->post('group_prayer_id')  : "";

        $result = $this->prayer_model->send_group_prayer($user_id,$group_prayer_contents,$group_prayer_id);

        if (! $result) {            
            $this->response(array('status_code' => 404 ));
        }

        $this->response(array('status_code'=> 200,'results' => $result));
    }

    public function get_group_prayer()
    {
        $group_prayer_id = $this->input->get('group_prayer_id') ? $this->input->get('group_prayer_id'): "" ;
        $user_id = $this->input->get('user_id') ? $this->input->get('user_id'): "" ;

        $result = $this->prayer_model->get_group_prayer($group_prayer_id,$user_id);
         // var_dump($result);exit;
         if (! $result) {            
             $this->response(array('status_code' => 404 ));
         }

         $this->response(array('status_code'=> 200,'results' => $result['data_return'],'is_send_group_prayer' => $result['is_send_group_prayer'] , 'group_prayer_total'=>count($result['data_return'])));   
    }

    public function del_group_payer()
    {
        $prayer_for_group_id = $this->input->get('prayer_for_group_id') ? $this->input->get('prayer_for_group_id') : "" ;
        $user_id   = $this->input->get('user_id') ? $this->input->get('user_id') : "" ; 
        $del_by    = $this->input->get('del_by') ? $this->input->get('del_by') : "" ;


        $results = $this->prayer_model->del_group_payer($prayer_for_group_id,$user_id,$del_by);
        // var_dump($results);exit;

        if (! $results || empty($results)) {

            $this->response(array('status_code'=> 400 ));
            return;
        }

        $this->response(array('status_code'=> 200 ));
    }

    public function  get_all_prayer()
    {
        $limit  = $this->get('limit');
        $page   = $this->get('page');

        $result = $this->prayer_model->get_all_prayer($limit,$page);
        // var_dump($result);exit;

        if (! $result) {            
            $this->response(array('status_code' => 404 ));
        }

        $this->response(array('status_code'=> 200,'results' => $result['temp_data'],'total' => $result['total']));
    }
}
