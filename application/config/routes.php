<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//GET
// bibile
$route['bibile/find_volumeName'] 											= 'bibile/bibile/find_volumeName';
$route['bibile/look_volume'] 												= 'bibile/bibile/look_volume';
$route['bibile/onlineBibile'] 												= 'bibile/bibile/onlineBibile';
$route['bibile/get_bibile_book_id_by_testament'] 							= 'bibile/bibile/get_bibile_book_id_by_testament';
$route['bibile/get_bible_section_by_book_id'] 							    = 'bibile/bibile/get_bible_section_by_book_id';


$route['register/findReUserName'] 											= 'user/register/findReUserName';
$route['tq_header_info/find'] 												= 'tq_header_info/find';
$route['tq_header_info/get_tip_messages'] 									= 'tq_header_info/get_tip_messages';
$route['alert_messages/remove_alert_by_id'] 						        = 'messages/user_alert_messages/remove_alert_by_id';
$route['tq_header_info/remove_alert_by_user_id'] 						    = 'tq_header_info/remove_alert_by_user_id';


$route['personal/user_registered'] 											= 'user/register/user_registered';
$route['group/get_group'] 													= 'group/group/get_group';
$route['group/del_group'] 													= 'group/group/del_group';
$route['group/del_users_by_id'] 											= 'user/user/del_users_by_id';

$route['group/find_user_by_group_id'] 										= 'group/group/find_user_by_group_id';
$route['group/find_group_by_group_id'] 										= 'group/group/find_group_by_group_id';
$route['group/find_all_users_by_group_id']  								= 'group/group/find_all_users_by_group_id';
$route['group/check_spirituality_or_prayer'] 								= 'group/group/check_spirituality_or_prayer';
$route['group/del_user_spirituality'] 										= 'group/group/del_user_spirituality';
$route['group/check_nextday'] 												= 'group/group/check_nextday';
$route['group/find_week_s_report'] 											= 'group/group/find_week_s_report';
$route['group/see_member'] 											        = 'group/group/see_member';
$route['group/get_notice_groups_results'] 									= 'group/group/get_notice_groups_results';
$route['group/get_rate_of_spirituality'] 									= 'group/group/get_rate_of_spirituality';
$route['group/delete_spirituality'] 									    = 'group/group/delete_spirituality';
$route['group/del_comments_by_comment_id'] 								    = 'group/group/del_comments_by_comment_id';
$route['group/get_users_by_group_id'] 								        = 'group/group/get_users_by_group_id';
$route['group/del_prayer'] 								                    = 'group/group/del_prayer';


//user
$route['home/find_home_inform'] 										    = 'admin/admin_setting/find_home_inform';
$route['home/find_urgent_prayer'] 										    = 'admin/admin_setting/find_urgent_prayer';
$route['home/find_spirituality'] 										    = 'admin/admin_setting/find_spirituality';
$route['home/find_todayScriptures'] 									    = 'admin/admin_setting/find_todayScriptures';

$route['home/send_spirituality'] 											= 'user/user/send_spirituality';
$route['home/find_user_spirituality'] 										= 'user/user/find_user_spirituality';
$route['home/reminder_spirituality_by_id'] 									= 'user/user/reminder_spirituality_by_id';
$route['home/add_praise'] 									                = 'user/user/add_praise';

$route['home/recently_fellowship_photos'] 									= 'fellowship_life/fellowship_life/recently_fellowship_photos';

//priest_preach
$route['priest_preach/find_class_name_priest_preach'] 						= 'priest_preach/priest_preach/find_class_name_priest_preach';
$route['priest_preach/get_priest_preach_by_id'] 						    = 'priest_preach/priest_preach/get_priest_preach_by_id';
$route['priest_preach/pp_read_by_id'] 						                = 'priest_preach/priest_preach/pp_read_by_id';
$route['priest_preach/del_course'] 						                    = 'priest_preach/priest_preach/del_course';
$route['priest_preach/read_myEdit_by_id'] 						            = 'priest_preach/priest_preach/read_myEdit_by_id';
$route['priest_preach/del_document'] 						                = 'priest_preach/priest_preach/del_document';

//fellowship_life

$route['fellowship_life/get_user_album_name'] 						        = 'fellowship_life/fellowship_life/get_user_album_name';
$route['fellowship_life/group_albums'] 						                = 'fellowship_life/fellowship_life/group_albums';
$route['fellowship_life/see_user_albums'] 						            = 'fellowship_life/fellowship_life/see_user_albums';
$route['fellowship_life/see_user_photos'] 						            = 'fellowship_life/fellowship_life/see_user_photos';
$route['fellowship_life/get_photos_count'] 						            = 'fellowship_life/fellowship_life/get_photos_count';
$route['fellowship_life/del_photos'] 						                = 'fellowship_life/fellowship_life/del_photos';
$route['fellowship_life/get_today_user_photos'] 						    = 'fellowship_life/fellowship_life/get_today_user_photos';

