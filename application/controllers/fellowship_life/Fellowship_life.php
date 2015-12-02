<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Fellowship_life extends MY_Controller {

    const DEFAULT_LIMIT = 50;
    const MAX_LIMIT = 20;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('fellowship_life/fellowship_life_model');

	}

	public function create_album()
	{	
		$user_id     = $this->input->post('user_id');
		$album_name  = $this->input->post('album_name');
        $group_id    = $this->input->post('group_id') ;

		$result = $this->fellowship_life_model->create_album($user_id,$album_name,$group_id);

        if ( isset($result) && $result>=1  ) {

            $this->response(array( 'status_code'=> 200 ,'message'=>'添加'.'<b>'.$album_name.'</b>'.'成功！'));
            return;
        }else if ( $result == 'Y') {
            $this->response(array('status_code'=> 401 ,'message'=>'添加'.'<b>'.$album_name.'</b>'.'已存在！'));
            return;
        }else {            
            $this->response(array('status_code'=> 400 ,'message'=>'添加'.'<b>'.$album_name.'</b>'.'失败，请重试！'));
            return;
        } 
	}
	
    public function get_user_album_name()
    {
        $user_id     = $this->input->get('user_id');
        
        $results = $this->fellowship_life_model->get_user_album_name($user_id);
        if (!$results) {
            $this->response(array('status_code'=> 401));
            return;
        }

        $this->response(array('status_code'=> 200 , 'results' =>$results));
    }

    public function save_data()
    {
        $album_id     = $this->input->post('album_id');
        $paths        = $this->input->post('paths');
        $user_id      = $this->post('user_id');

        $insert_id = $this->fellowship_life_model->save_data($album_id,$paths);

        $this->fellowship_life_model->alert_user_album_src($insert_id,$user_id);

        $this->response(array('results' => count($insert_id)));
        
    }

    public function rename_album_name()
    {
        $album_id   = $this->get('album_id');
        $album_name = $this->get('album_name');

        $affect_id = $this->fellowship_life_model->rename_album_name($album_id,$album_name);

        if (! $affect_id) {
            $this->response(array('status_code'=> 401));
            return;     
        }  

        $this->response(array('status_code'=> 200 , 'results' => $affect_id));
    }

    public function group_albums()
    {
        $group_id        = $this->input->get('group_id');

        $results = $this->fellowship_life_model->group_albums($group_id);
        // var_dump($results);exit;
        
        if (! $results) {
            $this->response(array('status_code'=> 401));
            return;     
        }        
        
        $this->response(array('status_code'=> 200 , 'results' =>$results));

    }

    public function see_user_albums()
    {
        $user_id        = $this->input->get('user_id');

        $results = $this->fellowship_life_model->see_user_albums($user_id);
        // var_dump($results);exit;
        if (! $results) {
            $this->response(array('status_code'=> 401));
            return;     
        }        
        
        $this->response(array('status_code'=> 200 , 'results' =>$results));
        
    }

    public function see_user_photos()
    {
        $album_id        = $this->input->get('album_id');
        $user_id         = $this->input->get('user_id');
        $limit = $this->get('limit');

        $limit = $limit ? $limit : self::DEFAULT_LIMIT;
        if($limit > self::MAX_LIMIT) $limit = self::DEFAULT_LIMIT;
        $page = $this->get('page');
        $page = $page ? $page : 1;

        if($page == 0) $page = 1;

        $total = $this->fellowship_life_model->count_user_photos_by_album_id($album_id);
        if($total <= 0 || !$total ){
            $this->response(array('status_code'=> 400));
            return;
        }

        $this->load->helper('util_helper');
        $pagination = get_pagination($total, $limit, $page);  

        $use_nick_Hsrc   = null;

        if (!empty($user_id)) {
            $this->load->model('user/user_model');
            $use_nick_Hsrc = $this->user_model->get_user_head_src_and_nick($user_id);
        }

        $get_photos = $this->fellowship_life_model->see_user_photos($album_id,$pagination['limit'], $pagination['offset']);
        // var_dump($get_photos);exit;

        $this->response(array('status_code'=> 200 , 'total' => $total, 'results' => array('use_nick_Hsrc' => $use_nick_Hsrc,'results' =>$get_photos )));
    }

    public function get_photos_count()
    {

        $this->load->model('tq_header_info_model');
        $group_info = $this->tq_header_info_model->findGroup();
        // var_dump($group_info);exit;

        $results = $this->fellowship_life_model->get_photos_count($group_info);
        // var_dump($results);exit;
        
        if (! $results) {
            $this->response(array('status_code'=> 401));
            return;     
        }        
        
        $this->response(array('status_code'=> 200 , 'results' =>$results));
    }

    public function del_photos()
    {
        $src_id        = $this->input->get('src_id') ;
        $user_id       = $this->input->get('user_id');
        $admin_id      = $this->input->get('admin_id');
        $results       = $this->fellowship_life_model->del_photos($src_id,$user_id,$admin_id);

        if(is_numeric($results)){
           $affect_id =  $this->fellowship_life_model->del_alert_user_album_src($src_id);
        }
        // $results = 1;
        if (! isset($results) || empty($results)) {
            $this->response(array('status_code'=> 401));
            return;     
        }        
        
        $this->response(array('status_code'=> 200 , 'results' =>$results));
    }

    public function recently_fellowship_photos()
    {
        $regtime  =  date("Y-m-d H:i:s",time());

        $results = $this->fellowship_life_model->recently_fellowship_photos($regtime);       
        // var_dump($results);exit;
        
        if (! isset($results) || empty($results)) {
            $this->response(array('status_code'=> 401));
            return;     
        }        
        
        $this->response(array('status_code'=> 200 , 'results' =>$results));
    }

    public function get_today_user_photos()
    {  
        $limit = $this->get('limit') ;

        $limit = $limit ? $limit : self::DEFAULT_LIMIT;

        if($limit > self::MAX_LIMIT) $limit = self::DEFAULT_LIMIT;
        $temp_page = $this->get('page');
        $page = $temp_page ? $temp_page : 1;
        if($page == 0){ $page = 1;}

        $total = $this->fellowship_life_model->count_user_photos();
        // var_dump($total);exit;

        if($total <= 0 || !$total ){
            $this->response(array('status_code'=> 400));
            return;
        }

        if(($total < $limit) && ( $page >=2)){
             $this->response(array('status_code'=> 400));
            return;
        }

        $this->load->helper('util_helper');
        $pagination = get_pagination($total, $limit, $page);    
        $user_photos_results = $this->fellowship_life_model->get_all_user_photos($pagination['limit'], $pagination['offset']);       
        
        // if (!$user_photos_results) {
        //     $this->response(array('status_code'=> 400));
        //     return;
        // }
        // $this->response(array('status_code'=> 200 ,'total' => $total, 'results'=>$results ));


               
        // var_dump($user_photos_results);exit;
        if (! isset($user_photos_results) || empty($user_photos_results)) {
            $this->response(array('status_code'=> 401));
            return;     
        }        
        
        $this->response(array('status_code'=> 200 , 'total' => $total, 'results' =>$user_photos_results));
    }
}
