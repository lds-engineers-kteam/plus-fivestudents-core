<?php
function plus_add_students(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  //$formdata->id = plus_get_request_parameter("id", 0);
  $formdata->firstname = plus_get_request_parameter("firstname", "");
  $formdata->groupid = plus_get_request_parameter("id", 0);
  $formdata->userid = plus_get_request_parameter("userid", 0);
  $formdata->lastname = plus_get_request_parameter("lastname", "");
  $formdata->char = plus_get_request_parameter("char", "");
  $formdata->password=plus_get_request_parameter("password", "");
  $formdata->usertype=plus_get_request_parameter("usertype", 0);
  if(empty($formdata->groupid)){
    plus_redirect(home_url()."/groups");
    exit;
  }

 
  if(isset($_POST['saveuser'])){
    $APIRES=$MOODLE->get("createStudents",NULL,$formdata);
    plus_redirect(home_url()."/group-details/?id=".$formdata->groupid);
  }
    if(!empty($formdata->userid)){
     $APIRES1=$MOODLE->get("getStudentsById",NULL,$formdata);
     if(!empty($APIRES1->data)){
        $userdata=$APIRES1->data;
        $formdata->firstname =$userdata->firstname;
        $formdata->lastname =$userdata->lastname;
        $formdata->char =$userdata->alternatename;
        $formdata->userid=$userdata->id;
        $formdata->usertype=$userdata->usertype;
     }
   
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
                  <h4 class="card-title">'.plus_get_string("add", "form").'  '.plus_get_string("users", "site").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                   
                   
                    <div class="form-group row">
                      <label for="firstname" class="col-sm-2 col-form-label">'.plus_get_string("firstname", "student").'*</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="firstname" class="form-control" id="firstname" placeholder="'.plus_get_string("firstname", "student").'" value="'.$formdata->firstname.'" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="lastname" class="col-sm-2 col-form-label">'.plus_get_string("lastname", "student").'*</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="lastname" class="form-control" id="lastname" placeholder="'.plus_get_string("lastname", "student").'" value="'.$formdata->lastname.'" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="lastname" class="col-sm-2 col-form-label">'.plus_get_string("usertype", "student").'*</label>
                      <div class="col-sm-10">
                        <select required="required" name="usertype" class="form-control" id="usertype">
                          <option value="0" '.($formdata->usertype=="0"?'selected':"").' >Regular</option>
                          <option value="1" '.($formdata->usertype=="1"?'selected':"").' >Multiple</option>
                          <option value="2" '.($formdata->usertype=="2"?'selected':"").' >Staff</option>
                          <option value="3" '.($formdata->usertype=="3"?'selected':"").' >Free</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="jobtitle" class="col-sm-2 col-form-label">'.plus_get_string("charactername", "student").'*</label>
                      <div class="col-sm-10">
                        <input type="text" name="char" class="form-control" id="charname" placeholder="'.plus_get_string("charactername", "student").'" value="'.$formdata->char.'" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="password" required="required" class="col-sm-2 col-form-label">'.plus_get_string("password", "student").' '.($formdata->userid ? "" : "*" ).'</label>
                      <div class="col-sm-10">
                        <input type="password" name="password" class="form-control" id="password" placeholder="'.plus_get_string("password", "student").'" value="'.$formdata->password.'" '.($formdata->userid ? "" : "required").'>
                      </div>
                    </div>
                   <input type="hidden" name="id" value="'.$formdata->groupid.'"/>
                   <input type="hidden" name="userid" value="'.$formdata->userid.'"/>
                   
                    <button type="submit" name="saveuser" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="/group-details/?id='.$formdata->groupid .'" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '</div>
            </div>
          </div>';
  echo $html;
}