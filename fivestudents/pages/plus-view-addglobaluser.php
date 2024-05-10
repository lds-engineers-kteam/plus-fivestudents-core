<?php
function plus_view_addglobaluser(){
  global $wp;
  if ( !is_user_logged_in() || !current_user_can('view_plusaddglobaluser')) {
    return plus_view_noaccess();
  }
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->firstname = plus_get_request_parameter("firstname", "");
  $formdata->lastname = plus_get_request_parameter("lastname", "");
  $formdata->email = plus_get_request_parameter("email", "");
  $formdata->password = plus_get_request_parameter("password", "");
  $formdata->institutes = plus_get_request_parameter("institutes", array());
  $formdata->role = plus_get_request_parameter("role", "");

  $searchreq = new stdClass();
  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }
  if(isset($_POST['saveglobaluser'])){
    $user_data = array(
     'user_pass' =>$formdata->password,
     'user_login' => $formdata->email,
     'user_nicename' => $formdata->firstname." ".$formdata->lastname,
     'user_email' => $formdata->email,
     'display_name' => $formdata->firstname." ".$formdata->lastname,
     'nickname' => $formdata->email,
     'first_name' => $formdata->firstname,
     'last_name' => $formdata->lastname,
     'description' => "",
     'user_registered' => "",
     'role' =>$formdata->role
    );
    $usermeta = array(
     'institutes' => $formdata->institutes,
     'accounttype' => $formdata->role,
     'jobtitle' => $formdata->role,
    );
    if($existinguser = get_user_by( 'email', $formdata->email )){
      $user_id=$existinguser->ID;
      $user_data['ID']=$user_id;
      if(empty($user_data['user_pass'])){ unset($user_data['user_pass']); }
      wp_update_user($user_data);
      foreach ($usermeta as $metakey => $metadata) {
        $updated = update_user_meta( $user_id, $metakey, $metadata );
      }
      $userdata = plus_getuserformoodle($user_id);
      $userdata->institutes = $formdata->institutes;
      $res1 = $MOODLE->get("CreateGlobalUser", "internaladmin", $userdata);
    } else {
      if($user_id = wp_insert_user($user_data)){
        foreach ($usermeta as $metakey => $metadata) {
          $updated = update_user_meta($user_id, $metakey, $metadata );
        }
        $userdata = plus_getuserformoodle($user_id);
        $userdata->institutes = $formdata->institutes;
        $res1 = $MOODLE->get("CreateGlobalUser", "internaladmin", $userdata);
      }
    }
    plus_redirect(home_url()."/global-users");
    exit;
  }
  $html='';
  if(!empty($formdata->id)){
    $APIRES = $MOODLE->get("getGlobalUserById", null, array("id"=>$formdata->id));
    if($APIRES->code == 200 and $APIRES->data->id == $formdata->id){
      $formdata->id = $APIRES->data->id;
      $formdata->firstname = $APIRES->data->firstname;
      $formdata->lastname = $APIRES->data->lastname;
      $formdata->email = $APIRES->data->email;
      $formdata->role = $APIRES->data->role;
      $formdata->institutes = explode(",", $APIRES->data->institutes);
    } 
  }
  $institutionData = $MOODLE->get("institutionData", null,'');
  $allinstitution="";
  foreach ($institutionData->data as $instivalue) {
   $allinstitution.="<option value='".$instivalue->id."' ". (in_array($instivalue->id, $formdata->institutes)?"selected":"") .">".$instivalue->institution."</option>";
  }
  // $html .= '<pre>'.print_r($formdata, true).'</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("edituser", "form").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">

                    <div class="form-group row">
                      <label for="institutes" class="col-sm-2 col-form-label">'.plus_get_string("schools", "site").'</label>
                      <div class="col-sm-10">
                        <select  autocomplete="off" name="institutes[]" id="institutes" multiple required="required" class="form-control">
                              '.$allinstitution.'
                        </select>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="firstname" class="col-sm-2 col-form-label">'.plus_get_string("firstname", "form").' *</label>
                      <div class="col-sm-10">
                        <input  autocomplete="off" type="text" required="required" name="firstname" class="form-control" id="firstname" placeholder="'.plus_get_string("firstname", "form").'" value="'.$formdata->firstname.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="lastname" class="col-sm-2 col-form-label">'.plus_get_string("lastname", "form").' *</label>
                      <div class="col-sm-10">
                      <input  autocomplete="off" type="text" required="required" name="lastname" id="lastname" class="form-control" placeholder="'.plus_get_string("lastname", "form").'" value="'.$formdata->lastname.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="email" class="col-sm-2 col-form-label">'.plus_get_string("email", "form").' *</label>
                      <div class="col-sm-10">
                        <input  autocomplete="off" type="text" required="required" name="email" class="form-control" id="email" placeholder="'.plus_get_string("email", "form").'" value="'.$formdata->email.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="role" class="col-sm-2 col-form-label">'.plus_get_string("role", "form").'</label>
                      <div class="col-sm-10">
                        <select  autocomplete="off" name="role" id="role" required="required" class="form-control">
                          <option value="" >Select Role</option>
                          <option value="principal" '.($formdata->role == 'principal'?'selected':'').'>Principal</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="password" class="col-sm-2 col-form-label">'.plus_get_string("password", "form").' *</label>
                      <div class="col-sm-10">
                        <input  autocomplete="off" type="password" '.(empty($formdata->id)?'required="required"':'').' name="password" class="form-control" id="password" placeholder="'.plus_get_string("password", "form").'" value="'.$formdata->password.'">
                      </div>
                    </div>
                    
                    
                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <button type="submit" name="saveglobaluser" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="/global-users" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
                  </form>

                 


                </div>
              </div>
            </div>';
  $html .=  '</div>
            </div>
          </div>';
  return $html;
}



     

