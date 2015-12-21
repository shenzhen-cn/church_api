<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    	
    function __construct()
    {
        parent::__construct();
        
		
	}
	
	public function get_week_days($day){ 

	    if (!empty($day)) {

	     	$week_lastday  = date('Y-m-d',strtotime("$day Sunday")); 
	     	$week_firstday = date('Y-m-d',strtotime("$week_lastday -6 days")); 

	     	return array('week_firstday' => $week_firstday,'week_lastday' => $week_lastday);
	     } 
	} 

	public function get_month_days($day)
	{ 
		if (!empty($day)) {

			$month_firstday = date('Y-m-01',strtotime($day)); 
			$month_lastday = date('Y-m-d',strtotime("$month_firstday +1 month -1 day")); 

			return array('month_firstday' => $month_firstday,'month_lastday' => $month_lastday); 
		}
	}

	//时间转换为刚刚、XX分钟前
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

	
	
	//比较两个日期之间差几天
	public function diffBetweenTwoDays ($day1, $day2)
	{
		  $second1 = strtotime($day1);
		  $second2 = strtotime($day2);
	    
		  if ($second1 < $second2) {
		    $tmp = $second2;
		    $second2 = $second1;
		    $second1 = $tmp;
		  }
		  return ($second1 - $second2) / 86400;
	}

	public function get_user_all_info($user_id)
	{
		$this->db->select('user.id as user_id ,email ,sex, nick,group_id,group_name,user.created_at as user_created_at ');
		$this->db->from('user');
		$this->db->join('group', 'group.id = user.group_id', 'left');
		$this->db->where('user.id', $user_id);
		$this->db->where('user.deleted_at is null');
		$this->db->where('group.deleted_at is null');
		$arr1 =  $this->db->get()->first_row();
		$arr2 = $this->finduserHeadSrc($user_id);
		if(!empty($arr1)) {			
			return  array(
					'user_id' => $arr1->user_id,
				    'sex' => $arr1->sex,
					'email' => $arr1->email,
					'nick' => $arr1->nick,
					'userHead_src' => isset($arr2->userHead_src) ? $arr2->userHead_src : "",
					'group_id' => $arr1->group_id,
					'group_name' => $arr1->group_name,
					'user_created_at' => $arr1->user_created_at,				
			);
		}else{
			return null;
		}	


	}

	public function finduserHeadSrc($user_id)
	{
		$data_return  = array();
		$this->db->select('max(update_at) as update_at');
		$this->db->from('church.userhead_src');
		$this->db->where('user_id' ,$user_id);
		$this->db->where('deleted_at is null');
		$update_at = $this->db->get()->first_row();
		if (isset($update_at) && ! empty($update_at) && ! empty($user_id)) {
			
			$this->db->select('id,userHead_src');
			$this->db->from('church.userhead_src');
			$this->db->where('user_id' ,$user_id);
			$this->db->where('update_at' ,$update_at->update_at);
			$this->db->where('deleted_at is null');
			$data_return = $this->db->get()->first_row();
			return $data_return;
		}else{
			return false;
		}
		
	}

	//两个数相除得百分比
	public function get_percentage($val1,$val2,$decimal = 2)
	{
	    if ($val2==0) {
	        return "0%";
	    }

	    return round($val1 / $val2 * 100 , $decimal) . "%";
	}

	//管理员信息
	public function get_admin_all_info($admin_id)
	{	
		$data_return = array();
		$this->db->select('id,admin_name,nick,gender,level');
		$this->db->from('church.admin');
		$this->db->where('id' , $admin_id);
		$this->db->where('deleted_at is NULL');
		$data1  =  $this->db->get()->first_row();

		$data2 = $this->adminHead_src($admin_id);

		$data_return = array(
			'admin_id'=>$data1->id,
			'admin_name'=>$data1->admin_name,
			'admin_nick'=>$data1->nick,
			'admin_gender'=>$data1->gender,
			'admin_level'=>$data1->level,
			'adminHead_src'=>isset($data2->adminHead_src) ? $data2->adminHead_src:"" ,
			);
		return $data_return;
		
	}

	public function adminHead_src($admin_id)
	{

		$data_return  = array();

		$this->db->select('max(update_at) as update_at');
		$this->db->from('church.adminhead_src');
		$this->db->where('admin_id' ,$admin_id);
		$this->db->where('deleted_at is null');
		$update_at = $this->db->get()->first_row();
//		var_dump($update_at);exit;
		if(!empty($update_at)){
			if (isset($update_at) && ! empty($update_at) && ! empty($admin_id)) {

				$this->db->select('id,adminHead_src');
				$this->db->from('church.adminhead_src');
				$this->db->where('admin_id' ,$admin_id);
				$this->db->where('update_at' ,$update_at->update_at);
				$this->db->where('deleted_at is null');
				$data_return = $this->db->get()->first_row();

			}
		}

		return $data_return;

	}

	public function admin_login_log($admin_id)
	{
		if (!empty($admin_id)) {
			$this->db->select('max(login_at) as last_login_at');	
			$this->db->from('admin_login_log');
			$this->db->where('admin_id', $admin_id);
			$this->db->where('deleted_at is null');
			return $this->db->get()->first_row();
		}
	}
}
