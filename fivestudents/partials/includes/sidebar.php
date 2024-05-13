<?php
function sidebar(){
  global $wp, $CFG; 
  $MOODLESESSION = wp_get_moodle_session(); 

  // $current_url = home_url( add_query_arg( array(), $wp->request ) );
  $html = '<nav class="sidebar sidebar-offcanvas" id="sidebar"><ul class="nav">';
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">'.plus_get_string("dashboard", "site").'</span>
            </a>
          </li>';
if(current_user_can('manage_plususers')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/users" aria-expanded="false" aria-controls="ui-basic">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">'.plus_get_string("schools", "site").'</span>
            </a>
          </li>'; 
}
if(current_user_can('view_plusglobalusers')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/global-users" aria-expanded="false" aria-controls="ui-basic">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">'.plus_get_string("globalusers", "site").'</span>
            </a>
          </li>'; 
}
if(current_user_can('view_plusmanageevents') && $MOODLESESSION->INSTITUTION && $MOODLESESSION->INSTITUTION->disablecalendar != 1){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/events" aria-expanded="false" aria-controls="ui-basic">
              <i class="mdi mdi-calendar-multiple menu-icon"></i>
              <span class="menu-title">'.plus_get_string("events", "site").'</span>
            </a>
          </li>'; 
}
if(current_user_can('plus_viewcalendar') && $MOODLESESSION->INSTITUTION && $MOODLESESSION->INSTITUTION->disablecalendar != 1){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/calendar" aria-expanded="false" aria-controls="ui-basic">
              <i class="mdi mdi-calendar-multiple menu-icon"></i>
              <span class="menu-title">'.plus_get_string("calendar", "site").'</span>
            </a>
          </li>'; 
}
if(current_user_can('override_plussubscription')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/override-subscription" aria-expanded="false" aria-controls="ui-basic">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">'.plus_get_string("overridesubscription", "form").'</span>
            </a>
          </li>'; 
          
}
if(current_user_can('plus_viewteachers')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/teachers" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-account-multiple menu-icon"></i>
              <span class="menu-title">'.plus_get_string("teachers", "site").'</span>
            </a>
          </li>';

}
if(current_user_can('view_plussurveys') && $MOODLESESSION->INSTITUTION && $MOODLESESSION->INSTITUTION->disablecalendar != 1){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/surveys" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-account-multiple menu-icon"></i>
              <span class="menu-title">'.plus_get_string("surveys", "site").'</span>
            </a>
          </li>';

}
if(current_user_can('plus_viewgroups')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/groups" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-vector-circle menu-icon"></i>
              <span class="menu-title">'.plus_get_string("groups", "site").'</span>
            </a>
          </li>';

}
if(current_user_can('manage_plushomework')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/homework" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-drawing-box menu-icon"></i>
              <span class="menu-title">'.plus_get_string("homeworks", "site").'</span>
            </a>
          </li>';
}
if(current_user_can('manage_plusgroups')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/reports" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-drawing-box menu-icon"></i>
              <span class="menu-title">'.plus_get_string("reports", "form").'</span>
            </a>
          </li>';

}
if(current_user_can('manage_plustransections')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/payment-transection" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-database menu-icon"></i>
              <span class="menu-title">'.plus_get_string("transections", "site").'</span>
            </a>
          </li>';

}
if(current_user_can('view_plusdevicelist') && $MOODLESESSION->INSTITUTION && $MOODLESESSION->INSTITUTION->disableoffline != 1){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/devices-list" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-cellphone menu-icon"></i>
              <span class="menu-title">'.plus_get_string("devices", "site").'</span>
            </a>
          </li>';

}
if(current_user_can('view_plusresources')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/resources" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-database menu-icon"></i>
              <span class="menu-title">'.plus_get_string("resources", "site").'</span>
            </a>
          </li>';
}
if(current_user_can('view_plustraining')){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/training" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-database menu-icon"></i>
              <span class="menu-title">'.plus_get_string("training", "site").'</span>
            </a>
          </li>';

}
if((current_user_can('view_plusclaimedevent') || current_user_can('view_plusinprogressedevent') || current_user_can('view_pluscencllededevent')) && $MOODLESESSION->INSTITUTION && $MOODLESESSION->INSTITUTION->disablecalendar != 1){
  $html .= '<li class="nav-item">
            <a class="nav-link" href="/event-management" aria-expanded="false" aria-controls="form-elements">
              <i class="mdi mdi-database menu-icon"></i>
              <span class="menu-title">'.plus_get_string("eventmanagement", "site").'</span>
            </a>
          </li>';
}
  // $html .= '<li class="nav-item">
  //           <a class="nav-link" href="#subscriptions" aria-expanded="false" aria-controls="charts">
  //             <i class="mdi mdi-wallet-travel menu-icon"></i>
  //             <span class="menu-title">Subscriptions</span>
  //           </a>
  //         </li>';
  // $html .= '<li class="nav-item">
  //           <a class="nav-link" href="#tables" aria-expanded="false" aria-controls="tables">
  //             <i class="mdi mdi-message menu-icon"></i>
  //             <span class="menu-title">Messages</span>
  //             <!-- <i class="menu-arrow"></i>-->
  //           </a>
  //         </li>';
  // $html .= '<li class="nav-item">
  //           <a class="nav-link" href="#icons" aria-expanded="false" aria-controls="icons">
  //             <i class="icon-paper menu-icon"></i>
  //             <span class="menu-title">Reports</span>
  //             <!-- <i class="menu-arrow"></i>-->
  //           </a>
  //         </li>';
if(plus_is_admin_user()){
    $html .= '<li class="nav-item">
              <a class="nav-link" href="/wp-admin">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Admin Dashboard</span>
              </a>
            </li>';
  $html .= '<li class="nav-item">
            <a class="nav-link" href="'. $CFG->wwwroot . '/pages/forms/basic_elements.html">
              <i class="icon-paper menu-icon"></i>
              <span class="menu-title">Documentation</span>
            </a>
          </li>';
}
  $html .= '<li class="nav-item">
            <a class="nav-link" href="'.wp_logout_url( 'login' ).'" aria-expanded="false" >
              <i class="ti-power-off menu-icon"></i>
              <span class="menu-title">'.plus_get_string("logout", "site").'</span>
            </a>
          </li>';
// var_dump(is_admin());
// die;
  $html .= '</ul></nav>';
      echo $html;
    }