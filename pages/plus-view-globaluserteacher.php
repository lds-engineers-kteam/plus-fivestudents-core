<?php
function plus_view_globaluserteacher(){
  global $CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  
  if (!current_user_can('view_pluseditglobaluserteacher')) {
    return plus_view_noaccess();
  }

  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->teachers = plus_get_request_parameter("teachers", array());
  if(isset($_REQUEST['cancel'])){
    plus_redirect("/global-users");
    exit;
  }


  $html='';
  $allteachers = array();
  $teacherids = array();
  if(!empty($formdata->id)){
    $APIRES = $MOODLE->get("getGlobalUserTeacher", null, array("id"=>$formdata->id));
    if($APIRES->code == 200 and $APIRES->data->id == $formdata->id){
      $formdata->id = $APIRES->data->id;
      $allteachers = $APIRES->data->allteachers;
      $teachers = $APIRES->data->teachers;
      $teacherids = $APIRES->data->teacherids;
    }
    // $html .= '<pre>'.print_r($APIRES->data, true).'</pre>';
  }
  $strallteachers="";
  foreach ($allteachers as $teacher) {
   $strallteachers.="<option value='".$teacher->id."' ". (in_array($teacher->id, $teacherids)?"selected":"") .">".$teacher->firstname." ".$teacher->lastname."</option>";
  }
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("teachers", "site").'</h4>
                  <a class="btn btn-warning card-body-action" href="'.$CFG->wwwroot.'/global-users"><i class="mdi mdi-keyboard-backspace"></i></a>
                  ';
  $html .=        '<div class="table-responsive">
                    <table class="table table-striped plus_local_datatable">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("userid", "form").'</th>
                          <th>'.plus_get_string("firstname", "form").'</th>
                          <th>'.plus_get_string("lastname", "form").'</th>
                          <th>'.plus_get_string("email", "form").'</th>
                          <th>'.plus_get_string("schools", "site").'</th>
                          <th>'.plus_get_string("roles", "form").'</th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_array($teachers)){
                foreach ($teachers as $key => $user) {
                  $html .=  '<tr>
                              <td class="py-1">'.$user->userid.'</td>
                              <td class="py-1">'.$user->firstname.'</td>
                              <td class="py-1">'.$user->lastname.'</td>
                              <td class="py-1">'.$user->email.'</td>
                              <td class="py-1">'.$user->institution.'</td>
                              <td class="py-1">'.$user->role.'</td>
                              </tr>';
                }
              }
            $html .=  '</tbody>
                    </table>
                  </div>';
  $html .=      '</div>
              </div>
            </div>';
  $html .=  '
          </div>
        </div>
      </div>';
  return $html;
}



     
