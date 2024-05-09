<?php
function plus_assessment_report(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager();
/*$formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->institutionid = plus_get_request_parameter("institutionid", 0);
  $formdata->institution = plus_get_request_parameter("institution", "");
  $formdata->role = plus_get_request_parameter("role", "");
  $formdata->firstname = plus_get_request_parameter("firstname", "");
  $formdata->lastname = plus_get_request_parameter("lastname", "");
  $formdata->email = plus_get_request_parameter("email", "");
  $formdata->password = plus_get_request_parameter("password", "");
  $formdata->phone = plus_get_request_parameter("phone", "");
  $formdata->address = plus_get_request_parameter("address", "");
  $formdata->jobtitle = plus_get_request_parameter("jobtitle", "");
  $formdata->startdate = plus_get_request_parameter("startdate", "");
  $formdata->enddate = plus_get_request_parameter("enddate", "");
  $formdata->quantity = plus_get_request_parameter("quantity", 0);
  if(isset($_POST['saveuser'])){
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
     'role' => $formdata->role
    );
    $usermeta = array(
     'institution' => $formdata->institution,
     'accounttype' => $formdata->role,
     'address' => $formdata->address,
     'phone' => $formdata->phone,
     'contactname' => $formdata->firstname." ".$formdata->lastname,
     'jobtitle' => $formdata->jobtitle
    );
    if($existinguser = get_user_by( 'email', $formdata->email )){
      // print_r($existinguser);
      $user_id=$existinguser->ID;
      $user_data['ID']=$user_id;

      if(empty($user_data['user_pass'])){ unset($user_data['user_pass']); }
      wp_update_user($user_data);
      foreach ($usermeta as $metakey => $metadata) {
        $updated = update_user_meta( $user_id, $metakey, $metadata );
      }
      $userdata = plus_getuserformoodle($user_id);
      $userdata->institutionid = $formdata->institutionid;
      // print_r($userdata);
      // die;
      $res1 = $MOODLE->get("CreateUser", "internaladmin", $userdata);
    } else {
      if($user_id = wp_insert_user($user_data)){
        foreach ($usermeta as $metakey => $metadata) {
          $updated = update_user_meta( $user_id, $metakey, $metadata );
        }
        $userdata = plus_getuserformoodle($user_id);
        $res1 = $MOODLE->get("CreateUser", "internaladmin", $userdata);
      }
    }
    plus_redirect(home_url()."/users");
    exit;
  }

  if(!empty($formdata->id)){
    $APIRES = $MOODLE->get("GetUserById", null, array("id"=>$formdata->id));
    if($APIRES->code == 200 and $APIRES->data->id == $formdata->id){
      $formdata->institutionid = $APIRES->data->institutionid;
      $formdata->institution = $APIRES->data->institution;
      $formdata->role = $APIRES->data->role;
      $formdata->firstname = $APIRES->data->firstname;
      $formdata->lastname = $APIRES->data->lastname;
      $formdata->email = $APIRES->data->email;
      $formdata->phone = $APIRES->data->phone;
      $formdata->address = $APIRES->data->address;
      $formdata->jobtitle = $APIRES->data->jobtitle;
      $formdata->startdate = date("Y-m-d", $APIRES->data->startdate);
      $formdata->enddate = date("Y-m-d", $APIRES->data->enddate);
      $formdata->quantity = $APIRES->data->quantity;
    }
  }*/
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
/*$html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Add User</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="institution" class="col-sm-2 col-form-label">Institution *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="institution" class="form-control" id="institution" placeholder="Institution" value="'.$formdata->institution.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="role" class="col-sm-2 col-form-label">Account type *</label>
                      <div class="col-sm-10">
                        <select required="required" name="role" id="role" class="form-control">
                          <option value="schooladmin" '.($formdata->role == 'schooladmin'?'selected':'').'>School Admin</option>
                          <option value="tutoringcenter" '.($formdata->role == 'tutoringcenter'?'selected':'').'>Tutoring Center</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="firstname" class="col-sm-2 col-form-label">First Name *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="firstname" class="form-control" id="firstname" placeholder="First Name" value="'.$formdata->firstname.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="lastname" class="col-sm-2 col-form-label">Last Name *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="lastname" class="form-control" id="lastname" placeholder="Last Name" value="'.$formdata->lastname.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="email" class="col-sm-2 col-form-label">Email *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="email" class="form-control" id="email" placeholder="Email" value="'.$formdata->email.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="password" required="required" class="col-sm-2 col-form-label">Password *</label>
                      <div class="col-sm-10">
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" value="'.$formdata->password.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="phone" class="col-sm-2 col-form-label">Phone number</label>
                      <div class="col-sm-10">
                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone number" value="'.$formdata->phone.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="address" class="col-sm-2 col-form-label">Address</label>
                      <div class="col-sm-10">
                        <input type="text" name="address" class="form-control" id="address" placeholder="Address" value="'.$formdata->address.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="jobtitle" class="col-sm-2 col-form-label">Job Title</label>
                      <div class="col-sm-10">
                        <input type="text" name="jobtitle" class="form-control" id="jobtitle" placeholder="Job Title" value="'.$formdata->jobtitle.'">
                      </div>
                    </div>
                    <h4 class="card-title">Subscriptions</h4>
                    <div class="form-group row">
                      <label for="startdate" class="col-sm-2 col-form-label">Start Date</label>
                      <div class="col-sm-10">
                        <input type="date" name="startdate" class="form-control" id="startdate" value="'.$formdata->startdate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="enddate" class="col-sm-2 col-form-label">End Date</label>
                      <div class="col-sm-10">
                        <input type="date" name="enddate" class="form-control" id="enddate" value="'.$formdata->enddate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="quantity" class="col-sm-2 col-form-label">Quantity</label>
                      <div class="col-sm-10">
                        <input type="text" name="quantity" class="form-control" id="quantity" placeholder="Quantity" value="'.$formdata->quantity.'">
                      </div>
                    </div>

                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <input type="hidden" name="institutionid" value="'.$formdata->institutionid.'"/>
                    <button type="submit" name="saveuser" class="btn btn-primary mr-2">Save</button>
                    <a href="/users" class="btn btn-light">Back</a>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '</div>
            </div>
          </div>';*/

//$html .=   '<div class="row">';
  $html .=  '     
    <div class="row">
      <div class="col-sm-6">
      <button type="button" class="btn btn-primary" style="padding: 8px 8px;font-size: 10px;">Classe :</button>
      <a href="#" style="font-size:10px;">3AC/1</a>
      <button type="button" class="btn btn-primary" style="padding: 8px 8px;font-size: 10px;">Leçon :</button>
      <a href="#" style="font-size:10px;">Pythagore</a>
      <button type="button" class="btn btn-primary" style="padding: 8px 8px;font-size: 10px;">Tentative :</button>
      <a href="#" style="font-size:10px;">1ère tentative</a>
      </div>
      <div class="col-sm-6">
      <span style="font-size:10px;">Non atteint</span><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span>
      <span style="font-size:10px;">Satisfaisant</span><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span>
      <span style="font-size:10px;">Noncommencé</span><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #ffebf9"></span>
    </div>  
 </div>
    <div class="row">
      <div class="col-sm-6">
      <button type="button" class="btn btn-primary" style="padding: 8px 8px;font-size: 10px;">Semestre :</button>
      <a href="#" style="font-size:10px;">Semestre 1</a>
      <button type="button" class="btn btn-primary" style="padding: 8px 8px;font-size: 10px;">Mode :</button>
      <a href="#" style="font-size:10px;">Mission</a>
      <button type="button" class="btn btn-primary" style="padding: 8px 8px;font-size: 10px;">Quizzes :</button>
      <a href="#" style="font-size:10px;">3</a>
      </div>
      <div class="col-sm-6">
      <span style="font-size:10px;">Minimalement atteint</span><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span>
      <span style="font-size:10px;">Dépassé</span><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span>
      <span style="font-size:10px;">Noncomplété</span><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span>
     </div>
    </div><br/><br/>';
    $html .='<table border="1" style="border-color: #e0ebeb;table-layout: fixed; width:100%;">
    <tr>
        <td colspan="4">Enseignant	:	KHALIL	XXX</td>
        <td style="padding-bottom:20px;padding-right:10px;"><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">Ponderation</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">Moyenne de la classe</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">Ismail</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">SARA</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">Aya</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">HAJAR</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">SAFA</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">CHAHD</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">FATIMAZAHRA</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">AYMEN</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">HATIM</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">Mohammed taha</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">MERYEM</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">IMRANE</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">OMAR</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">FAYSAL</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">OTHMANE</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">Marwa</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">OUMAMA</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">NADA</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">Zyad</span></td>
        <td><span style="font-size:10px;-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;
        writing-mode: vertical-rl;transform: rotate(180deg);">SOFIA</span></td>  
    </tr>';
    $html .='<table border="1" style="border-color: #e0ebeb;table-layout: fixed; width:100%;">
    <tr>
      <td style="font-size:9px;padding:5px;" colspan="4">Q1</td>
      <td style="font-size:9px;padding:5px;">3.00</td>
      <td style="font-size:9px;padding:5px;">2.09<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
      <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
      <td style="font-size:9px;padding:5px;">2.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">2.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">0.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">2.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">2.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
    </tr>
      <tr>
        <td scope="row" style="font-size:9px;padding:5px;" colspan="4">Q2</td>
        <td style="font-size:9px;padding:5px;">3.00</td>
        <td style="font-size:9px;padding:5px;">1.43<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">0.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">2.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">0.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">0.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">0.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">0.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      </tr>
      <tr>
        <td scope="row" style="font-size:9px;padding:5px;" colspan="4">Q3</td>
        <td style="font-size:9px;padding:5px;">3.00</td>
        <td style="font-size:9px;padding:5px;">1.71<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">1.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">1.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">0.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">0.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">1.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">2.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">1.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">0.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">1.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
      </tr>
      <tr>
        <td scope="row" style="font-size:9px;padding:5px;" colspan="4">Q4</td>
        <td style="font-size:9px;padding:5px;">3.00</td>
        <td style="font-size:9px;padding:5px;">1.51<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">1.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">0.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">1.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">1.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">1.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">2.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">1.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>        
        <td style="font-size:9px;padding:5px;">1.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>        
        <td style="font-size:9px;padding:5px;">0.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">2.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">1.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      </tr>
      <tr>
      <td style="font-size:9px;padding:5px;" colspan="4">Q5</td>
      <td style="font-size:9px;padding:5px;">4.00</td>
      <td style="font-size:9px;padding:5px;">1.93<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">1.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">1.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">1.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
      <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
      <td style="font-size:9px;padding:5px;">2.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">4.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">4.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">2.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">3.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">2.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
      <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
      <td style="font-size:9px;padding:5px;">2.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">2.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">0.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>      
      <td style="font-size:9px;padding:5px;">2.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">2.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
      <td style="font-size:9px;padding:5px;">2.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
    </tr>
      <tr>
        <td scope="row" style="font-size:9px;padding:5px;" colspan="4">Q6</td>
        <td style="font-size:9px;padding:5px;">4.00</td>
        <td style="font-size:9px;padding:5px;">3.08<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">0.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">4.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">4.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">2.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">1.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">3.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
      </tr>
      <tr>
        <td scope="row" style="font-size:9px;padding:5px;" colspan="4">Note</td>
        <td style="font-size:9px;padding:5px;">20.00</td>
        <td style="font-size:9px;padding:5px;">11.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">7.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">9.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">13.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#fff53b"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">11.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">18.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span></td>
        <td style="font-size:9px;padding:5px;">14.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">11.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">13.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">8.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">14.75<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#fff53b"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;"><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #878f99"></span></td>
        <td style="font-size:9px;padding:5px;">12.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">16.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span></td>
        <td style="font-size:9px;padding:5px;">4.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">7.00<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#FF0000"></span></td>
        <td style="font-size:9px;padding:5px;">14.50<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#fff53b"></span></td>
        <td style="font-size:9px;padding:5px;">16.25<br/><span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color:#fff53b"></span></td>
      </tr>
  </table>
  ';
  // $html .=  '</div>'; 'Non atteint<div class="me-3" style="width:20px; height:20px; border-radius: 50%; background-color: #FF0000"></div>';
  return $html;
}