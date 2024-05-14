<?php

function home_url($optionalparam) {
	global $CFG;
	return $CFG->wwwroot . $optionalparam;
}

function wp_logout_url() {
    global $CFG;
    return $CFG->wwwroot . '/login/logout.php';
}

// Main fucntion to set user session in the core
function wp_set_current_user($tokenObj) {
    global $WPUSER;
    plus_startMoodleSession($tokenObj);
    plus_getUsermeta($tokenObj);
    $newUserObj = new stdClass();
    $newUserObj->data = $WPUSER->data;
	$newUserObj->token = $tokenObj->token; 
	$newUserObj->privatetoken = $tokenObj->privatetoken;
	$newUserObj->lang = "FR";
	$newUserObj->user_status = 0;
    $newUserObj->ID = $WPUSER->data->ID;
    $newUserObj->display_name = $WPUSER->data->display_name;
    
    $capabilities = [];
    if ($WPUSER->data->metadata->accounttype == 'schooladmin') {
        $capabilities = ['manage_options','plus_viewdashboardkpi','plus_viewsubscriptionkpi','plus_editevents','plus_editownevents','view_plusaddglobaluser','view_plusaddsurvey','plus_viewcalendar','plus_viewteachersevent','plus_calendarmyevent','view_plusresources','view_plusclaims','manage_plususers','view_plusclaimedevent','view_plusinprogressedevent','view_pluscencllededevent','plus_eventotherviewcompletions','plus_eventothereditdate','plus_eventothercancel','plus_eventotherinprogress','plus_eventothercomplete','view_plusmanageevents','view_pluseditevent','view_plusglobalusers',
        'view_pluseditglobaluserteacher','plus_viewusersubscription','plus_editstudents','plus_generatemonthlyreport','plus_addgroups','plus_viewtobeapproved','plus_viewgroupdetails','plus_generategrouplink','plus_generategroupcode','plus_notification_viewallcolumns','plus_notification_eventview','view_plussurveys','view_pluseditsurvey','plus_addteacher','view_plusdevicelist','plus_cansubmitsurvey','manage_plushomework','plus_viewgroups','manage_plusgroups','override_plussubscription','manage_plusscorecard','manage_plusstudentscorecard','manage_plusstudentprofile',
        'manage_plusclassprofile','manage_plusclassprofilecompetency','manage_plusteacher','plus_viewteachers','manage_plustransections','view_plustraining'
        ];  
    } elseif ($WPUSER->data->metadata->accounttype == 'internaladmin') {
        $capabilities = ['manage_options','plus_viewdashboardkpi','plus_viewsubscriptionkpi','plus_editevents','plus_editownevents','view_plusaddglobaluser','view_plusaddsurvey','plus_viewcalendar','plus_viewteachersevent','plus_calendarmyevent','view_plusresources','view_plusclaims','manage_plususers','view_plusclaimedevent','view_plusinprogressedevent','view_pluscencllededevent','plus_eventotherviewcompletions','plus_eventothereditdate','plus_eventothercancel','plus_eventotherinprogress','plus_eventothercomplete','view_plusmanageevents','view_pluseditevent','view_plusglobalusers',
        'view_pluseditglobaluserteacher','plus_viewusersubscription','plus_editstudents','plus_generatemonthlyreport','plus_addgroups','plus_viewtobeapproved','plus_viewgroupdetails','plus_generategrouplink','plus_generategroupcode','plus_notification_viewallcolumns','plus_notification_eventview','view_plussurveys','view_pluseditsurvey','plus_addteacher','view_plusdevicelist','plus_cansubmitsurvey','manage_plushomework','plus_viewgroups','manage_plusgroups','override_plussubscription','manage_plusscorecard','manage_plusstudentscorecard','manage_plusstudentprofile',
        'manage_plusclassprofile','manage_plusclassprofilecompetency','manage_plusteacher','plus_viewteachers','manage_plustransections','view_plustraining'
        ];
    } elseif ($WPUSER->data->metadata->accounttype == 'consultant') {
        $capabilities = ['manage_options','plus_viewdashboardkpi','plus_viewsubscriptionkpi','plus_editevents','plus_editownevents','view_plusaddglobaluser','view_plusaddsurvey','plus_viewcalendar','plus_viewteachersevent','plus_calendarmyevent','view_plusresources','view_plusclaims','manage_plususers','view_plusclaimedevent','view_plusinprogressedevent','view_pluscencllededevent','plus_eventotherviewcompletions','plus_eventothereditdate','plus_eventothercancel','plus_eventotherinprogress','plus_eventothercomplete','view_plusmanageevents','view_pluseditevent','view_plusglobalusers',
        'view_pluseditglobaluserteacher','plus_viewusersubscription','plus_editstudents','plus_generatemonthlyreport','plus_addgroups','plus_viewtobeapproved','plus_viewgroupdetails','plus_generategrouplink','plus_generategroupcode','plus_notification_viewallcolumns','plus_notification_eventview','view_plussurveys','view_pluseditsurvey','plus_addteacher','view_plusdevicelist','plus_cansubmitsurvey','manage_plushomework','plus_viewgroups','manage_plusgroups','override_plussubscription','manage_plusscorecard','manage_plusstudentscorecard','manage_plusstudentprofile',
        'manage_plusclassprofile','manage_plusclassprofilecompetency','manage_plusteacher','plus_viewteachers','manage_plustransections','view_plustraining'
        ];
    } else {
        $capabilities = [];
    }

    // $capabilities = ['manage_options','plus_viewdashboardkpi','plus_viewsubscriptionkpi','plus_editevents','plus_editownevents','view_plusaddglobaluser','view_plusaddsurvey','plus_viewcalendar','plus_viewteachersevent','plus_calendarmyevent','view_plusresources','view_plusclaims','manage_plususers','view_plusclaimedevent','view_plusinprogressedevent','view_pluscencllededevent','plus_eventotherviewcompletions','plus_eventothereditdate','plus_eventothercancel','plus_eventotherinprogress','plus_eventothercomplete','view_plusmanageevents','view_pluseditevent','view_plusglobalusers',
    // 'view_pluseditglobaluserteacher','plus_viewusersubscription','plus_editstudents','plus_generatemonthlyreport','plus_addgroups','plus_viewtobeapproved','plus_viewgroupdetails','plus_generategrouplink','plus_generategroupcode','plus_notification_viewallcolumns','plus_notification_eventview','view_plussurveys','view_pluseditsurvey','plus_addteacher','view_plusdevicelist','plus_cansubmitsurvey','manage_plushomework','plus_viewgroups','manage_plusgroups','override_plussubscription','manage_plusscorecard','manage_plusstudentscorecard','manage_plusstudentprofile',
    // 'manage_plusclassprofile','manage_plusclassprofilecompetency','manage_plusteacher','plus_viewteachers','manage_plustransections','view_plustraining'
    // ];

    $newUserObj->capabilities = $capabilities;
    $_SESSION['CURRENTUSERSESSION'] = $newUserObj;
    
    return isset($WPUSER)?true:false;
}


function wp_get_current_user() {
    global $CURRENTUSERSESSION;
    if(isset($_SESSION['CURRENTUSERSESSION'])){
        $CURRENTUSERSESSION = $_SESSION['CURRENTUSERSESSION'];
        return $CURRENTUSERSESSION;
    }
}


function wp_get_moodle_session() {
    global $MOODLESESSION;
    $MOODLESESSION = $_SESSION['MOODLESESSION'];
    return $MOODLESESSION;
}


function get_user_meta(int $user_id, string $key = "", bool $single = false) {
    if (!isset($_SESSION['CURRENTUSERSESSION'])) {
        return $single ? null : [];
    }
    $userObj = $_SESSION['CURRENTUSERSESSION'];
    if ($userObj->ID != $user_id) {
        return $single ? null : [];
    }
    switch ($key) {
        case 'token':
            return $single ? $userObj->token : [$userObj->token];
        case 'privatetoken':
            return $single ? $userObj->privatetoken : [$userObj->privatetoken];
        case 'currentinstitution':
            return $single ? $userObj->data->metadata->currentinstitution : [$userObj->data->metadata->currentinstitution];
        case 'INSTITUTION':
            return $single ? $userObj->data->metadata->institution : [$userObj->data->metadata->institution];
        case 'display_name':
            return $single ? $userObj->display_name : [$userObj->display_name];
        case 'nickname':
            return $single ? $userObj->nickname : [$userObj->nickname];
        case 'first_name':
            return $single ? $userObj->data->metadata->first_name : [$userObj->data->metadata->first_name];
        case 'last_name':
            return $single ? $userObj->data->metadata->last_name : [$userObj->data->metadata->last_name];
        case 'description':
            return $single ? $userObj->description : [$userObj->description];
        case 'user_login':
            return $single ? $userObj->user_login : [$userObj->user_login];
        case 'user_pass':
            return $single ? $userObj->user_pass : [$userObj->user_pass];
        case 'user_nicename':
            return $single ? $userObj->user_nicename : [$userObj->user_nicename];
        case 'lang':
            return $single ? $userObj->lang : [$userObj->lang];
            break;
        default:
            return $single ? null : [];
    }
}

