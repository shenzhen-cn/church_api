<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('get_pagination'))
{
	function get_pagination($total_results, $limit, $page)
	{
	
		// Check for valid and limit
		$page = $page < 1 ? 1 : $page;
		$limit = $limit < 1 ? 1 : $limit;
		
		$total_pages = ceil($total_results/$limit);
		$page = $page > $total_pages ? $total_pages : $page;
		$offset = (($page * $limit) - $limit);
		
		return array(
			'limit'	=> $limit,
			'offset' => $offset < 1 ? 0 : $offset,
			'page'	=> $page
		);
	}
}

if ( ! function_exists('is_valid_date'))
{
	function is_valid_date($date)
	{
		// yyyy-mm-dd 
		if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches))
		{
			if (checkdate($matches[2], $matches[3], $matches[1])) return TRUE;
		}
	}
}

if ( ! function_exists('doCurl'))
{
	function doCurl($url, $param = NULL, $method = NULL)
	{
		log_message('debug', 'SNB-curl: Call to '.$url.($method == 'POST' ? ' with '.preg_replace('/\n/', '',print_r($param, TRUE)) : '')); 

		$process= curl_init($url);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		if($method == 'POST')
		{
			curl_setopt($process, CURLOPT_POST, TRUE);
			curl_setopt($process, CURLOPT_POSTFIELDS, $param);
			curl_setopt($process, CURLOPT_CUSTOMREQUEST, $method); 
		}
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		$content = array();
		$content['output'] = curl_exec($process);
		$content['http_status_code'] = curl_getinfo($process, CURLINFO_HTTP_CODE);
		
		return $content;
	}
}


if (! function_exists("smtp_mail"))
 {
	require("application/libraries/PHPMailer-master/PHPMailer.php"); 
	require("application/libraries/PHPMailer-master/SMTP.php");
	// $this->load->library('PHPMailer-master/PHPMailer');
	// $this->load->library('PHPMailer-master/SMTP');

	function smtp_mail( $sendto_email, $subject, $body, $id,$token, $user_name,$active,$isadmin=''){    
		// var_dump($active);exit;
	    $mail = new PHPMailer();    
	    $mail->IsSMTP();                  // send via SMTP   
	    // $mail->SMTPDebug=true;//这个参数可以打开debug,看看发送的时候有什么错误,然后查找错误根源
	    $mail->Host = "smtp.qq.com";   // SMTP servers    
	    $mail->Port = '587';
	    $mail->SMTPAuth = true;           // turn on SMTP authentication    
	    $mail->Username = "3302376521@qq.com";     // SMTP username  注意：普通邮件认证不需要加 @域名    
	    $mail->Password = "tq1234"; // SMTP password    
	    $mail->From = "3302376521@qq.com";   //发件人邮箱   
	    // 发件人邮箱    
	    $mail->FromName =  "使命青年团契网";  // 发件人    

	    $mail->CharSet = "UTF-8";   // 这里指定字符集！    
	    $mail->Encoding = "base64";    
	    $mail->AddAddress($sendto_email);  // 收件人邮箱和姓名    
	    $mail->IsHTML(true);  // send as HTML    
	    // 邮件主题    
	    $mail->Subject = $subject.date('Y-m-d H:i:s');
	    if(!empty($isadmin) && $isadmin == 'Y'){
			$mail_url = "<a href='http://192.168.1.123/church_dev/tq_admin/resetPwd?op=$active&id=$id&token=$token'>"." http://192.168.1.123/church_dev/tq_admin/resetPwd?op=$active&id=$id&token=$token</a>";	    	
	    }else{
			$mail_url = "<a href='http://192.168.1.123/church_dev/tq_user/register?op=$active&id=$id&token=$token'>"." http://192.168.1.123/church_dev/tq_user/register?op=$active&id=$id&token=$token</a>";	    	
	    }

	    $str1 = null;
	    if($active == 'active'){	    			
			$str1 = "绑定邮箱账号";
	    }else{
	    	$str1 = "重置密码的";
	    }

	    // 邮件内容    
	    $mail->Body = "
	    <html><head>   
	    <meta http-equiv='Content-Languag' content='zh-cn'>   
	    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>   
	    </head>   
	    <body>  
				".$sendto_email.":<br>		
	    		您好，我们收到了您的".$str1."申请，现在请你确认。<br>
	    		======================================<br>
	    		如果是您申请绑定此邮箱账号，请点击以下链接登录，否则不要点击:<br>
	    		注意：此链接24小时内生效，否则，重新注册，链接只能使用一次，请尽快激活:<br>
	    		".$mail_url."<br>
	    		如果无法跳转，请把上面网页地址复制到浏览器地址栏中打开<br/>
				我们欢迎您，愿上帝与你同在，阿们！<br/>
				本邮件是系统自动发出，请勿回复。<br/>
				感谢你的使用。<br>
				使命青年团契网!<br>
	    </body>   
	    </html>
	    ";   // optional, comment out and test                    


	    $mail->AltBody ="text/html";    

	    if(!$mail->Send())    
	    {    
	        $msg =  "邮件发送有误 <br> ";    

	        $msg = $msg. "邮件错误信息: " . $mail->ErrorInfo;    
	        exit;    
	    }    
	    else {    
	        $msg = "$user_name 邮件发送成功!<br />";    
	    }  

	    return $msg;  
	}

	// 参数说明(发送到, 邮件主题, 邮件内容, 附加信息, 用户名)    
	// smtp_mail("sundeamdoit@163.com", "欢迎使用乐嗑网！", "NULL", "凡客");  exit(); 

}
