<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class My_controller extends CI_Controller
{
	private $output_formats = array(
			'json' 	=> 'application/json'
	    );

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Shanghai');
		
	}

    public function response($data, $status_code = 200)
    {
        foreach($data as $k=>$v)
        {
            switch($k)
            {
               	case 'code':
                    $output_data['code'] = $v;
                break;

                case 'message':
                    $output_data['message'] = $v;
                break;

                case 'total':
                    $output_data['total'] = $v;
                break;

                case 'results':
                    $output_data['results'] = $v;
                break;

                case 'errors':
                    $output_data['errors'] = $v;
                break;

                default:
                    $output_data[$k] = $v;
            }
        }
		
        if (isset($output_data))
        {
            // set http response header
            $this->output->set_status_header($status_code);
            $output = json_encode($output_data);
        }
        else
        {
            // set http response header
            $this->output->set_status_header(500);
            $output = json_encode(array('Internal server error: Try again later.'));
        }
		
		// set output content type
		$this->output->set_header('Content-Type: '.$this->output_formats['json'].'; charset=utf-8');        
		
		// log the request
		// $this->logger($output);
		
		// send output
		$this->output->set_output($output);
    }

	public function get($key)
	{
		return $this->input->get($key, TRUE);
	}
	
	/**
	 *  A wrapper for CI POST input
	 */
	public function post($key)
	{
		return $this->input->post($key, TRUE);
	}    
    
    //这个星期的星期一  
    // @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
    // @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
    public function this_monday($timestamp=0,$is_return_timestamp=true){  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if(!isset($cache[$id])){  
            if(!$timestamp) $timestamp = time();  
            $monday_date = date('Y-m-d', $timestamp-86400*date('w',$timestamp)+(date('w',$timestamp)>0?86400:-/*6*86400*/518400));  
            if($is_return_timestamp){  
                $cache[$id] = strtotime($monday_date);  
            }else{  
                $cache[$id] = $monday_date;  
            }  
        }  
        return $cache[$id];  
      
    }  
      
    //这个星期的星期天  
    // @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
    // @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
    public function this_sunday($timestamp=0,$is_return_timestamp=true){  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if(!isset($cache[$id])){  
            if(!$timestamp) $timestamp = time();  
            $sunday = $this->this_monday($timestamp) + /*6*86400*/518400;  
            if($is_return_timestamp){  
                $cache[$id] = $sunday;  
            }else{  
                $cache[$id] = date('Y-m-d',$sunday);  
            }  
        }  
        return $cache[$id];  
    }  
      
    //上周一  
    // @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
    // @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
    public function last_monday($timestamp=0,$is_return_timestamp=true){  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if(!isset($cache[$id])){  
            if(!$timestamp) $timestamp = time();  
            $thismonday = $this->this_monday($timestamp) - /*7*86400*/604800;  
            if($is_return_timestamp){  
                $cache[$id] = $thismonday;  
            }else{  
                $cache[$id] = date('Y-m-d',$thismonday);  
            }  
        }  
        return $cache[$id];  
    }  
      
    //上个星期天  
    // @$timestamp ，某个星期的某一个时间戳，默认为当前时间  
    // @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
    public function last_sunday($timestamp=0,$is_return_timestamp=true){  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if(!isset($cache[$id])){  
            if(!$timestamp) $timestamp = time();  
            $thissunday = $this->this_sunday($timestamp) - /*7*86400*/604800;  
            if($is_return_timestamp){  
                $cache[$id] = $thissunday;  
            }else{  
                $cache[$id] = date('Y-m-d',$thissunday);  
            }  
        }  
        return $cache[$id];  
      
    }  
      
    //这个月的第一天  
    // @$timestamp ，某个月的某一个时间戳，默认为当前时间  
    // @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
      
    public function month_firstday($timestamp = 0, $is_return_timestamp=true){  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if(!isset($cache[$id])){  
            if(!$timestamp) $timestamp = time();  
            $firstday = date('Y-m-d', mktime(0,0,0,date('m',$timestamp),1,date('Y',$timestamp)));  
            if($is_return_timestamp){  
                $cache[$id] = strtotime($firstday);  
            }else{  
                $cache[$id] = $firstday;  
            }  
        }  
        return $cache[$id];  
    }  
      
    //这个月的最后一天  
    // @$timestamp ，某个月的某一个时间戳，默认为当前时间  
    // @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
      
    public function month_lastday($timestamp = 0, $is_return_timestamp=true){  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if(!isset($cache[$id])){  
            if(!$timestamp) $timestamp = time();  
            $lastday = date('Y-m-d', mktime(0,0,0,date('m',$timestamp),date('t',$timestamp),date('Y',$timestamp)));  
            if($is_return_timestamp){  
                $cache[$id] = strtotime($lastday);  
            }else{  
                $cache[$id] = $lastday;  
            }  
        }  
        return $cache[$id];  
    }  
      
    //上个月的第一天  
    // @$timestamp ，某个月的某一个时间戳，默认为当前时间  
    // @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
      
    public function lastmonth_firstday($timestamp = 0, $is_return_timestamp=true){  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if(!isset($cache[$id])){  
            if(!$timestamp) $timestamp = time();  
            $firstday = date('Y-m-d', mktime(0,0,0,date('m',$timestamp)-1,1,date('Y',$timestamp)));  
            if($is_return_timestamp){  
                $cache[$id] = strtotime($firstday);  
            }else{  
                $cache[$id] = $firstday;  
            }  
        }  
        return $cache[$id];  
    }  
      
    //上个月的最后一天  
    // @$timestamp ，某个月的某一个时间戳，默认为当前时间  
    // @is_return_timestamp ,是否返回时间戳，否则返回时间格式  
      
    public function lastmonth_lastday($timestamp = 0, $is_return_timestamp=true){  
        static $cache ;  
        $id = $timestamp.$is_return_timestamp;  
        if(!isset($cache[$id])){  
            if(!$timestamp) $timestamp = time();  
            $lastday = date('Y-m-d', mktime(0,0,0,date('m',$timestamp)-1, date('t',$this->lastmonth_firstday($timestamp)),date('Y',$timestamp)));  
            if($is_return_timestamp){  
                $cache[$id] = strtotime($lastday);  
            }else{  
                $cache[$id] =  $lastday;  
            }  
        }  
        return $cache[$id];  
    }

    // echo '本周星期一：'.$this->this_monday(0,false).'';  
    // echo '本周星期天：'.$this->this_sunday(0,false).'';  
    // echo '上周星期一：'.$this->last_monday(0,false).'';  
    // echo '上周星期天：'.$this->last_sunday(0,false).'';  
    // echo '本月第一天：'.$this->month_firstday(0,false).'';  
    // echo '本月最后一天：'.$this->month_lastday(0,false).'';  
    // echo '上月第一天：'.$this->lastmonth_firstday(0,false).'';  
    // echo '上月最后一天：'.$this->lastmonth_lastday(0,false).'';      
	
}