function update_user_meta(int $user_id, string $meta_key, mixed $meta_value, mixed $prev_value = "") {
    if (!isset($_SESSION['CURRENTUSERSESSION'])) {
        return false;
    }
    $newUserObj = $_SESSION['CURRENTUSERSESSION']; 
    if ($newUserObj->ID != $user_id) {
        return false;
    }
    switch($meta_key) {
        case 'token':
            $newUserObj->token = $meta_value;
            break;
        case 'privatetoken':
            $newUserObj->privatetoken = $meta_value;
            break;
        case 'currentinstitution':
            $newUserObj->data->metadata->currentinstitution = $meta_value;
            break;
        case 'INSTITUTION':
            $newUserObj->data->metadata->institution = $meta_value;
            break;
        case 'display_name':
            $newUserObj->display_name = $meta_value;
            break;
        case 'nickname':
            $newUserObj->nickname = $meta_value;
            break;
        case 'first_name':
            $newUserObj->data->metadata->first_name = $meta_value;
            break;
        case 'last_name':
            $newUserObj->data->metadata->last_name = $meta_value;
            break;
        case 'description':
            $newUserObj->description = $meta_value;
            break;
        case 'user_login':
            $newUserObj->user_login = $meta_value;
            break;
        case 'user_pass':
            $newUserObj->user_pass = $meta_value;
            break;
        case 'user_nicename':
            $newUserObj->user_nicename = $meta_value;
            break;
        case 'user_url':
            $newUserObj->user_url = $meta_value;
            break;
        case 'user_registered':
            $newUserObj->user_registered = $meta_value;
            break;
        case 'user_activation_key':
            $newUserObj->user_activation_key = $meta_value;
            break;
        case 'user_status':
            $newUserObj->user_status = $meta_value;
            break;
        case 'roles':
            $newUserObj->roles = $meta_value;
            break;
        case 'allcaps':
            $newUserObj->allcaps = $meta_value;
            break;
        case 'filter':
            $newUserObj->filter = $meta_value;
            break;
        case 'lang':
            $newUserObj->lang = $meta_value;
            break;
        default:
            return false;
    }
    $_SESSION['CURRENTUSERSESSION'] = $newUserObj;
    return true;
}


function authenticate_user_login($args) {
    global $CFG;
    $logintype = "normal";     
    $username = $args['username'];     
    $password = $args['password'];     
    if($logintype == "normal") {
        if(empty($username) || empty($password)) {
            return "Username and password is reqired";
            die;
        } else {
            $postDATA = [
                'username' => $args['username'],
                'password' => $args['password'],
                'service'   => 'moodle_mobile_app',
            ];
            $ch = curl_init($CFG->apiroot."/login/token.php");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postDATA);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $gettoken = curl_exec($ch);
            if($gettoken = json_decode($gettoken)) {
                if($gettoken->token){
                   if(wp_set_current_user($gettoken)){
                       redirect("{$CFG->wwwroot}/dashboard/");
                       die;
                   } else {
				    redirect("{$CFG->wwwroot}/login/");
                    die;
                   }
                } else {
                   redirect("{$CFG->wwwroot}/login/");
                   die;
                }
            } else {
				redirect("{$CFG->wwwroot}/login/");
                die;
            }
        }
    } 
}

function current_user_can($string) {
    $current_user = wp_get_current_user();
    $capabilities = $current_user->capabilities;
    if(isset($capabilities)){
        return in_array($string, $capabilities);
    }     
}

function plus_get_qsparameter($allparams) {
	$qsarray = array();
	if(is_object($allparams)){$allparams = (array)$allparams;}
	if(is_array($allparams)){
		foreach ($allparams as $key => $param) {
			if(is_array($param)){
				foreach($param as $p){
					array_push($qsarray, $key."[]=$p");
				}
			} else {
				array_push($qsarray, "$key=$param");
			}	
		}
	}
    return implode("&", $qsarray);
}

function plus_redirect($url){
	$string = '<script>'; 
	$string .= 'window.location = "' . $url . '"';
	$string .= '</script>'; 
 	echo $string; 
}

function plus_getpageurl($pageurl = null, $args = null){
  global $wp;
  if(empty($pageurl)){
  	$pageurl = home_url( $wp->request );
  }
  if(empty($args)){
  	$args = array();
  }
  return $pageurl.'?'. plus_get_qsparameter($args);
}

function plus_pagination($start, $limit, $total, $page="page", $displayto = true){

  $current_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  $pageurl = $current_url;
  $displaycount = ($start+$limit < $total)?$start+$limit:$total;
  $prevcount = ($start-$limit > 0)?$start-$limit:0;
  $allparams = plus_get_allparameter();
  $allparams['start']=$prevcount; 
  $allparams['limit']=$limit; 
  $prevpageurl = $pageurl.'?'. plus_get_qsparameter($allparams);
  $allparams['start']=$displaycount; 
  $nextpageurl = $pageurl.'?'. plus_get_qsparameter($allparams);
  $html = '';
  $html .= '<div class="dataTables_wrapper">';
  $html .= '<div class="row">';
  if($total == 0){ $start --; }
  if($displayto){
	  $html .= '<div class="col-sm-12 col-md-5"><div class="dataTables_info" id="'.$page.'-listing_info" role="status" aria-live="polite">'.plus_get_string("showing", "form").' '.($start+1).' '.strtolower(plus_get_string("to", "form")).' '.$displaycount.' '.plus_get_string("of", "form").' '.$total.' '.plus_get_string("records", "form").'</div></div>';
  } else {
	  $html .= '<div class="col-sm-12 col-md-5"><div class="dataTables_info" id="'.$page.'-listing_info" role="status" aria-live="polite">'.plus_get_string("showing", "form").' '.($start+1).' '.plus_get_string("of", "form").' '.$total.' '.plus_get_string("records", "form").'</div></div>';
	}
  $prev = '';
  $next = '';
  if($start > 0){
    $prev = '<li class="paginate_button page-item previous" id="'.$page.'-listing_previous"><a href="'.$prevpageurl.'" aria-controls="'.$page.'-listing"  class="page-link">'.plus_get_string("prev", "form").'</a></li>';
  }
  if($displaycount < $total){
    $next = '<li class="paginate_button page-item next " id="'.$page.'-listing_next"><a href="'.$nextpageurl.'" class="page-link">'.plus_get_string("next", "form").'</a></li>';
  }
  if(!empty($prev) || !empty($next)){
	  $html .= '<div class="col-sm-12 col-md-7"><div class="dataTables_paginate paging_simple_numbers" id="'.$page.'-listing_paginate"><ul class="pagination">'.$prev.$next.'</ul></div></div>';
  }
  $html .= '</div>';
  $html .= '</div>';
  return $html;
}


function plus_getuserformoodle($userid){
	$returndata = new stdClass();
    if($user = get_userdata($userid)){
    	// $user = json_decode(json_encode($user));
	    $meta = get_user_meta($userid);
	    $metadata = array();
	    $skippkeys = array("session_tokens");
	    foreach ($meta as $key => $value) {
	        if(in_array($key, $skippkeys)){ continue; }
	        if(is_array($value)){
	            $value = array_pop($value);
	        }
	        if(is_serialized($value)){
	            $value = unserialize($value);
	        }
	        $metadata[$key] = $value;
	    }
	    $user->metadata = $metadata;
    }
    return $user;
}

function plus_is_admin_user() {
    return current_user_can('manage_options');
}

function plus_startMoodleSession($tokenObj) {
	global $MOODLESESSION; 
	$MOODLE = new MoodleManager($tokenObj);
	$MOODLESESSION = $_SESSION['MOODLESESSION'] = $MOODLE->get("ConnectionTest");
}

function plus_getUsermeta($tokenObj) {
    global $WPUSER;
    $MOODLE = new MoodleManager($tokenObj);
    $WPUSER = $MOODLE->get("GetUsermeta");
}

function plus_allUserRoles($tokenObj) {
    global $ALLROLES;
    if($tokenObj){
	   return $ALLROLES = array("internaladmin", "schooladmin", "tutoringcenter", "tutor");
    }
}

function wp_unslash($value) {
    if (is_array($value)) {
        // If $value is an array, recursively unslash each element
        return array_map('wp_unslash', $value);
    } else {
        // If $value is not an array, remove slashes
        return stripslashes($value);
    }
}


function plus_get_request_parameter($key, $default = '') {
    // If not request set
    if ( ! isset( $_REQUEST[ $key ] ) ||( empty( $_REQUEST[ $key ] ) && $_REQUEST[ $key ] !== 0  && $_REQUEST[ $key ] !== "0" )) {
        return $default;
    }
    // Set so process it
    if(is_array($_REQUEST[ $key ])){
    	return $_REQUEST[ $key ];
    } else {
    	return strip_tags( (string) wp_unslash( $_REQUEST[ $key ] ) );
    }
}

