<?php
function navbar(){
  global $CFG;
  
  $CURRENTUSERSESSION = wp_get_current_user();
  $MOODLESESSION = wp_get_moodle_session();
  $current_user = wp_get_current_user();
  $current_user->teachid = $MOODLESESSION->INSTITUTION->member->userid;
  $current_user = (object)$current_user;

  if(is_string($MOODLESESSION)){
    return $MOODLESESSION;
  }

  $current_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  // $totalnotifications = $MOODLESESSION->data->totalnotifications;
  $userlang = "FR";
  if(isset($_GET['changelang'])){
    $userlang = $_GET['changelang'];
    plus_updateuserlang($userlang, $current_url);
  }
  if(isset($_GET['changeinstitution'])){
    $newinstitution = $_GET['changeinstitution'];
    plus_updateuserinstitution($newinstitution);
  }
  $notificationshtml = '';
  foreach ($MOODLESESSION->data->notifications as $key => $notification) {
    $notificationshtml .= '<div class="dropdown-divider"></div>
              <a class="dropdown-item preview-item" href="'.$CFG->wwwroot.'/notifications/?id='.$notification->id.'">
                <div class="preview-item-content">
                  <h6 class="preview-subject font-weight-normal">'.plus_get_string("event", "calendar").' '.plus_get_string("status_{$notification->status}", "calendar").' - '.plus_dateToFrench($notification->timestart).'</h6>
                  <p class="font-weight-light small-text mb-0 text-muted">
                    '.$notification->schoolname.' - '.$notification->teachername.'<br>
                    '.$notification->comment.'
                  </p>
                </div>
              </a>';
  }
  $userlang = plus_setuserlang();
  $alllang = array("FR"=>"French", "EN"=>"English");
  $userimg = $current_user->data->profileurl;
  $langdropdown = '';
  $langdropdown .= '<li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="langDropdown" data-toggle="dropdown">
              '.(isset($alllang[$userlang])?$alllang[$userlang]:$userlang).'
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">';
            foreach ($alllang as $key => $lang) {
              if($userlang == $key){continue;}
              $langdropdown .= '<a class="dropdown-item" href="'.plus_generatelangurl($key, $current_url).'">'.(isset($alllang[$lang])?$alllang[$lang]:$lang).'</a>';
            }
  $langdropdown .= ' </div>
          </li>';
  if(isset($MOODLESESSION->allinstitutions) && is_array($MOODLESESSION->allinstitutions) && !empty($MOODLESESSION->allinstitutions)){
    $userinstitution = plus_getuserinstitution();
    // $langdropdown .= '<li class="nav-item">'.$userinstitution.'</li>';
    $langdropdown .= '<li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="institutionDropdown" href="#" data-toggle="dropdown">
                '.(isset($MOODLESESSION->INSTITUTION->institution)?$MOODLESESSION->INSTITUTION->institution:$userinstitution).'
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">';
              foreach ($MOODLESESSION->allinstitutions as $key => $institution) {
                if($userinstitution == $institution->id){continue;}
                $langdropdown .= '<a class="dropdown-item" href="'.plus_generatecustomurl('changeinstitution',$institution->id).'">'.$institution->institution.'</a>';
              }
    $langdropdown .= '            </div>
            </li>';
  }
$html = '<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="'. $CFG->wwwroot . '/dashboard/"><img src="'. $CFG->wwwroot . '/images/Five-Students-Logo_big-1.webp" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="'. $CFG->wwwroot . '/dashboard/"><img src="'. $CFG->wwwroot . '/images/fivestudents.png" alt="logo"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <!--<ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul>-->
        <ul class="navbar-nav navbar-nav-right">
          '.$langdropdown.'
          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              '.(sizeof($MOODLESESSION->data->notifications) > 0?'<span class="count"></span>':'').'
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
              <p class="mb-0 font-weight-normal dropdown-header">'.plus_get_string("title", "notification").'</p>
              '.$notificationshtml.'
              <div class="dropdown-divider"></div>
              <a href="'.$CFG->wwwroot.'/notifications/"><h6 class="p-3 mb-0 text-center">'.plus_get_string("seeall", "notification").'</h6></a>
            </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              '.ucwords($current_user->display_name).' &nbsp; <img src="'.$userimg . '" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <!--<a class="dropdown-item">
                <i class="ti-settings text-primary"></i>
                Settings
              </a>-->
              <a class="dropdown-item" href="'.wp_logout_url( 'login' ).'">
                <i class="ti-power-off text-primary"></i>
                '.plus_get_string("logout", "site").'
              </a>
            </div>
          </li>
          <!--<li class="nav-item nav-settings d-none d-lg-flex">
            <a class="nav-link" href="#">
              <i class="icon-ellipsis"></i>
            </a>
          </li>-->
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <script>
    var USERLANG = "'.$userlang.'";
    </script>
    ';

    return $html;
}