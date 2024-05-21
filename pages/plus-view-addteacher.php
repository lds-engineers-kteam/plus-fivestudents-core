<?php
function plus_add_teacher(){
  global $CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->firstname = plus_get_request_parameter("firstname", "");
  $formdata->lastname = plus_get_request_parameter("lastname", "");
  $formdata->email = plus_get_request_parameter("email", "");
  $formdata->password = plus_get_request_parameter("password", "");
  $formdata->isadmin = plus_get_request_parameter("isadmin", 0);
  $formdata->subjects = plus_get_request_parameter("subjects", array());
  
  $errormessage = "";
  if(isset($_POST['saveteacher'])){
    $user_data = array(
     'user_pass' =>$formdata->password,
     'user_login' => $formdata->email,
     'user_nicename' => $formdata->firstname." ".$formdata->lastname,
     'user_email' => $formdata->email,
     'display_name' => $formdata->firstname." ".$formdata->lastname,
     'nickname' => $formdata->email,
     'first_name' => $formdata->firstname,
     'last_name' => $formdata->lastname,
     'role' => "tutor",
    );
    $usermeta = array(
      'firstname' => $formdata->firstname,
      'lastname' => $formdata->lastname,
      'email' => $formdata->email,
      'isadmin' => $formdata->isadmin,
      'subjects' => $formdata->subjects,
      'createddate' => time()  ,
      'name' => $formdata->firstname." ".$formdata->lastname,
      'accounttype' => "tutor"
    );
    $moodleuser = array(
      'id' => $formdata->id,
      'firstname' => $formdata->firstname,
      'lastname' => $formdata->lastname,
      'email' => $formdata->email,
      'isadmin' => $formdata->isadmin,
      'subjects' => $formdata->subjects,
    );
    if($existinguser = get_user_by( 'email', $formdata->email )){
      if(empty($formdata->id)){
        $errormessage = '<div class="alert alert-danger">'.plus_get_string("useralreadyexist", "form").'</div>';
      } else {
        $user_id=$existinguser->ID;
        $user_data['ID']=$user_id;
        if(empty($user_data['user_pass'])){ unset($user_data['user_pass']); }
        wp_update_user($user_data);
        foreach ($usermeta as $metakey => $metadata) {
          $updated = update_user_meta( $user_id, $metakey, $metadata );
        }
        if(!empty($formdata->password)){
          $moodleuser['password'] = $formdata->password;
        }
        $res1 = $MOODLE->get("CreateTeacher", "", $moodleuser);
        plus_redirect(home_url()."/teachers");
        exit;
      }
    } else {
      if(!empty($formdata->password)){
        $moodleuser['password'] = $formdata->password;
      }
      if($user_id = wp_insert_user($user_data)){
        foreach ($usermeta as $metakey => $metadata) {
          $updated = update_user_meta( $user_id, $metakey, $metadata );
        }
        $res1 = $MOODLE->get("CreateTeacher", "", $moodleuser);
        plus_redirect(home_url()."/teachers");
        exit;
      }
    }

  }

  if(!empty($formdata->id)){
    $APIRES = $MOODLE->get("GetUserById", null, array("id"=>$formdata->id));
    if($APIRES->code == 200 and $APIRES->data->id == $formdata->id){
      $formdata->firstname = $APIRES->data->firstname;
      $formdata->lastname = $APIRES->data->lastname;
      $formdata->email = $APIRES->data->email;
      $formdata->isadmin = $APIRES->data->isadmin;
      $formdata->subjects = explode(",", $APIRES->data->subjects);
    }
  }
  $Allsubjects = '';
  $subjects = array("math", "french", "arabic", "physics", "science", "mise");
  foreach ($subjects as $key => $subject) {
    $sid = $key+1;
    $Allsubjects .= '<label class="checkbox-inline"><input type="checkbox" name="subjects[]" '.(in_array($sid, $formdata->subjects)?"checked":"").' value="'.$sid.'"> '.plus_get_string("subject_{$subject}", "course").' </label>&nbsp; &nbsp; &nbsp; ';
  }

  $html='';
  // $html .=  '<div class="row">';
  // $html .=  '<div class="col-md-12 grid-margin">
  //             <div class="row mb-4">
  //               <div class="col-sm-9"><h3 class="font-weight-bold">Add User</h3>
  //               </div>
  //               <div class="col-sm-3 text-right"><a href="/users" class="btn btn-primary">Back</a></div>
  //             </div>
  //           </div>';
  // $html .=  '</div>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">'.plus_get_string("add", "form").' '.plus_get_string("teacher", "form").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="firstname" class="col-sm-2 col-form-label">'.plus_get_string("firstname", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="firstname" class="form-control" id="firstname" placeholder="'.plus_get_string("firstname", "form").'" value="'.$formdata->firstname.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="lastname" class="col-sm-2 col-form-label">'.plus_get_string("lastname", "form").' *</label>
                      <div class="col-sm-10">
                      <input type="text" required="required" name="lastname" id="lastname" class="form-control" placeholder="'.plus_get_string("lastname", "form").'" value="'.$formdata->lastname.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="email" class="col-sm-2 col-form-label">'.plus_get_string("email", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="email" class="form-control" id="email" placeholder="'.plus_get_string("email", "form").'" value="'.$formdata->email.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="password" class="col-sm-2 col-form-label">'.plus_get_string("password", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="password" '.(empty($formdata->id)?'required="required"':'').' name="password" class="form-control" id="password" placeholder="'.plus_get_string("password", "form").'" value="'.$formdata->password.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="isadmin" class="col-sm-2">Admin</label>
                      <div class="col-sm-10">
                        <input type="checkbox" '.($formdata->isadmin==1?' checked ':'').' name="isadmin" class="form-control1" id="isadmin" value="1">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="teachers" class="col-sm-2">'.plus_get_string("matter", "form").'</label>
                      <div class="col-sm-10">
                        '.$Allsubjects.'
                      </div>
                    </div>
                    '.$errormessage.'
                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <button type="submit" name="saveteacher" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="'.$CFG->wwwroot.'/teachers/" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '</div>
            </div>
          </div>';
  return $html;
}