function plus_get_allparameter() {
    return $_GET;
}


function plus_dateToFrench($date, $format = 'd F Y H:i') 
{
	global $USERLANG;
	$lang = $USERLANG;
	if (empty($date)) { return '';}
	if(empty(intval($date))){
		return $date;
	}
	if (empty($format)) { $format = 'd F Y H:i';}
    $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $finaldate = date($format, $date );
    if($lang == 'FR'){
	    $french_days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
	    $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
	    $finaldate = str_replace($english_months, $french_months, str_replace($english_days, $french_days, $finaldate));
    }
    return $finaldate;
}



function plus_setuserlang() {
	global $USERLANG;
	$current_user = wp_get_current_user();
	$lang='FR';
	if($current_user){
		$userlang = get_user_meta( $current_user->ID, 'lang', true);
		if(empty($userlang)){
			update_user_meta( $current_user->ID, 'lang', $lang, $userlang );
			$userlang = $lang;
		}
	} else {
		$userlang=$lang;
	}
	$USERLANG = $userlang;
	return $userlang;
}

function plus_getuserlang() {
	global $USERLANG;
	if(!empty($USERLANG)){
		return $USERLANG;
	} else {
		plus_setuserlang();
	}
	return $USERLANG;
}

function plus_generatelangurl($lang, $currentpage){
  $allparams = plus_get_allparameter();
  $allparams['changelang'] = $lang; 
  $pageurl = $currentpage.'?'. plus_get_qsparameter($allparams);
  return $pageurl;
}

function plus_updateuserlang($lang, $current_url) {
	$current_user = wp_get_current_user();
	if(!empty($lang) && !empty($current_user)){
		$userlang = get_user_meta( $current_user->ID, 'lang', true);
		update_user_meta( $current_user->ID, 'lang', $lang, $userlang );		
		$allparams = plus_get_allparameter();
		unset($allparams['changelang']);
		plus_redirect($current_url);
	}
}

function plus_get_string($key, $page=""){
	global $LANGUAGESTRINGS;
	$lang = plus_getuserlang();
	if(isset($LANGUAGESTRINGS[$lang])){
		$stringarray = $LANGUAGESTRINGS[$lang];
		$finalkey = (!empty($page)?$page."_":"").$key;
		if(isset($stringarray[$finalkey])){
			return $stringarray[$finalkey];
		} else {
			return $lang.'['.$finalkey.']';
		}
	} else {
		return $lang.'['.$key.']';
	}
}


function wp_update_user( array|object $userdata ) {
    $newUserObj = $_SESSION['CURRENTUSERSESSION'];
    if (is_array($userdata)) {
        $userdata = (object) $userdata;
    }
    foreach ($userdata as $key => $value) {
        if (property_exists($newUserObj, $key)) {
            $newUserObj->$key = $value;
        }
    }
    $_SESSION['CURRENTUSERSESSION'] = $newUserObj;
}

function get_user_by( string $field, int|string $value ) {
    $newUserObj = $_SESSION['CURRENTUSERSESSION'];
    if (property_exists($newUserObj, $field) && $newUserObj->$field == $value) {
        return $newUserObj;
    } else {
        return null;
    }
}

function wp_insert_user( array|object $userdata ) {
    $newUserObj = $_SESSION['CURRENTUSERSESSION'];
    if (is_array($userdata)) {
        $userdata = (object) $userdata;
    }
    foreach ($userdata as $key => $value) {
        if (property_exists($newUserObj, $key)) {
            $newUserObj->$key = $value;
        }
    }
    if (property_exists($userdata, 'ID')) {
        $newUserObj->ID = $userdata->ID;
    }
    $_SESSION['CURRENTUSERSESSION'] = $newUserObj;
    return $newUserObj->ID;
}

function plus_login_failed($userid) {
	global $wpdb;
	wp_redirect("/login?loginfailed=1");
}

function plus_view_noaccess($url="") {
	global $wpdb;
	return '<div class="alert alert-warning"><h3>You don\'t have access to this page</h3>'.($url?'<a href="'.$url.' class="btn btn-primary">Continue</a>':'').'</div>';
}

function plus_setuserinstitution() {
	global $USERINSTITUTION, $MOODLESESSION;
	$current_user = wp_get_current_user();
	if($current_user){
		$currentinstitution = get_user_meta( $current_user->ID, 'currentinstitution', true);
		if(empty($currentinstitution)){
			if(isset($MOODLESESSION->allinstitutions) && is_array($MOODLESESSION->allinstitutions) && !empty($MOODLESESSION->allinstitutions)){
				update_user_meta( $current_user->ID, 'currentinstitution', $MOODLESESSION->allinstitutions[0]->id, $currentinstitution );
			}
			$currentinstitution = $MOODLESESSION->allinstitutions[0]->id;
		}
		$USERINSTITUTION = $currentinstitution;
	}
	return $USERINSTITUTION;
}

function plus_getuserinstitution() {
	global $USERINSTITUTION;
	if(!empty($USERINSTITUTION)){
		return $USERINSTITUTION;
	} else {
		plus_setuserinstitution();
	}
	return $USERINSTITUTION;
}

function plus_generatecustomurl($key,$value){
  global $wp;
  $pageurl = home_url( $wp->request );
  $allparams = plus_get_allparameter();
  $allparams[$key]=$value; 
  $pageurl = $pageurl.'?'. plus_get_qsparameter($allparams);
  return $pageurl;
}

function plus_updateuserinstitution($institutionid) {
	global $USERINSTITUTION, $wp;
	$current_user = wp_get_current_user();
	if(!empty($institutionid) && !empty($current_user)){
		$currentinstitution = get_user_meta( $current_user->ID, 'currentinstitution', true);
		update_user_meta( $current_user->ID, 'currentinstitution', $institutionid, $currentinstitution );		
		$pageurl = home_url( $wp->request );
		$allparams = plus_get_allparameter();
		unset($allparams['changeinstitution']);
		$pageurl = $pageurl.'?'. plus_get_qsparameter($allparams);
		plus_redirect($pageurl);
	}
}

function require_login() {
	global $CFG; 
	if(!(isset($_SESSION['loginuser']) && !empty($_SESSION['loginuser']))){
		redirect("{$CFG->wwwroot}/login/", "Please login to view this page", "info");
	}
}

function has_internet() {
	global $CFG;
	return $CFG->internetstatus;
}

function require_internat() {
	global $CFG;
	if(!has_internet()){
		redirect("{$CFG->wwwroot}/", "Please connect to internet", "warning");
	}
}

function isloggedin() {
	if(isset($_SESSION['loginuser']) && !empty($_SESSION['loginuser'])){
		return true;
	}
}

function optional_param($parname, $default=null) {
    // POST has precedence.
    if (isset($_POST[$parname])) {
        $param = $_POST[$parname];
    } else if (isset($_GET[$parname])) { 
        $param = $_GET[$parname];
    } else {
        return $default;
    } 
    return $param;
}





function syncAllUsers() {
	global $API, $USER, $CFG;
	if(has_internet()){
		$reqdata = array();
		$response = $API->call("getUserCredentials",$reqdata);
		if(is_object($response) && $response->code == 200) {
			saveFileTo("institutionroot", "/", $CFG->userdata, $response->data);
		}
	}
}

