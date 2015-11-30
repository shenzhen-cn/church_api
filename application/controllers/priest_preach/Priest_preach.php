<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Priest_preach extends MY_Controller
{
     const DEFAULT_LIMIT = 10;
     const MAX_LIMIT = 20;      

    public function __construct()
    {
        parent::__construct();
        $this->load->model('priest_preach/priest_preach_model');
        $this->load->model('messages/user_messages_model');                
    }

    public function add_course_class()
    {   
        $course_class = $this->input->post('course_class');
        $admin_id = $this->input->post('admin_id');
        // var_dump($admin_id);exit();
        $result = $this->priest_preach_model->add_course_class($course_class,$admin_id);
        // var_dump();exit();
        if (!$result) {
             $this->response(array('status_code'=> 400 ,'message'=>'添加'.'<b>'.$course_class.'</b>'.'失败！'));
             return;
         }else if (isset($result->id)) {

            $this->response(array('status_code'=> 401 ,'message'=>'你提交的'.'<b>'.$course_class.'</b>'.'已经存在!'));
            return;
        }

        $this->response(array('status_code'=> 200 ,'message'=>'<b>'.$course_class.'</b>'.'成功添加!'));
    }

    public function find_class_name_priest_preach()
    {
        $result = $this->priest_preach_model->find_class_name_priest_preach();

        if (empty($result)) {

            $this->response(array('status_code'=> 400));
            return;
        }

        $this->response(array('status_code'=> 200 ,'results'=>$result ));
    }

    public function getContent()
    {
        $c_p_p_id         = $this->input->post('p_p_c_n_id');
        $file_name        = $this->input->post('file_name');
        $full_path        = $this->input->post('full_path');
        $orig_name        = $this->input->post('orig_name');
        $file_size        = $this->input->post('file_size');
        $admin_id         = $this->input->post('admin_id');
        $course_title     = $this->input->post('course_title');
        $share_from       = $this->input->post('share_from');
        $course_keys      = $this->input->post('course_keys');
        // var_dump($course_keys);exit;
        $result = $this->priest_preach_model->getContent($c_p_p_id,$file_name,$full_path,$orig_name,$file_size,$admin_id,$course_title,$share_from,$course_keys);
        
        if (!$result || $result <= 0) {

            $this->response(array('status_code'=> 400));
            return;
        }

        // $result = 53;
        
        $this->priest_preach_model->alert_upload_priest_preach($result);
        
        $this->response(array('status_code'=> 200 ,'results'=>$result ));

    }   

    public function get_priest_preach_by_id()
    {
        $id = $this->input->get('id');
        $limit = $this->get('limit');
        $page = $this->get('page');

        $limit =  $limit ? $limit : self::DEFAULT_LIMIT;
        if($limit > self::MAX_LIMIT) $limit = self::DEFAULT_LIMIT;
        $page = $page ? $page : 1;
        if($page == 0) $page = 1;


        $count = $this->priest_preach_model->count_priest_preach_by_id($id);    
        $total = $count->count;
        if($total <= 0 || !$count ){
            $this->response(array('status_code'=>'400'));
            return;
        }


        $this->load->helper('util_helper');
        $pagination = get_pagination($total, $limit, $page);    
        $results = $this->priest_preach_model->get_priest_preach_by_id($id, $pagination['limit'], $pagination['offset']);
        
        if (!$results) {
            $this->response(array('status_code'=> 400));
            return;
        }
        $this->response(array('status_code'=> 200 ,'total' => $total, 'results'=>$results ));

    }

    public function  pp_read_by_id()
    {
        $id = $this->input->get('id');
        // echo $id;exit;
        $results = $this->priest_preach_model->pp_read_by_id($id);
        // var_dump($results);exit;

        if (!$results) {
            $this->response(array('status_code'=> 400));
            return;
        }
        $this->response(array('status_code'=> 200 ,'results'=>$results ));
    }

    public function del_course()
    {
        $id = $this->input->get('id');
        $admin_id = $this->input->get('admin_id');

        $result = $this->priest_preach_model->del_course($id,$admin_id);
        if(is_numeric($result)){
            $this->user_messages_model->del_alert_content_priest_preach($id);
        }
        if (!$result || $result <= 0) {
            $this->response(array('status_code'=> 400));
            return;
        }
        $this->response(array('status_code'=> 200 ,'results'=>$result ));
        
    }

    public function  getmyEditor()
    {
        $myEditor       = $this->input->post('myEditor');
        $admin_id       = $this->input->post('admin_id');
        $document_id    = $this->input->post('document_id');

        $result = $this->priest_preach_model->getmyEditor($myEditor,$admin_id,$document_id);

        if (!$result || $result <= 0) {
            $this->response(array('status_code'=> 400));
            return;
        }

        $this->response(array('status_code'=> 200 ,'results'=>$result ));
    }  
  
    public function read_myEdit_by_id()
    {
        $document_id  =  $this->input->get('document_id');
        // var_dump($document_id);exit;
        $result = $this->priest_preach_model->read_myEdit_by_id($document_id);
        // var_dump($result);exit;
        if (!isset($result) || count($result['rows']) <= 0) {
            $this->response(array('status_code'=> 400));
            return;
        }

        $this->response(array('status_code'=> 200 ,'results'=>$result ));  
    }     
    
    public function del_document()
    {
       $id = $this->input->get('id');
       $admin_id = $this->input->get('admin_id');

       $result = $this->priest_preach_model->del_document($id,$admin_id);
       // var_dump($result);exit;

       if (!$result || $result <= 0) {
           $this->response(array('status_code'=> 400));
           return;
       }
       $this->response(array('status_code'=> 200 ,'results'=>$result ));        
    }
}
