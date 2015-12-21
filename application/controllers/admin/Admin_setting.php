<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Admin_setting extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin/admin_model');
        $this->load->helper('date');
        $this->load->helper('util');
    

	}

	public function home_inform()
	{	
        $home_inform = $this->input->post('home_inform');
        $admin_id    = $this->input->post('admin_id');
        $home_inform_days = $this->input->post('home_inform_days');

		    $home_inform = $home_inform ? $home_inform : "" ;
        $admin_id =  $admin_id ? $admin_id : ""; 
        $home_inform_days =  $home_inform_days ? $home_inform_days : "1";


        $results = $this->admin_model->home_inform($home_inform,$admin_id,$home_inform_days);

        if (!$results) {

            $this->response( array('status_code' =>400 ));
            return;
        }

        $this->response(array('status_code'=> 200,'results' => $results));

	}

    public function  find_home_inform()
    {
        $results = $this->admin_model->find_home_inform();

//        var_dump($results);exit;
        if (empty($results)) {

            $this->response( array('status_code' =>400 ));
            return;
        }

        $overdue_at =    $results->overdue_at; 

        $regtime  =  date("Y-m-d H:i:s",time());

       if ($overdue_at < $regtime){

            $this->response( array('status_code' =>401 ));
            return;
       }
   
        $this->response(array('status_code'=> 200,'results' => $results));
        
    }

    public function  urgentPrayer()
    {
        $urgent_prayer_days    = $this->input->post('urgent_prayer_days');
        $urgent_prayer_content = $this->input->post('urgent_prayer_content');
        $admin_id              = $this->input->post('admin_id');

        $urgent_prayer_days =  $urgent_prayer_days ?  $urgent_prayer_days: '1';
        $urgent_prayer_content =  $urgent_prayer_content ?  $urgent_prayer_content : "";
        $admin_id = $admin_id ? $admin_id : "";

        $results = $this->admin_model->urgentPrayer($urgent_prayer_days,$urgent_prayer_content,$admin_id);
        // var_dump($results);exit();
        if (!$results) {

            $this->response( array('status_code' =>400 ));
            return;
        }

        $this->response(array('status_code'=> 200,'results' => $results));
        



    }
	
    public function find_urgent_prayer()
    {
         $results = $this->admin_model->find_urgent_prayer();

         if (empty($results)) {

             $this->response( array('status_code' =>400 ));
             return;
         }

         $overdue_at =    $results->overdue_at; 

         $regtime  =  date("Y-m-d H:i:s",time());

        if ($overdue_at < $regtime){

             $this->response( array('status_code' =>401 ));
             return;
        }
        
         $this->response(array('status_code'=> 200,'results' => $results));
         
    }
    
   public function find_spirituality()
   {
       $group_id = $this->input->get('group_id');

       $group_id = $group_id ? $group_id : "" ;    

       $get_book_chapter_id = $this->admin_model->find_spirituality($group_id);
       if (! $get_book_chapter_id) {

            $this->response( array('status_code' =>401 ));
            return;        
       } 

       $book_id = isset($get_book_chapter_id['book_id']) ? $get_book_chapter_id['book_id'] : "" ;    
       $chapter_id = isset($get_book_chapter_id['chapter_id']) ? $get_book_chapter_id['chapter_id'] : "" ;
         // var_dump($book_id);exit(); 
       $this->load->model('bibile/bibile_model');
       $volume_name = $this->bibile_model->volume_name($book_id);
       $bible_section = $this->bibile_model->look_volume($book_id,$chapter_id);
       $bible_note = $this->bibile_model->look_volume_note($book_id,$chapter_id);
       
       $this->response(array('status_code' =>200 ,'bible_section' => $bible_section ,'bible_note' => $bible_note,'volume_name'=>$volume_name->name ,'current_chapter_id' => $chapter_id ,'current_book_id' => $book_id));
                
   }   

   public function checkCurrentadminPwd()
   {
       $currentPwd = $this->input->post('currentPwd');
       $admin_id = $this->input->post('admin_id');

       $currentPwd  = $currentPwd ? $currentPwd : "";

       $admin_id    =  $admin_id ? $admin_id : "";
       
       $is_bool = false;

       if (empty($currentPwd) || empty($admin_id) ) {

        $this->response(array('results' => $is_bool )); 
        return;
       }

       $currentPwd = md5(md5($currentPwd));

       $isset_id = $this->admin_model->checkCurrentadminPwd($currentPwd,$admin_id);

       if (! empty($isset_id)) {
        $is_bool = true;

       }else{
        $is_bool = false;
       }

       $this->response(array('results' => $is_bool ));
   }

   public function alteradminPassword()
   {
    $admin_id               =   $this->input->post('admin_id');
    $currentPwd             =   $this->input->post('currentPwd');
    $confirmNewPwd          =   $this->input->post('confirmNewPwd');
    $user_email             =   $this->input->post('user_email');
    $active = 'resetpwd';

    if (isset($user_email) && ! empty($user_email)) {

        $is_exist_admin_id = $this->admin_model->find_admin_email_is_exist($user_email);
        // var_dump($is_exist_admin_id);exit;
        if (empty($is_exist_admin_id) || !$is_exist_admin_id) {
            $this->response(array('status_code' => 400,'message' =>'用户名不存在！'));
            return;
        }

        $regtime        =   date("Y-m-d H:i:s",time());
        $token_exptime  =   date("Y-m-d H:i:s",time()+24*3600);

        $token          =   md5(md5($user_email.$is_exist_admin_id.$regtime));   
        $this->load->model('user/user_model');
        $isadmin = 'Y';
        $forget_pwd_token_id = $this->user_model->insert_forget_pwd_token($is_exist_admin_id,$token,$token_exptime,$isadmin);
        // $forget_pwd_token_id = 38;
        if (isset($forget_pwd_token_id) && $forget_pwd_token_id ) {
            // var_dump($book_id);exitp
            $this->send_email($forget_pwd_token_id,$user_email ,$token,$active,$isadmin);
            
        }else{

            $this->response(array('status_code' => 404 ,'message' => '已提交申请！请查看你的邮箱！'));    
        }

    }else if(!empty($admin_id) && !empty($currentPwd) && !empty($confirmNewPwd)) {

        $is_reset=null;
        $currentPwd = md5(md5($currentPwd));
        $confirmNewPwd = md5(md5($confirmNewPwd));
        
        $isset_id = $this->admin_model->checkCurrentadminPwd($currentPwd,$admin_id);
        // var_dump($isset_id);exit;
        if (!empty($isset_id)) {

            $is_reset = $this->admin_model->resetadminpassword($admin_id,$confirmNewPwd);
            // var_dump($is_reset);exit;
        }

        $this->response(array('results' => $is_reset ));
    }
   }

   public function send_email($re_user_id, $re_user_email, $token,$active,$isadmin='')
   {
    if ($re_user_id) {

      $re_user_id=md5($re_user_id);
      $subject = "使命青年团契确认函：请完成您的绑定";
      $user_name = $re_user_email;
      // var_dump($isadmin);exit;
      $msg = smtp_mail( $re_user_email,$subject , "null" ,$re_user_id,$token,$user_name,$active,$isadmin);
      // var_dump($msg);exit;

      $this->response(array('status_code' => 200 , 'message' => $msg,'results' => $re_user_id));
      return;
      
    }else {

      $this->response(array('status_code' => 403 ,'message' => '申请失败！请重试！')); 
      return; 
    }
   }



   public function search_bibile()
   {  
      $testament   = $this->input->post('testament');
      $book_id     = $this->input->post('book_id');
      $chapter_id  = $this->input->post('chapter_id');
      $form_key    = $this->input->post('form_key');

      $testament      =  $testament ? $testament : "";
      $book_id        =  $book_id ? $book_id : "";
      $chapter_id     =   $chapter_id ? $chapter_id : "";

      $form_key       =   $form_key ? $form_key : "" ;

      $results  = $this->admin_model->search_bibile($testament,$book_id,$chapter_id,$form_key);

      if (!$results || empty($results)) {
        $this->response(array('status_code'=> 400 ));
        return;
      }

      $this->response(array('status_code'=> 200 ,'results' => $results));
   }

   public function setting_todayScriptures()
   {
      $params_json   =  $this->input->post('params_json');
      $params = json_decode($params_json);        
      $param_array = array();

     if (!empty($params->book_id) && !empty($params->chapter_id) && !empty($params->section_id) && ! empty($params->created_by) ) {
       for ($i=0; $i <count($params->book_id) ; $i++) { 
          $param_array[$i]['book_id']    = $params->book_id[$i];
          $param_array[$i]['chapter_id'] = $params->chapter_id[$i];
          $param_array[$i]['section_id'] = $params->section_id[$i];
          $param_array[$i]['created_by'] = $params->created_by;
          $param_array[$i]['created_at'] = mdate('%Y-%m-%d %H:%i:%s', now());

       }        
      }
     
      $results  = $this->admin_model->setting_todayScriptures($param_array);
    
     if (!$results || empty($results)) {
       $this->response(array('status_code'=> 400 ));
       return;
     }

     $this->response(array('status_code'=> 200 ,'results' => $results));
   }

   public function find_todayScriptures()
   {

       $results = $this->admin_model->find_todayScriptures();
       // var_dump($results);exit;
       if (!$results || empty($results)) {
         $this->response(array('status_code'=> 400 ));
         return;
       }

       $this->response(array('status_code'=> 200 ,'results' => $results));

   }

   public function notice_groups()
   {
     $group_id_str        = $this->input->post('group_id_str');
     $admin_id            = $this->input->post('admin_id');
     $notice_contents     = $this->input->post('notice_contents');

     $results = $this->admin_model->notice_groups($group_id_str,$admin_id,$notice_contents);
     // var_dump($results);exit;
     if (!$results || empty($results)) {
       $this->response(array('status_code'=> 400 ));
       return;
     }

     $this->response(array('status_code'=> 200 ,'results' => $results));
   }

   public function reset_pwd_for_forget()
   {
     $op = $this->get('op');
     $id = $this->get('id');
     $token = $this->get('token');

     if( !empty($op) & $op == 'resetpwd' && !empty($id) && !empty($token)){
        $this->load->model('user/user_model');
        $find_forget_pwd_token_by_token  = $this->user_model->find_forget_pwd_token_by_token($token);
        if($find_forget_pwd_token_by_token){

          $forget_pwd_token_id    = $find_forget_pwd_token_by_token->id;
          $re_user_id             = $find_forget_pwd_token_by_token->re_user_id;
          $get_admin_info         = $this->admin_model->get_admin_info($re_user_id);

          $token_exptime          = $find_forget_pwd_token_by_token->token_exptime;
          $deleted_at             = $find_forget_pwd_token_by_token->deleted_at;
          $is_admin               = $find_forget_pwd_token_by_token->is_admin;

          
          $regtime  =  date("Y-m-d H:i:s",time());

          if( ($id == md5($forget_pwd_token_id)) && ($regtime < $token_exptime ) && empty($deleted_at) && $is_admin == 'Y' ){
              $this->admin_model->is_send_email($forget_pwd_token_id);
              $this->response(array('status_code' => 200,'get_admin_info'=>$get_admin_info,'op' =>$op));      
              return;
          }            
        }
     }

      $this->response(array('status_code' => 400,'op' =>$op));      
      return ;
   }

   public function reset_admin_pwd()
   {
     $pwd = $this->post('pwd2');
     $admin_id = $this->post('admin_id');

     $results = $this->admin_model->reset_admin_pwd($pwd,$admin_id);
     if (!$results || empty($results)) {
       $this->response(array('status_code'=> 400 ));
       return;
     }

     $this->response(array('status_code'=> 200 ,'results' => $results));

   }

   /**
      update 12-20
   */
    public function admin()
    {

      $results = $this->admin_model->admin();
      
      if (!$results || empty($results)) {
        $this->response(array('status_code'=> 400 ));
        return;
      }

      $this->response(array('status_code'=> 200 ,'results' => $results));
    }



}