function my_encrypt($data) {
	global $CFG;
	if(empty($key)){
		$key = $CFG->key;
	}
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function my_decrypt($data) {
	global $CFG;
	if(empty($key)){
		$key = $CFG->key;
	}
    $encryption_key = base64_decode($key);
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

function getdirpath($region)
{
	global $CFG, $USER, $INSTITUTION;
	$finalpath = $CFG->dataroot;
	switch ($region) {
		case 'dataroot':
			$finalpath = $CFG->dataroot;
		break;
		case 'syncdataroot':
			$finalpath = $CFG->syncdataroot;
		break;
		case 'localdataroot':
			$finalpath = $CFG->localdataroot;
		break;
		case 'userroot':
			$subpath = md5("user_{$USER->id}");
			$finalpath = "{$CFG->dataroot}/{$subpath}";
		break;
		case 'cache':
			$subpath = md5("cache");
			$finalpath = "{$CFG->dataroot}/{$subpath}";
		break;
		case 'localcache':
			$subpath = md5("cache");
			$finalpath = "{$CFG->dataroot}/{$subpath}";
		break;
		case 'systemcache':
			$finalpath = "{$CFG->systemcacheddir}";
		break;
		case 'systemtemp':
			$finalpath = "{$CFG->systemtempdir}";
		break;
		case 'institutionroot':
			$subpath = md5("institution_{$INSTITUTION->institutionid}");
			$finalpath = "{$CFG->dataroot}/{$subpath}";
		break;
		default:
			$finalpath = $CFG->dataroot;
		break;
	}
	if(!is_dir($finalpath)) {
	    mkdir($finalpath, 0777, true);
	}
	return $finalpath;
}

function saveFileTo($region, $path, $filename, $data) {
	$finalpath = getdirpath($region);
	if(!is_string($data)){
		$data = json_encode($data);
	}
	$encrypted_code = my_encrypt($data);
	$finalfilepath = "{$finalpath}{$path}{$filename}"; 
	checkdirpath("{$finalpath}{$path}");
	file_put_contents($finalfilepath, $encrypted_code);
	return true;
}

function saveFile($path, $filename, $data) {
	global $CFG;
	if(!is_dir($path)) {
	    mkdir($path, 0777, true);
	}
	if(!is_string($data)){
		$data = json_encode($data);
	}
	$encrypted_code = my_encrypt($data); 
	file_put_contents($path.'/'.$filename, $encrypted_code);
	return true;
}

function saveOriginalFile($path, $filename, $data) {
	global $CFG;
	if(!is_dir($path)) {
	    mkdir($path, 0777, true);
	}
	if(!is_string($data)){
		$data = json_encode($data);
	}
	// $encrypted_code = my_encrypt($data); 
	file_put_contents($path.'/'.$filename, $data);
	return true;
}

function getFileFrom($region, $path, $filename) {
	global $CFG;
	$finalpath = getdirpath($region);
	$finalfilepath = "{$finalpath}{$path}{$filename}"; 
	if(file_exists($finalfilepath)){
		$encrypted_code = file_get_contents($finalfilepath);
		$decrypted_code = my_decrypt($encrypted_code);//Decrypt the encrypted code.
		return $decrypted_code;
	}
}

function getFile($path) {
	global $CFG;
	if(file_exists($path)){
		$encrypted_code = file_get_contents($path);
		$decrypted_code = my_decrypt($encrypted_code);//Decrypt the encrypted code.
		return $decrypted_code;
	}
}

function get_allusers() {
	global $CFG;
	$filedata = getFileFrom("institutionroot", "/", $CFG->userdata);

	if($filedata){
		$allusers = json_decode($filedata);
	} else {
		$allusers = new stdClass();
		$allusers->credentials = array();
		$allusers->lastsynced = 0;
		$allusers->institutionId = 0;
	}
	return $allusers;
}

function syncAllHomeworks() {
	global $API, $USER, $CFG;
	if(has_internet()){
		$reqdata = array();
		$response = $API->call("getOfflineHomeWork",$reqdata);
		if(is_object($response) && $response->code == 200) {
			saveFileTo("institutionroot", "/", $CFG->homeworkdata, $response->data);
		}
	}
}

function get_allhomeworks() {
	global $CFG;
	syncAllHomeworks();
	$filedata = getFileFrom("institutionroot", "/", $CFG->homeworkdata);
	if($filedata){
		$allusers = json_decode($filedata);
	} else {
		$allusers = new stdClass();
		$allusers->homeworks = array();
		$allusers->lastsynced = 0;
		$allusers->institutionId = 0;
	}
	return $allusers;
}

function syncAllGroups() {
	global $API, $USER, $CFG;
	if(has_internet()){
		$reqdata = array();
		$response = $API->call("getOfflineGroups",$reqdata);
		if(is_object($response) && $response->code == 200) {
			saveFileTo("institutionroot", "/", "groupsdata.tmp", $response->data);
		}
	}
}

function get_allgroups() {
	global $CFG;
	syncAllGroups();
	$filedata = getFileFrom("institutionroot", "/", "groupsdata.tmp");
	if($filedata){
		$allusers = json_decode($filedata);
	} else {
		$allusers = new stdClass();
		$allusers->groups = array();
		$allusers->lastsynced = 0;
		$allusers->institutionId = 0;
	}
	return $allusers;
}

function redirect($url, $message="", $type="info") {
	global $OUTPUT;
	$_SESSION['redirecturl'] = $url;
	if(!empty($message)){
		$_SESSION['redirectmessage'] = $message;
		$_SESSION['redirecttype'] = $type;
	}
	if(defined('INSTALLING')){
	}
	if(!headers_sent()){
	    $redirectby = 'Local';
        @header("X-Redirect-By: $redirectby");

        // 302 might not work for POST requests, 303 is ignored by obsolete clients.
        @header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
        @header('Location: '.$url);
        exit;
	} else {
        echo $OUTPUT->redirect_message($url);
	}
}


function get_localdashdata($args){
	global $CFG;
	$dashdata = new stdClass();
	$dashdata->grades = array();
    $homeworkdata = array("notstarted"=>array("count"=>0, "total"=>0, "percent"=>0), "notpassed"=>array("count"=>0, "total"=>0, "percent"=>0), "notcompleted"=>array("count"=>0, "total"=>0, "percent"=>0), "completed"=>array("count"=>0, "total"=>0, "percent"=>0),"latecompleted"=>array("count"=>0, "total"=>0, "percent"=>0), "passed"=>array("count"=>0, "total"=>0, "percent"=>0), "totalquizcompleted"=>0);
	$allhomework = get_allhomeworks();
	$usersdata = get_allusers();
	$alllocaldata = get_alllocaldata();
	$totalhomeworkstudent = 0;
	switch ($args->filtertype) {
        case 1:
            $args->fromdate = date("Y-m-d");
            $args->todate = date("Y-m-d");
            break;
        case 2:
            $startdate = strtotime("-1 day");
            $args->fromdate = date("Y-m-d", $startdate);
            $args->todate = date("Y-m-d", $startdate);
            break;
        case 3:
            $startdate = strtotime(date("01 F Y"));
            $enddate = strtotime(date("t F Y"));
            $args->fromdate = date("Y-m-d", $startdate);
            $args->todate = date("Y-m-d", $enddate);
            break;
    }
	$args->fromdatestamp = strtotime($args->fromdate);
	$args->fromdatestr = date("d F Y H:i:s A", $args->fromdatestamp);
	$args->todatestamp = strtotime("+1 day", strtotime($args->todate));
	$args->todatestr = date("d F Y H:i:s A", $args->todatestamp);
	$totalquizcompleted = 0;
    foreach ($alllocaldata as $key => $attempt) {
    	$q = json_decode(getFile("{$CFG->syncdataroot}/$attempt"));
    	if(is_object($q)){
	        if($q->submissiontime < $args->fromdatestamp || $q->submissiontime < $args->todatestamp){
	            continue;
	        }
			$totalquizcompleted ++;
    	}
    }
	// echo "<pre>\n\n\n\n\n\n";
	// print_r($args);
	// echo "</pre>";
	// die;
	foreach ($allhomework->homeworks as $key => $homework) {
		$alluser = array_values(array_filter($usersdata->credentials, function ($user) use ($homework) {
		    return in_array($homework->groupid, $user->groupids);
		}));
		$filenames = array_map(function($user) use ($homework)
		{
		    return "quiz_{$homework->quiz}_userid_{$user->id}_";
		}, $alluser);
		$attempted = array_filter($alllocaldata, function ($datafile) use ($filenames)
		{
			$exist = false;
			foreach ($filenames as $file) {
				if(strpos($datafile, $file)){$exist=true; break;}
			}
		    return $exist;
		});
		$homework->totaluser = sizeof($alluser);
	    $started = 0;
	    $completed = 0;
	    $completed = 0;
	    $latecompleted = 0;
	    $passed = 0;
	    foreach ($attempted as $key => $attempt) {
	    	$q = json_decode(getFile("{$CFG->syncdataroot}/$attempt"));
	    	if(is_object($q)){
		        // if($q->submissiontime < $args->fromdatestamp || $q->submissiontime < $args->todatestamp){
		        //     continue;
		        // }
	        	$started++;
	        	if($q->isfinished){
			        $completed++;
			        if(isset($q->qasumgrades) && (($q->qasumgrades/$homework->qsumgrades)*$homework->gigrademax >= $homework->gigradepass)){
			            $passed++;
			        }
			        if($q->submissiontime>$homework->duedate){
			            $latecompleted++;
			        }
	        	}
	    	}
	    }
        $totalstudent = $homework->totaluser;
        $notstarted = $totalstudent-$started;
        $totalhomeworkstudent += $totalstudent;
        $homeworkdata['passed']['total']+=$completed;
        $homeworkdata['notpassed']['total']+=$completed;
        $homeworkdata['completed']['total']+=$totalstudent;
        $homeworkdata['latecompleted']['total']+=$completed;
        $homeworkdata['notstarted']['total']+=$totalstudent;
        $homeworkdata['notcompleted']['total']+=$totalstudent;
        $homeworkdata['passed']['count']+=$passed;
        $homeworkdata['completed']['count']+=$completed;
        $homeworkdata['latecompleted']['count']+=$latecompleted;
        $homeworkdata['notstarted']['count']+=$notstarted;
        $homeworkdata['notcompleted']['count']+= ($totalstudent - ($notstarted+$completed));
        $homeworkdata['notpassed']['count']=$homeworkdata['completed']['count']-$homeworkdata['passed']['count'];

	}
    foreach ($homeworkdata as $key => $value) {
        if(empty($homeworkdata[$key]['count']) || empty($homeworkdata[$key]['total'])){ continue; }
        $homeworkdata[$key]['percent'] = number_format($homeworkdata[$key]['count']/$homeworkdata[$key]['total']*100, 2);
    }

    $homeworkdata['totalquizcompleted']=$totalquizcompleted;

	$dashdata->homeworkkpi = json_decode(json_encode($homeworkdata));
	return $dashdata;
}


function get_dashdata($args) {
	global $CFG, $API;
	if(has_internet()){
		$dashdata = $API->call("Dashboard",$args);
		if($dashdata->status =1){
			saveFileTo("userroot", "/", "dashdata.tmp", $dashdata->data);
		}
		if($filedata = getFileFrom("userroot", "/", "dashdata.tmp")){
			$dashdata = json_decode($filedata);
		} else {
			$dashdata = new stdClass();
			$dashdata->grades = array();
			$dashdata->homeworkkpi = json_decode(json_encode(array("notstarted"=>array("count"=>0, "total"=>0, "percent"=>0), "notpassed"=>array("count"=>0, "total"=>0, "percent"=>0), "notcompleted"=>array("count"=>0, "total"=>0, "percent"=>0), "completed"=>array("count"=>0, "total"=>0, "percent"=>0),"latecompleted"=>array("count"=>0, "total"=>0, "percent"=>0), "passed"=>array("count"=>0, "total"=>0, "percent"=>0), "totalquizcompleted"=>0)));
		}
	} else {
		$dashdata = get_localdashdata($args);
	}
	// if($filedata = getFileFrom("userroot", "/", "dashdata.tmp")){
	// 	$dashdata = json_decode($filedata);
	// } else {
	// 	$dashdata = new stdClass();
	// 	$dashdata->grades = array();
	// 	$dashdata->homeworkkpi = json_decode(json_encode(array("notstarted"=>array("count"=>0, "total"=>0, "percent"=>0), "notpassed"=>array("count"=>0, "total"=>0, "percent"=>0), "notcompleted"=>array("count"=>0, "total"=>0, "percent"=>0), "completed"=>array("count"=>0, "total"=>0, "percent"=>0),"latecompleted"=>array("count"=>0, "total"=>0, "percent"=>0), "passed"=>array("count"=>0, "total"=>0, "percent"=>0), "totalquizcompleted"=>0)));
	// }
	return $dashdata;
}

function fetch_awaitingsynceddata() {
	global $API, $USER, $CFG;
	if(has_internet()){
		$reqdata = array(
			"lastsynced"=>0
		);
		$response = $API->call("dataSyncStatus",$reqdata);
		if(is_object($response) && $response->code == 200) {
			saveFileTo("systemcache", "/", $CFG->awaitingsynceddata, $response->data);
		}
	}
}

function fetch_awaitingfileddata() {
	global $API, $USER, $CFG;
	if(has_internet()){
		$reqdata = array(
			"lastsynced"=>0
		);
		$response = $API->call("fileSyncStatus",$reqdata);
		if(is_object($response) && $response->code == 200) {
			saveFileTo("systemcache", "/", $CFG->awaitingfileddata, $response->data);
		}
	}
}

function get_lastsuncedfiledata() {
	global $CFG;
	if($data = getFileFrom("systemcache", "/",$CFG->syncedfiledata)){
		return json_decode($data);
	}
	return null;
}

function get_awaitingsynceddata($from='') {
	global $CFG;
	$filedata = getFileFrom("systemcache", "/",$from);
	if($filedata){
		$synceddata = json_decode($filedata);
	} else {
		$synceddata = new stdClass();
		$synceddata->data = array();
		$synceddata->updateddate = 0;
	}
	return $synceddata;
}

function get_logintoken() {
	return isset($_SESSION['logintoken'])?$_SESSION['logintoken']:null;
}


function timestamp_to_date($date, $format="d F Y H:i A") {
	if(intval($date) !=0 ){
		$datetext = date($format,$date);
		return $datetext;
	}
	return $date;
}

function base_init() {
	global $CFG;
    if(!wp_get_current_user()) {
        if(strpos($_SERVER['PHP_SELF'], '/login/index.php') === false){
            redirect($CFG->wwwroot . '/login/');
        }
    }
}


function get_localdata() {
	global $USER, $CFG, $LOCAL, $INSTITUTION;
	$d = getFileFrom("dataroot", "/", md5("lastinstitution").".tmp");
	if(!empty($d)){
		$INSTITUTION = json_decode($d);
	}
	$filedata = json_decode(getFile($CFG->dataroot."/access.tmp"));
	if(is_object($filedata)){
		return $filedata;
	} else {
		$filedata = new stdClass();
		$filedata->syncedfiles = array();
		$filedata->datalastsynced = get_string("never",'form');
		return $filedata;
	}
}

function prepareLocalData() {
	global $USER, $CFG, $LOCAL, $FILES;
	try {
		$LOCAL = get_localdata();
	} catch (Exception $e) {
		$LOCAL = new stdClass();
		$LOCAL->syncedfiles = array();
	}
}

function getDashboardData($formdata) {
	$data = new stdClass();
	$data->homeworkdata = get_allhomeworks();
	$data->usersdata = get_allusers();
	$data->awaitingsyncdata = get_allawaitingsync();
	$data->dashdata = get_dashdata($formdata);
	return $data;
}

function fullname($user=null){
	global $USER;
	$name = array();
	if(empty($user)){
		$user = $USER;
	}
	if(!empty($user->firstname)){array_push($name, $user->firstname);}
	if(!empty($user->lastname)){array_push($name, $user->lastname);}
	return implode(" ", $name);
}


function get_alllocaldata() {
	global $CFG, $LOCAL, $FILES;
	return $FILES->scanAllDir($CFG->syncdataroot, true);
}

function get_allawaitingsync() {
	global $CFG, $LOCAL;
	$syncedfiles = get_alllocaldata();
	$synced = $LOCAL->syncedfiles;
	$lastsynced = $LOCAL->datalastsynced?$LOCAL->datalastsynced:get_string("never",'form');
	$awaiting = array_filter($syncedfiles, function ($file) use ($synced) {
	    return !in_array($file, $synced);
	});
	$syncdata = new stdClass();
	$syncdata->awaiting = $awaiting;
	$syncdata->synced = $synced;
	$syncdata->awaitingcount = sizeof($syncdata->awaiting);
	$syncdata->syncedcount = sizeof($syncdata->synced);
	$syncdata->total = $syncdata->awaitingcount + $syncdata->syncedcount;
	$syncdata->lastsynced = $lastsynced;
	return $syncdata;
	
	// $attemptdata = json_decode(getFile("{$CFG->syncdataroot}/$path"));
}

function get_string($key, $page="") {
	global $LANGUAGESTRINGS, $CURRENTLANG;
	$lang = $CURRENTLANG;
	if(empty($lang)){$lang="FR";}
	if(isset($LANGUAGESTRINGS[$lang])){
		$stringarray = $LANGUAGESTRINGS[$lang];
		$finalkey = (!empty($page)?$page."_":"").$key;
		if(isset($stringarray[$finalkey])){
			return $stringarray[$finalkey];
		} else {
			return $lang.'['.$finalkey.']';
		}
	} else {
		return $lang.'['.$key.']';
	}
}

function get_qsparameter($allparams) {
	$qsarray = array();
	if(is_object($allparams)){$allparams = (array)$allparams;}
	if(is_array($allparams)){
		foreach ($allparams as $key => $param) {
			if(is_array($param)){
				foreach($param as $p){
					array_push($qsarray, $key."[]=$p");
				}
			} else {
				array_push($qsarray, "$key=$param");
			}	
		}
	}
    return implode("&", $qsarray);
}

function get_allparameter() {
    return $_GET;
}

function getpageurl($pageurl = null, $args = null) {
  global $CFG;
  if(empty($pageurl)){
  	$pageurl = $CFG->wwwroot;
  }
  if(empty($args)){
  	$args = array();
  }
  return $pageurl.'?'. get_qsparameter($args);
}

function get_current_pageurl()
{
	global $CFG;
	return (isset($_SERVER['SCRIPT_NAME']) && !empty($_SERVER['SCRIPT_NAME']))?$_SERVER['SCRIPT_NAME']:$CFG->wwwroot;
} 

function check_validateUserResponse($content) {
	if(is_string($content)){
		$content = json_decode($content);
	}
	$errors = array();
	if(!is_object($content)){
		array_push($errors, "Invalid Data format");
	} else {
		$allrequiredcheck = array("userid", "devicetoken", "devicename", "attempt", "quizid", "submissiondate", "submissiontime", "questions", "isAttempted", "isfinished");
		$notemptycheck = array("userid", "devicetoken", "devicename", "attempt", "quizid", "submissiondate", "submissiontime", "questions", "isAttempted");
		foreach ($allrequiredcheck as $key => $requiredfield) {
			if(!isset($content->$requiredfield)){
				array_push($errors, "Missing required key {$requiredfield}");
			} else if(in_array($requiredfield, $notemptycheck) && empty($content->$requiredfield))
				array_push($errors, "{$requiredfield} should not be empty.");
		}
	}
	if(sizeof($errors) == 0){
		return true;
	}
	return $errors;
}

function get_homeworkreport($homeworkid) {
	GLOBAL $CFG;
	$homeowrk = null;
	$homeworkdata = get_allhomeworks()->homeworks;
	$found_key = array_search($homeworkid, array_column((array)$homeworkdata, 'id'));
	if($found_key !== false){
		$homeowrk = $homeworkdata[$found_key];
		$allusers = get_allusers()->credentials;
		$groupid = $homeowrk->groupid;
		$users = array_filter($allusers, function ($user) use ($groupid) {
		    return in_array($groupid, $user->groupids);
		});
		
		$syncedfiles = get_alllocaldata();
		foreach ($users as $key => $user){
			$filename = "quiz_{$homeowrk->quiz}_userid_{$user->id}";
			$userdata = array_filter($syncedfiles, function ($path) use ($filename) {
		    		return strpos($path, $filename) !== false;
			});
			if(sizeof($userdata)> 0){
				$user->attemptdata = json_decode(getFile("{$CFG->syncdataroot}/".array_pop($userdata)));
				// echo json_encode($user->attemptdata);
				// die;
			} else {
				$user->attemptdata = null;
			}
		}
        $homeowrk->users = $users;
	}
	return $homeowrk;
}


function syncAlluserdata($syncnow) {
	global $API, $USER, $CFG;
	if(has_internet()){
		try {
			$data = get_allawaitingsync();
			if($data->awaitingcount){
				$counter = 0;
				$allattemptdata = array();
				$awaiting = $data->awaiting;
				foreach ($awaiting as $key => $attempt) {
					if($syncnow != "all" && $syncnow != md5($attempt)){
						continue;
					}
					$attemptdata = getFile("{$CFG->syncdataroot}/$attempt");
					if($counter >= $CFG->syncapicount){break;}
					array_push($allattemptdata, array("file"=>$attempt, "data"=>$attemptdata));
					$counter++;
				}
				$reqdata = array(
					"alldata"=>$allattemptdata
				);
				$response = $API->call("syncLocalData",$reqdata);
				if(is_object($response) && $response->code == 200) {
					if(is_array($response->data->passed)){
						foreach ($response->data->passed as $key => $attempt) {
							datasyncedsuccess($attempt->file);
						}
					}
				}
			}
		} catch (Exception $e) {
		}
	}
}

function datasyncedsuccess($filepath) {
	global $CFG;
	$data = get_localdata();
	if(is_array($data->syncedfiles)){
		array_push($data->syncedfiles, $filepath);
		$data->datalastsynced = time();
		saveFileTo("dataroot", "/", "access.tmp", json_encode($data));		
	}
}

function syncmoduledata($updatedata) {
	global $API, $CFG, $FILES;
	if(has_internet()){
		$reqdata = array(
			"reqid"=>$updatedata->id
		);
		$response = $API->call("getUpdatedfiledata",$reqdata);
		if(is_object($response) && $response->code == 200 && $response->data->filedata) {
			$FILES->prepareofflinedata($updatedata, base64_decode($response->data->filedata));
			saveFileTo("dataroot", "/syncdata/", md5("data_{$updatedata->id}").".tmp", base64_decode($response->data->filedata));
			// saveOriginalFile($CFG->localorigdataroot, "{$updatedata->id}.zip", base64_decode($response->data->filedata));
			return addtolocalsynceddata($updatedata);
		}
	}
}

function addtolocalsynceddata($updatedata) {
	global $API, $CFG;
	$synceddata = get_awaitingsynceddata($CFG->syncedsynceddata);
	if(is_array($synceddata->data)){
		$found_key = array_search($updatedata->id, array_column($synceddata->data, 'id'));
		if($found_key !== false){
			$synceddata->data[$found_key] = $updatedata;
		} else {
			array_push($synceddata->data, $updatedata);
		}
		if($updatedata->processtime > $synceddata->updateddate){
			$synceddata->updateddate = $updatedata->processtime;
		}
		saveFileTo("systemcache", "/", $CFG->syncedsynceddata, json_encode($synceddata));
		return true;
	}
}

function updatehomeqordquestionstatus($homeworkid, $questionid, $status=0) {
	global $API, $USER, $CFG;
	$hmstatus = getquestionblockingstatus();
	if(!isset($hmstatus[$homeworkid])){
		$hmstatus[$homeworkid] = new stdClass();
	}
	$questionsstatus= (object)$hmstatus[$homeworkid];
	$questionsstatus->$questionid = $status;
	$hmstatus[$homeworkid] = $questionsstatus;
	saveFileTo("institutionroot", "/", "homeworkquestionstatus.tmp", $hmstatus);
	return true;
}

function gethomeqordquestionstatus($homeworkid) {
	global $API, $USER, $CFG;
	$hmstatus = getquestionblockingstatus();
	if(!isset($hmstatus[$homeworkid])){
		$hmstatus[$homeworkid] = new stdClass();
	}
	return $hmstatus[$homeworkid];
}

function getquestionblockingstatus() {
	global $CFG;
	$filedata = getFileFrom("institutionroot", "/", "homeworkquestionstatus.tmp");
	if($filedata){
		$data = json_decode($filedata, true);
	} else {
		$data = array();
	}
	return $data;
}

function get_group($groupid)
{
	global $USER;
	$groupdata = get_allgroups();
	$group_key = array_search($groupid, array_column($groupdata->groups, 'id'));
	if($group_key !== false){
		$group = $groupdata->groups[$group_key];
		if(!in_array($group->id, $USER->groupids)){
			return false;
		}
		return $group;
	}
	return false;
}

function online_GetGroupDetailsById($groupid, $currentschoolyear=0) {
	global $API;
	$APIRES = $API->call("GetGroupDetailsById", array("id"=>$groupid, "currentschoolyear"=>$currentschoolyear));
	if($APIRES->code == 200){
		return $APIRES->data;
	}
	return null;
}

function online_GetGroupById($groupid) {
	global $API;
	$APIRES = $API->call("GetGroupById", array("id"=>$groupid));
	if($APIRES->code == 200){
		return $APIRES->data;
	}
	return null;
}

function online_GetCoursesModeDetails($ids) {
	global $API;
	$APIRES = $API->call("GetCoursesModeDetails", array("ids"=>$ids));
	if($APIRES->code == 200){
		return $APIRES->data;
	}
	return null;
}

function online_GetHomeWorkById($id) {
	global $API;
	$APIRES = $API->call("GetHomeWorkById", array("id"=>$id));
	if($APIRES->code == 200){
		return $APIRES->data;
	}
	return null;
}

function online_SaveHomeWork($formdata)
{
	global $API;

	$APIRES = $API->call("SaveHomeWork", $formdata);
	if($APIRES->code == 200){
		return $APIRES->data;
	}
	return null;
}

function online_getAllSchoolyear()
{
	global $API;
	$APIRES = $API->call("getAllSchoolyear", array());
	if($APIRES->code == 200){
		return $APIRES->data;
	}
	return null;
}

function online_getclassProfileFilter($formdata)
{
	global $API;
	$APIRES = $API->call("getclassProfileFilter", $formdata);
	if($APIRES->code == 200){
		return $APIRES->data;
	}
	return null;
}

function online_getclassProfileReport($formdata)
{
	global $API;
	$APIRES = $API->call("getclassProfileReport1", $formdata);
	if($APIRES->code == 200){
		return $APIRES->data;
	}
	return null;
}

function local_getapilog(){
	global $CFG;
	$filedata = getFileFrom("dataroot", "/", $CFG->apilogdata);
	if($filedata){
		$filedata = json_decode($filedata);
	} else {
		$filedata = array();
	}
	return $filedata;
}

function local_saveapilog($request){
	global $API, $USER, $CFG;
	$request = (array)$request;
	$logdata = local_getapilog();
	if(empty($logdata)){
		$logdata = array();
	}
	$request['logtime'] = time();
	array_push($logdata, $request);
	saveFileTo("dataroot", "/", $CFG->apilogdata, $logdata);
	return true;	
}

function get_syncedfiledata($fileid)
{
	return getFileFrom("dataroot", "/syncdata/", md5("data_{$fileid}").".tmp");
}

function syncAllDevices() {
	global $API, $USER, $CFG;
	require_internat();
	$reqdata = array();
	$response = $API->call("getOfflineGroups",$reqdata);
	if(is_object($response) && $response->code == 200) {
		saveFileTo("institutionroot", "/", $CFG->devicelist, $response->data);
	}
}

function get_alldevices() {
	global $CFG;
	$filedata = getFileFrom("institutionroot", "/", $CFG->devicelist);
	if($filedata){
		$allusers = json_decode($filedata);
	} else {
		$allusers = new stdClass();
		$allusers->groups = array();
		$allusers->lastsynced = 0;
		$allusers->institutionId = 0;
	}
	return $allusers;
}

function syncAlldevicelist() {
	global $API, $USER, $CFG;
	require_internat();
	$reqdata = array();
	$response = $API->call("getDeviceKeys",$reqdata);
	if(is_object($response) && $response->code == 200) {
		saveFileTo("institutionroot", "/", $CFG->devicekeys, $response->data);
	}
}

function get_alldevicelist() {
	global $CFG;
	$filedata = getFileFrom("institutionroot", "/", $CFG->devicekeys);
	if($filedata){
		$devicelist = json_decode($filedata);
	} else {
		$devicelist = new stdClass();
		$devicelist->deviceslist = array();
		$devicelist->allowedkeys = 0;
	}
	return $devicelist;
}

function initialsync() {
	syncAllGroups();
	syncAlldevicelist();
	syncAllHomeworks();
	syncAllUsers();
	syncAlluserdata('all');
}

function get_cronsetting() {
	global $CFG;
	$filedata = getFileFrom("institutionroot", "/", $CFG->cronsetting);
	if($filedata){
		$finaldata = json_decode($filedata);
	} 
	if(empty($finaldata)){
		$finaldata = new stdClass();
		$finaldata->active = false;
		$finaldata->croninterval = 5;
		$finaldata->lastrun = 0;
	}
	return $finaldata;
}

function set_cronsetting($args) {
	global $CFG;
	$setting = get_cronsetting();
	$setting->active = $args->active;
	if(isset($args->active)){$setting->active = $args->active;}
	if(isset($args->croninterval)){$setting->croninterval = $args->croninterval;}
	if(isset($args->lastrun)){$setting->lastrun = $args->lastrun;}
	saveFileTo("institutionroot", "/", $CFG->cronsetting, $setting);
	return true;
}

function checkcronstatus(){
	if(has_internet() && isloggedin()){
		$setting = get_cronsetting();
		if($setting->active){
			$nextrun = strtotime("+{$setting->croninterval} minutes", $setting->lastrun);
			if($nextrun < time()){
				syncAllHomeworks();
				syncAllUsers();
				syncAlldevicelist();
				fetch_awaitingsynceddata();
				// syncAlluserdata('all');
				$setting->lastrun = time();
				set_cronsetting($setting);
			}
		}
	}
}

function get_UserSetting() {
	global $CFG;
	$finaldata = new stdClass();
	$finaldata->userlang = "EN";
	if(isloggedin()){
		$filedata = getFileFrom("userroot", "/", $CFG->usersetting);
		if($filedata){
			$finaldata = json_decode($filedata);
		}
	}
	if(empty($finaldata)){
		$finaldata = new stdClass();
		$finaldata->userlang = "EN";
	}
	return $finaldata;
}

function set_UserSetting($args) {
	global $CFG;
	if(isloggedin()){
		$setting = get_UserSetting();
		foreach ($args as $key => $value) {
			$setting->$key = $value;
		}
		saveFileTo("userroot", "/", $CFG->usersetting, $setting);
	}
	return true;
}

function syncAllevents() {
	global $CFG, $API;
	if(has_internet()){
		sync_eventUpdate();
		$reqdata = array();
		$response = $API->call("getCalendarEvents",$reqdata);
		if(is_object($response) && $response->code == 200) {
			saveFileTo("institutionroot", "/", $CFG->eventdata, $response->data);
		}
	}
}

function get_allevents() {
	global $CFG;
	syncAllevents();
	$filedata = getFileFrom("institutionroot", "/", $CFG->eventdata);
	if($filedata){
		$alldata = json_decode($filedata);
	} else {
		$alldata = new stdClass();
		$alldata->events = array();
		$alldata->lastsynced = 0;
		$alldata->institutionId = 0;
	}
	return $alldata;
}

function get_calendarevents($args)
{
	global $USER;
	$eventdata = get_allevents();
	$surveydatalocal = get_surveyresponsedatalocal();
	$filterdata = array();
	if(is_array($eventdata->events)){
		$allstatus = get_alleventstatus();
		$args['userids'] = array($USER->id);
		$args['timestart'] = strtotime($args['start']); 
		$args['timeend']	= strtotime($args['end']); 
		$filterdata = array_values(array_filter($eventdata->events, function ($event) use ($args) {
	    	return (in_array($event->teacherid, $args['userids']) && ($event->timestart > $args['timestart']) && ($event->timeend <= $args['timeend']));
		}));

		foreach ($filterdata as $key => $data) {
			$mystatus = array_values(array_filter($allstatus, function ($event) use ($data) {
		    	return ($data->id == $event->eventid);
			}));
			if(sizeof($mystatus) > 0){
				$laststatus = array_pop($mystatus);
				$data->status = $laststatus->status;
			}
			$surveyresponse_key = array_search($data->id, array_column($surveydatalocal, 'eventid'));
			if($surveyresponse_key !== false){
				$data->surveysubmitted = true;
			}
			$filterdata[$key] = $data;
		}

	}
	return $filterdata;
}

function get_alleventstatus() {
	global $CFG;
	$filedata = getFileFrom("institutionroot", "/", $CFG->eventstatus);
	if($filedata){
		$alldata = json_decode($filedata);
	}
	if(!is_array($alldata)){
		$alldata = array();
	}
	return $alldata;
}

function save_event_update_off($args) {
	global $API, $CFG;
	$allstatus = get_alleventstatus();
	$args->updatedtime = time();
	array_push($allstatus, $args);
	saveFileTo("institutionroot", "/", $CFG->eventstatus, $allstatus);	
	return true;
}

function sync_eventUpdate() {
	global $CFG, $API;
	if(has_internet()){
		$args = new stdClass();
		$args->allstatus = get_alleventstatus();
		$APIRES = $API->call("updateBulkEventStatus", $args);
		if($APIRES->code == 200){
			saveFileTo("institutionroot", "/", $CFG->eventstatus, $APIRES->data->event);
		}
	}
}

function sync_SurveyResponse()
{
	global $CFG, $API;
	if(has_internet()){
		$args = new stdClass();
		$args->allresponse = get_allsurveyresponse();
		$APIRES = $API->call("updateBulkSurveyResponse", $args);
		if($APIRES->code == 200){
			saveFileTo("institutionroot", "/", $CFG->surveyresponsedata, $APIRES->data->response);
		}
	}
}

function syncAllSurvey() {
	global $CFG, $API;
	if(has_internet()){
		sync_SurveyResponse();
		$reqdata = array();
		$response = $API->call("getSurveyList",$reqdata);
		if(is_object($response) && $response->code == 200) {
			saveFileTo("institutionroot", "/", $CFG->surveysdata, $response->data);
		}
	}
}

function getSurveyList() {
	global $CFG;
	syncAllSurvey();
	$filedata = getFileFrom("institutionroot", "/", $CFG->surveysdata);
	if($filedata){
		$alldata = json_decode($filedata);
	} else {
		$alldata = new stdClass();
		$alldata->surveys = array();
		$alldata->lastsynced = 0;
		$alldata->institutionId = 0;
	}
	return $alldata;
}

function get_allsurveyresponse() {
	global $CFG;
	$filedata = getFileFrom("institutionroot", "/", $CFG->surveyresponsedata);
	if($filedata){
		$alldata = json_decode($filedata);
	}
	if(!is_array($alldata)){
		$alldata = array();
	}
	return $alldata;
}

function get_surveyresponsedatalocal() {
	global $CFG;
	$filedata = getFileFrom("institutionroot", "/", $CFG->surveyresponsedatalocal);
	$alldata = array();
	if($filedata){
		$alldata = json_decode($filedata);
	}
	if(!is_array($alldata)){
		$alldata = array();
	}
	return $alldata;
}

function save_surveyresponse($args) {
	global $API, $CFG, $USER;
	$allresponse = get_allsurveyresponse();
	$args->userid = $USER->id;
	$args->updatedtime = time();
	array_push($allresponse, $args); 
	$allresponselocal = get_surveyresponsedatalocal();
	array_push($allresponselocal, $args); 
	saveFileTo("institutionroot", "/", $CFG->surveyresponsedata, $allresponse);	
	saveFileTo("institutionroot", "/", $CFG->surveyresponsedatalocal, $allresponse);	
	return true;
}

function get_surveyresponse($userid, $surveyid, $eventid=0) {
	global $API, $CFG, $USER;
	$allresponse = get_allsurveyresponse();
	$filter = (object)array("userid"=>$userid, "surveyid"=>$surveyid, "eventid"=>$eventid);
	$mystatus = array_values(array_filter($allresponse, function ($response) use ($filter) {
    	return ($response->userid == $filter->userid && $response->eventid == $filter->eventid && $response->surveyid == $filter->surveyid);
	}));
	$userresponse = array();
	if(sizeof($mystatus) > 0){
		$userresponse = array_pop($mystatus);
	}
	return $userresponse;
}

function online_getQuizes($formdata)
{
	global $API;
	if(has_internet()){
		$APIRES = $API->call("getQuizes", $formdata);
		if($APIRES->code == 200){
			return $APIRES->data;
		}
	}
	return null;
}

function classprofilereport_tr($topics, $addtitle = true){
  $html = '';
  $nextrowhtml = '';
  $allsubtopics = array();
  foreach ($topics as $key => $topic) {
    $html .= '<td colspan="'.$topic->childcount.'"><p class="text-center font-weight-bold mb-0" '.($topic->lang == 'ar'?'style="direction:rtl;"':'').' >'.($topic->name?$topic->name:"SubTopic ".$topic->section).'</p></td>';
  	$topic->subtopic = (array)$topic->subtopic;
    foreach ($topic->subtopic as $subtopic) {
      array_push($allsubtopics, $subtopic);
    }
  }
  if(sizeof($allsubtopics) > 0){
    $nextrowhtml .= classprofilereport_tr($allsubtopics, false);
  }
  if($addtitle){
    $html = '<tr><th>'.get_string("students", "form").'</th>'.$html.'</tr>';
  } else {
    $html = '<tr><th></th>'.$html.'</tr>';
  }
  $html .= $nextrowhtml;
  return $html;
}

function classprofilereport_td($topics, $scoredata, $xpsetting){
  global $CLASSPROFILEAVGDATA;
  $html = '';
  $allsubtopics = array();
  foreach ($topics as $key => $topic) {
  	$topic->subtopic = (array)$topic->subtopic;
    if(sizeof($topic->subtopic) > 0){
      foreach ($topic->subtopic as $subtopic) {
        array_push($allsubtopics, $subtopic);
      }
    } else {
      $colordataposition = 0;
      $color='grey';
      $colordata = '';
      $topicid = $topic->id;
      // $colordata .= '<pre>'.$topicid.print_r($scoredata, true).'</pre>';
      if(isset($scoredata->$topicid)){

        // $colordata .= '<pre>'.print_r($scoredata->$topicid).'</pre>';
        $ques_att = $scoredata->$topicid;
        $ques_att->totalmarks1 = number_format($ques_att->totalmarks, 2);
        $ques_att->totalmaxmarks1 = number_format($ques_att->totalmaxmarks, 2);
        $ques_att->totalmaxfraction1 = number_format($ques_att->totalmaxfraction, 2);
        $ques_att->maxfraction = number_format($xpsetting->roundon, 2);
        $ques_att->fraction = (($ques_att->totalattempt > 0)?($ques_att->totalmarks1/$ques_att->totalmaxmarks1)*$ques_att->totalmaxfraction1:0);
        $ques_att->fraction = number_format($ques_att->fraction, 2);
        $ques_att->maxmark = number_format($ques_att->totalmaxmarks, 2);
        $ques_att->percent = ($ques_att->totalmarks1/$ques_att->totalmaxmarks1)*100;
        if(!isset($CLASSPROFILEAVGDATA[$topicid])){
          $sbtdata = new stdClass();
          $sbtdata->gotscore = array();
          $sbtdata->finalpercent = array();
          $sbtdata->percentscore = array();
          $sbtdata->totalscore = array();
          $CLASSPROFILEAVGDATA[$topicid] = $sbtdata;
        }

        if($ques_att->totalattempt > 0){
          array_push($CLASSPROFILEAVGDATA[$topicid]->finalpercent, $ques_att->finalpercent);
          array_push($CLASSPROFILEAVGDATA[$topicid]->gotscore, $ques_att->fraction);
          array_push($CLASSPROFILEAVGDATA[$topicid]->percentscore, $ques_att->percent);
          array_push($CLASSPROFILEAVGDATA[$topicid]->totalscore, $ques_att->maxfraction);
          if($ques_att->finalpercent > 8.5){$color = 'blue'; $colordataposition=3;}
          else if($ques_att->finalpercent > 7.0){$color = 'lightgreen';$colordataposition=2;} 
          else if($ques_att->finalpercent > 5.0){$color = 'yellow';$colordataposition=1;} 
          else {$color = 'red';$colordataposition=0;} 
        }
        $colordata .= (number_format($ques_att->finalpercent, 2)).'/'.$ques_att->maxfraction.'<br/>';
      }
      $colordata .= '<span class="smalldot '.$color.'"></span>';
      $html .=  '<td class="text-center 0" >'.(($colordataposition == 0)?$colordata:'').'</td>
                <td class="text-center 1" >'.(($colordataposition == 1)?$colordata:'').'</td>
                <td class="text-center 2" >'.(($colordataposition == 2)?$colordata:'').'</td>
                <td class="text-center 3" >'.(($colordataposition == 3)?$colordata:'').'</td>';

    }
  }
  if(sizeof($allsubtopics) > 0){
    $nextrowhtml .= classprofilereport_td($allsubtopics, $scoredata, $xpsetting);
  }
  $html .= $nextrowhtml;
  return $html;
}

function classprofilereport_avg($topics, $xpsetting){
  global $CLASSPROFILEAVGDATA;
  $html = '';
  $allsubtopics = array();
  foreach ($topics as $key => $topic) {
  	$topic->subtopic = (array)$topic->subtopic;
    if(sizeof($topic->subtopic) > 0){
      foreach ($topic->subtopic as $subtopic) {
        array_push($allsubtopics, $subtopic);
      }
    } else {
      $colordataposition = 0;
      $color='grey';
      $colordata = '';
      $topicid = $topic->id;
      if(isset($CLASSPROFILEAVGDATA[$topicid])){
        $topicdata = $CLASSPROFILEAVGDATA[$topicid];
        $percentscore = $topicdata->percentscore;
        $gotscore = $topicdata->gotscore;
        $finalpercent = $topicdata->finalpercent;
        $totalscore = $topicdata->totalscore;
        $colordataposition = 0;
        $color='grey';
        $gotscoreaverage = 0;
        $percentaverage = 0;
        $totalscoreaverage = 0;
        if(sizeof($percentscore) > 0){
          $percentaverage = array_sum($finalpercent)/count($finalpercent);
          $gotscoreaverage = array_sum($gotscore)/count($gotscore);
          $totalscoreaverage = array_sum($totalscore)/count($totalscore);
          if($percentaverage > 8.5){$color = 'blue'; $colordataposition=3;}
          else if($percentaverage > 7.0){$color = 'lightgreen';$colordataposition=2;} 
          else if($percentaverage > 5.0){$color = 'yellow';$colordataposition=1;} 
          else {$color = 'red';$colordataposition=0;} 
        }
        // $colordata .= '<pre>'.print_r($topicdata, true).'</pre>';
        $colordata .= number_format($percentaverage,2).'/'.number_format($totalscoreaverage,2).'<br/>';
      }
      $colordata .= '<span class="smalldot '.$color.'"></span>';
      $html .=  '<td class="text-center 0" >'.(($colordataposition == 0)?$colordata:'').'</td>
                <td class="text-center 1" >'.(($colordataposition == 1)?$colordata:'').'</td>
                <td class="text-center 2" >'.(($colordataposition == 2)?$colordata:'').'</td>
                <td class="text-center 3" >'.(($colordataposition == 3)?$colordata:'').'</td>';

    }
  }
  if(sizeof($allsubtopics) > 0){
    $nextrowhtml .= classprofilereport_avg($allsubtopics, $xpsetting);
  }
  $html .= $nextrowhtml;
  return $html;
}

function offline_getQuizes($quizid){
	global $CFG, $FILES;
	$synceddata = get_awaitingsynceddata($CFG->syncedsynceddata);	
	// echo "<pre>";
	// print_r($quizid);
	// print_r($synceddata);
	// die;
	if(is_array($synceddata->data)){
		$found_key = array_search($quizid, array_column($synceddata->data, 'module'));
		// var_dump($found_key);
		// die;
		if($found_key !== false){
			$quizdata = $synceddata->data[$found_key];
	        $path = $FILES->get_offlinedatapath($quizdata);
	        $quizpath = "{$path}/{$quizid}.dat";
	        if($quiz = $FILES->getFiledata($quizpath)){
	        	$quiz = json_decode($quiz);
	        	$questionpath = "{$path}/{$quizid}/questions/{$quizid}.dat";
	        	if($quiz->allquestions = $FILES->getFiledata($questionpath)){
	        		$quiz->allquestions = json_decode($quiz->allquestions);
	        	}
	        	return $quiz;
	        }
		}
	}
	return null; 
}

function prepareOfflineContent($html="")
{
	global $FILES;
	if(empty($html)){
		return $html;
	}
	return $FILES->prepareOfflineContent($html);
}

function logout()
{
    unset($_SESSION['CURRENTUSERSESSION']);
    unset($_SESSION['MOODLESESSION']);
    session_destroy();
}

