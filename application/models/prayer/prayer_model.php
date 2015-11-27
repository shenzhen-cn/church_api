<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Prayer_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
		$this->load->model('/tq_header_info_model');

	}
	
	public function get_tq_content_prayer($tq_prayer_id,$user_id='')
	{		
		if (!empty($tq_prayer_id)) {
			$data_return  = array(); 
			$is_send = 'N';
			$this->db->select('prayer_for_urgent.id as urgent_id ,user_id,nick,group_id,content_prayer,prayer_for_urgent.created_at');
			$this->db->from('prayer_for_urgent');
			$this->db->join('user', 'user.id = prayer_for_urgent.user_id', 'left');
			$this->db->where('urgent_prayer_id', $tq_prayer_id);
			$this->db->where('prayer_for_urgent.deleted_at is null');
			$this->db->where('user.deleted_at is null');
			$this->db->order_by('urgent_id', 'desc');
			$data1 =  $this->db->get();
			// var_dump($data1->result_array());exit;
			foreach ($data1->result_array() as $row_cer) {

				$created_at = $row_cer['created_at'];
				$row_cer['conversion_time'] = $this->tranTime(strtotime($created_at));				

				$user_id1 = $row_cer['user_id'];
				$HeadSrc  = $this->tq_header_info_model->finduserHeadSrc($user_id1);
				$row_cer['userHeadSrc'] = $HeadSrc->userHead_src;

				if ($user_id == $user_id1 ) {
					$is_send = 'Y';
				}

				$data_return[] = $row_cer; 
				// $count++;
			}
			// var_dump($is_send);exit;
			return array('data_return' => $data_return,
						'is_send' => $is_send
			 );	
		}else {
			return false;
		}
	}

	public function send_prayer($user_id,$urgent_prayer_id,$content_prayer)
	{
		if (!empty($user_id) && !empty($urgent_prayer_id) && !empty($content_prayer) ) {

			$this->db->set('user_id', $user_id);
			$this->db->set('urgent_prayer_id', $urgent_prayer_id);
			$this->db->set('content_prayer',  $content_prayer);

			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));

			$this->db->insert('prayer_for_urgent');
						
			return $this->db->insert_id();

		}else {
			return false;
		}
	}
	
	public function tranTime($time) {

	    $rtime = date("m-d H:i", $time);
	    $htime = date("H:i", $time);
	    $time = time() - $time;
	 
	    if ($time < 60) {
	        $str = '刚刚';
	    } elseif ($time < 60 * 60) {
	        $min = floor($time / 60);
	        $str = $min.'分钟前';
	    } elseif ($time < 60 * 60 * 24) {
	        $h = floor($time / (60 * 60));
	        $str = $h.'小时前 '.$htime;
	    } elseif ($time < 60 * 60 * 24 * 3) {
	        $d = floor($time / (60 * 60 * 24));
	        $str = ($d == 1) ? '昨天 '.$rtime : '前天 '.$rtime;
	    } else {
	        $str = $rtime;
	    }
	 
	    return $str;
	}

	public function del_payer($urgent_id,$user_id,$del_by)
	{
		if (!empty($urgent_id) && !empty($user_id) && !empty($del_by)) {

			$is_admin = 'N';
			// if ($user_id == $del_by) {
			// 	$is_admin = 'Y';				
			// }

			$params  = array(
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
				'is_admin' => $is_admin, 
				'deleted_by' =>$del_by
				);

			$this->db->where('id', $urgent_id);
			$this->db->update('prayer_for_urgent', $params);
			return  $this->db->affected_rows();

		}else{
			return false;
		}
	}

	public function send_group_prayer($user_id,$group_prayer_contents,$group_prayer_id)
	{
		if (!empty($user_id) && !empty($group_prayer_id) && !empty($group_prayer_contents) ) {

			$this->db->set('user_id', $user_id);
			$this->db->set('group_prayer_contents', $group_prayer_contents);
			$this->db->set('group_prayer_id',  $group_prayer_id);

			$this->db->set('created_at', mdate('%Y-%m-%d %H:%i:%s', now()));

			$this->db->insert('prayer_for_group');
						
			return $this->db->insert_id();

		}else {
			return false;
		}
	}

	public function get_group_prayer($group_prayer_id,$user_id)
	{
		if (!empty($group_prayer_id)) {
			$data_return  = array(); 
			$is_send_group_prayer = 'N';

			$this->db->select('prayer_for_group.id as prayer_for_group_id ,user_id,nick,group_id,group_prayer_contents,prayer_for_group.created_at');
			$this->db->from('prayer_for_group');
			$this->db->join('user', 'user.id = prayer_for_group.user_id', 'left');
			$this->db->where('group_prayer_id', $group_prayer_id);
			$this->db->where('prayer_for_group.deleted_at is null');
			$this->db->where('user.deleted_at is null');
			$this->db->order_by('prayer_for_group_id', 'desc');
			$data1 =  $this->db->get();
			// echo $this->db->last_query();exit;
			// var_dump($data1->result_array());exit;
			foreach ($data1->result_array() as $row_cer) {

				$created_at = $row_cer['created_at'];
				$row_cer['conversion_time'] = $this->tranTime(strtotime($created_at));				

				$user_id1 = $row_cer['user_id'];
				$HeadSrc  = $this->tq_header_info_model->finduserHeadSrc($user_id1);
				$row_cer['userHeadSrc'] = $HeadSrc->userHead_src;
				// var_dump($row_cer['userHeadSrc']);exit();

				if ($user_id == $user_id1 ) {
					$is_send_group_prayer = 'Y';
				}

				$data_return[] = $row_cer; 
				// $count++;
			}
			// var_dump($is_send);exit;
			return array('data_return' => $data_return,
						'is_send_group_prayer' => $is_send_group_prayer
			 );	
		}else {
			return false;
		}	
	}

	public function del_group_payer($prayer_for_group_id,$user_id,$del_by)
	{
		if (!empty($prayer_for_group_id) && !empty($user_id) && !empty($del_by)) {

			$is_admin = 'N';
			// if ($user_id == $del_by) {
			// 	$is_admin = 'Y';				
			// }

			$params  = array(
				'deleted_by' => $del_by,
				'deleted_at' => mdate('%Y-%m-%d %H:%i:%s', now()) , 
				'is_admin' => $is_admin
				);

			$this->db->where('id', $prayer_for_group_id);
			$this->db->update('prayer_for_group', $params);
			return  $this->db->affected_rows();

		}else{
			return false;
		}
	}

	public function get_all_prayer($limit,$page)
	{
		$data_return1 = array();
		$this->db->select('prayer_for_urgent.id as urgent_id ,prayer_for_urgent.created_at, content_prayer,prayer_for_urgent.user_id,nick');
		$this->db->from('prayer_for_urgent');
		$this->db->join('user', 'user.id = prayer_for_urgent.user_id', 'left');
		$this->db->where('user.deleted_at is  null');
		$this->db->where('prayer_for_urgent.deleted_at is  null');
		$this->db->order_by('urgent_id', 'desc');
		$data1 =  $this->db->get()->result_array();

		foreach ($data1 as $row_cer) {
			$user_id1 = $row_cer['user_id'];
			$HeadSrc  = $this->tq_header_info_model->finduserHeadSrc($user_id1);
			$row_cer['userHeadSrc'] = $HeadSrc->userHead_src;

			$created_at = $row_cer['created_at'];
			$row_cer['conversion_time'] = $this->tranTime(strtotime($created_at));	

        	$data_return1[] = $row_cer; 
		}
		
		$data_return2 = array();
		$this->db->select('prayer_for_group.id as group_prayer_id ,prayer_for_group.created_at, group_prayer_contents,prayer_for_group.user_id,nick');
		$this->db->from('prayer_for_group');
		$this->db->join('user', 'user.id = prayer_for_group.user_id', 'left');
		$this->db->where('prayer_for_group.deleted_at is  null');
		$this->db->where('user.deleted_at is  null');

		$this->db->order_by('group_prayer_id', 'desc');
		$data2 =  $this->db->get()->result_array();

		foreach ($data2 as $row_cer) {
			$user_id1 = $row_cer['user_id'];
			$HeadSrc  = $this->tq_header_info_model->finduserHeadSrc($user_id1);
			$row_cer['userHeadSrc'] = $HeadSrc->userHead_src;

			$created_at = $row_cer['created_at'];
			$row_cer['conversion_time'] = $this->tranTime(strtotime($created_at));	
			
        	$data_return2[] = $row_cer; 
		}

		$data_return3 = array_merge($data_return1,$data_return2);
				
		usort($data_return3, function($a, $b) {
	        $al = $a['created_at'];
	        $bl = $b['created_at'];
	        if ($al == $bl)
	            return 0;
	        return ($al > $bl) ? -1 : 1;
		}); 
		$total = count($data_return3);
		$temp_data  = array();
		$temp_data = $this->page_array($limit,$page,$data_return3,0);
		return  array('temp_data' => $temp_data, 'total' =>$total);

	}

	/**
	 * 数组分页函数  核心函数  array_slice
	 * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中
	 * $count   每页多少条数据
	 * $page   当前第几页
	 * $array   查询出来的所有数组
	 * order 0 - 不变     1- 反序
	 */ 

	public function page_array($count,$page,$array,$order){
	   	global $countpage; #定全局变量
	    $page=(empty($page))?'1':$page; #判断当前页面是否为空 如果为空就表示为第一页面 
	       $start=($page-1)*$count; #计算每次分页的开始位置
	    if($order==1){
	      $array=array_reverse($array);
	    }   
	    $totals=count($array);  
	    if($count != 0 ){	    	
		    $countpage = ceil($totals/$count); #计算总页面数
	    }

	   	$pagedata=array();
		$pagedata=array_slice($array,$start,$count);
	   	return $pagedata;  #返回查询数据
	}
	/**
	 * 分页及显示函数
	 * $countpage 全局变量，照写
	 * $url 当前url
	 */
	public function show_array($countpage,$url){
	     $page=empty($_GET['page'])?1:$_GET['page'];
		 if($page > 1){
		   	$uppage=$page-1;

		 }else{
		 	$uppage=1;
		 }

		 if($page < $countpage){
		   	$nextpage=$page+1;

		 }else{
		    	$nextpage=$countpage;
		 }
		return  array('countpage' => $countpage,'uppage' => $uppage,'nextpage' => $nextpage, 'nextpage' => $nextpage);  	
	 //    $str='<div style="border:1px; width:300px; height:30px; color:#9999CC">';
		// $str.="<span>共  {$countpage}  页 / 第 {$page} 页</span>";
		// $str.="<span><a href='$url?page=1'>   首页  </a></span>";
		// $str.="<span><a href='$url?page={$uppage}'> 上一页  </a></span>";
		// $str.="<span><a href='$url?page={$nextpage}'>下一页  </a></span>";
		// $str.="<span><a href='$url?page={$countpage}'>尾页  </a></span>";
		// $str.='</div>';
		// return $str;
	}
	
}
	
	
	
	
	
	
	
	
	