//wallOfPrayer
$route['wallOfPrayer/get_tq_content_prayer'] 						        = 'prayer/prayer/get_tq_content_prayer';
$route['wallOfPrayer/del_payer'] 						                    = 'prayer/prayer/del_payer';
$route['wallOfPrayer/get_group_prayer'] 						            = 'prayer/prayer/get_group_prayer';
$route['wallOfPrayer/del_group_payer'] 						                = 'prayer/prayer/del_group_payer';
$route['wallOfPrayer/get_all_prayer'] 						                = 'prayer/prayer/get_all_prayer';
//calendar
$route['calendar/get_all_events_for_json'] 						            = 'user/user/get_all_events_for_json';
$route['personal/get_personal_data_for_spirituality'] 					    = 'user/user/get_personal_data_for_spirituality';

//alert_messages
$route['alert_messages/del_alert_comments_by_spirituality_id']              = 'messages/user_alert_messages/del_alert_comments_by_spirituality_id';
$route['alert_messages/del_all_praise_alert']                               = 'messages/user_alert_messages/del_all_praise_alert';
$route['alert_messages/del_prompt_alerts'] 						            = 'messages/user_alert_messages/del_prompt_alerts';
$route['personal/get_informations'] 					                    = 'user/user/get_informations';
$route['personal/upload_headSrc'] 					                        = 'user/user/upload_headSrc';
$route['personal/modify_user_data'] 					                    = 'user/user/modify_user_data';
$route['personal/get_honor_list'] 					                        = 'user/user/get_honor_list';



//POST
//register

$route['register/register'] 												= 'user/register/register';
$route['register/resetpassword'] 											= 'user/register/resetpassword';
$route['register/resetpwd_for_forgetpwd'] 									= 'user/register/resetpwd_for_forgetpwd';
$route['resetpassword/checkCurrentPwd'] 									= 'user/register/checkCurrentPwd';



$route['user/addPersonal'] 													= 'user/register/addPersonal';
$route['register/improveInformation'] 										= 'user/register/improveInformation';

//group
$route['group/addGroup'] 													= 'group/group/addGroup';

$route['group/groupEdit'] 													= 'group/group/groupEdit';
$route['group/spirituality'] 										        = 'group/group/spirituality';
$route['group/setting_spirituality'] 										= 'group/group/setting_spirituality';
$route['group/setting_group_prayer'] 										= 'group/group/setting_group_prayer';
$route['group/get_today_group_prayer'] 										= 'group/group/get_today_group_prayer';
$route['group/send_comments'] 										        = 'group/group/send_comments';
$route['group/send_reply'] 										            = 'group/group/send_reply';


$route['login/login_email/find'] 											= 'user/login/find';

$route['personal/upload_photo'] 											= 'user/register/improveInformation';


//admin

$route['adminLogin/checkLogin'] 											= 'admin/login/checkLogin';
$route['tq_admin_header_info/find'] 										= 'tq_header_info/find_tq_admin_header_info';
$route['register/reset_pwd_for_forget'] 									= 'admin/admin_setting/reset_pwd_for_forget';
$route['admin_setting/reset_admin_pwd'] 									= 'admin/admin_setting/reset_admin_pwd';


$route['personal/upload_admin_photo'] 										= 'admin/login/improveInformation';
$route['homeSetting/home_inform'] 										    = 'admin/admin_setting/home_inform';
$route['homeSetting/urgentPrayer'] 										    = 'admin/admin_setting/urgentPrayer';
$route['homeSetting/search_bibile'] 										= 'admin/admin_setting/search_bibile';
$route['homeSetting/setting_todayScriptures'] 								= 'admin/admin_setting/setting_todayScriptures';
$route['homeSetting/notice_groups'] 								        = 'admin/admin_setting/notice_groups';


$route['resetpassword/checkCurrentadminPwd'] 								= 'admin/admin_setting/checkCurrentadminPwd';
$route['register/alteradminPassword'] 								        = 'admin/admin_setting/alteradminPassword';


// priest_preach
$route['priest_preach/add_course_class'] 									= 'priest_preach/priest_preach/add_course_class';
$route['priest_preach/getContent'] 									        = 'priest_preach/priest_preach/getContent';
$route['priest_preach/getmyEditor'] 									    = 'priest_preach/priest_preach/getmyEditor';

//fellowship_life
$route['fellowship_life/create_album'] 									    = 'fellowship_life/fellowship_life/create_album';
$route['fellowship_life/save_data'] 									    = 'fellowship_life/fellowship_life/save_data';
$route['fellowship_life/rename_album_name'] 								= 'fellowship_life/fellowship_life/rename_album_name';
// wallOfPrayer
$route['wallOfPrayer/send_prayer'] 						                    = 'prayer/prayer/send_prayer';
$route['wallOfPrayer/send_group_prayer'] 									= 'prayer/prayer/send_group_prayer';







