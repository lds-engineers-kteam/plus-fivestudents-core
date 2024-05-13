<?php
function plus_add_user(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
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
  $formdata->paymenttype = plus_get_request_parameter("paymenttype", 0);
  $formdata->presubscription = plus_get_request_parameter("presubscription", 0);
  $formdata->region = plus_get_request_parameter("region", "");
  $formdata->provinces = plus_get_request_parameter("provinces", "");
  $formdata->totalkeys = plus_get_request_parameter("totalkeys", 0);
  $formdata->disablecalendar = plus_get_request_parameter("disablecalendar", 0);
  $formdata->disableoffline = plus_get_request_parameter("disableoffline", 0);
  $formdata->ispublic = plus_get_request_parameter("ispublic", 0);
  $formdata->flowtype = plus_get_request_parameter("flowtype", 0);
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
     'jobtitle' => $formdata->jobtitle,
     'paymenttype' => $formdata->paymenttype,
     'presubscription' => $formdata->presubscription,
     'region' => $formdata->region,
     'provinces' => $formdata->provinces,
     'totalkeys' => $formdata->totalkeys,
     'disablecalendar' => $formdata->disablecalendar,
     'disableoffline' => $formdata->disableoffline,
     'ispublic' => $formdata->ispublic,
     'flowtype' => $formdata->flowtype,
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
      $formdata->paymenttype =$APIRES->data->paymenttype;
      $formdata->region =$APIRES->data->region;
      $formdata->provinces =$APIRES->data->provinces;
      $formdata->totalkeys =$APIRES->data->totalkeys;
      $formdata->disablecalendar =$APIRES->data->disablecalendar;
      $formdata->disableoffline =$APIRES->data->disableoffline;
      $formdata->ispublic =$APIRES->data->ispublic;
      $formdata->flowtype  =$APIRES->data->flowtype;
    }
  }

  // echo "<br><br><br><pre>";
  // print_r($APIRES);
  // echo "</pre>";

  $allregionsdata = array(
    "Beni Mellal-Khénifra"=>array("Province de Béni-Mellal", "Province de Azilal", "Province de Fquih Ben Salah", "Khenifra Province", "Khouribga Province"),
    "Casablanca-Settat"=>array("Prefecture de Casablanca", "Mohammedia Prefecture", "Province de El Jadida", "Province de Nouaceur", "Province de Médiouna", "Province de Benslimane", "Province de Berrechid", "Settat Province", "Province de Sidi Bennour"),
    "Dakhla-Oued Ed-Dahab"=>array("Province de Oued Ed Dahab", "Province de Aousserd"),
    "Draa-Tafilalet"=>array("Errachidia Province", "Ouarzazate Province", "Midelt Province", "Tinghir Province", "Zagora Province"),
    "Fez-Meknes"=>array("Prefecture de Fez", "Meknes Prefecture", "Province de El Hajeb", "Province de Ifrane", "Moulay Yaâcoub Province", "Sefrou Province", "Province de Boulemane", "Taounate Province", "Province de Taza"),
    "Guelmim-Oued Noun"=>array("Guelmim Province", "Assa-Zag Province", "Tan-Tan Province", "Province de Sidi Ifni"),
    "Laayoune-Sakia El Hamra"=>array("Province de Laâyoune", "Boujdour Province", "Tarfaya Province", "Es-Semara Province"),
    "Marrakech-Safi"=>array("Prefecture de Marrakech", "Chichaoua Province", "Al Haouz Province", "Province de El Kelaâ des Sraghna", "Province de Essaouira", "Rehamna Province", "Safi Province", "Youssoufia Province"),
    "Oriental"=>array("Oujda-Angad Prefecture", "Province de Nador", "Driouch Province", "Jerada Province", "Province de Berkane", "Taourirt Province", "Guercif Province", "Province de Figuig"),
    "Rabat-Salé-Kénitra"=>array("Prefecture de Rabat", "Prefecture de Salé", "Skhirate-Témara Prefecture", "Province de Kenitra", "Province de Khemisset", "Province de Sidi Kacem", "Province de Sidi Slimane"),
    "Souss-Massa"=>array("Prefecture de Agadir Ida-Outanane", "Prefecture de Inezgane-Aït Melloul", "Province de Chtouka-Aït Baha", "Province de Taroudant", "Tiznit Province", "Tata Province"),
    "Tangier-Tetouan, Al Hoceima"=>array("Tangier-Assilah Prefecture", "Prefecture de M'diq-Fnideq", "Tetouan Province", "Fahs-Anjra Province", "Province de Larache", "Al Hoceïma Province", "Chefchaouen Province", "Province de Ouezzane"),
    "Other"=>array("Autre")
  );
  $allregions='';
  $allprovinces='';
  $selectedregion=!empty($formdata->region)?$formdata->region:'';
  $selectedprovinces=!empty($formdata->provinces)?$formdata->provinces:'';
  foreach ($allregionsdata as $key => $value) {
    $allregions.='<option value="'.$key.'" '.($selectedregion == $key?'selected':'').' >'.$key.'</option>';
  }
  if($selectedregion && isset($allregionsdata[$selectedregion]) && is_array($allregionsdata[$selectedregion])){
    $allprovincesdata = $allregionsdata[$selectedregion];
    foreach ($allprovincesdata as $key => $pvdata) {
      $allprovinces.='<option value="'.$pvdata.'" '.($selectedprovinces == $pvdata?'selected':'').' >'.$pvdata.'</option>';
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
                  <h4 class="card-title">'.plus_get_string("editschools", "form").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="institution" class="col-sm-2 col-form-label">'.plus_get_string("schools", "site").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="institution" class="form-control" id="institution" placeholder="Institution" value="'.$formdata->institution.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="role" class="col-sm-2 col-form-label">'.plus_get_string("accounttype", "form").' *</label>
                      <div class="col-sm-10">
                        <select required="required" name="role" id="role" class="form-control">
                          <option value="schooladmin" '.($formdata->role == 'schooladmin'?'selected':'').'>School Admin</option>
                          <option value="tutoringcenter" '.($formdata->role == 'tutoringcenter'?'selected':'').'>Tutoring Center</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="firstname" class="col-sm-2 col-form-label">'.plus_get_string("gradelevel", "site").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="firstname" class="form-control" id="firstname" placeholder="'.plus_get_string("gradelevel", "site").'" value="'.$formdata->firstname.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="lastname" class="col-sm-2 col-form-label">'.plus_get_string("group", "site").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="lastname" class="form-control" id="lastname" placeholder="'.plus_get_string("group", "site").'" value="'.$formdata->lastname.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="email" class="col-sm-2 col-form-label">'.plus_get_string("email", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="email" class="form-control" id="email" placeholder="'.plus_get_string("email", "form").'" value="'.$formdata->email.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="password" required="required" class="col-sm-2 col-form-label">'.plus_get_string("password", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="password" name="password" class="form-control" id="password" placeholder="'.plus_get_string("password", "form").'" value="'.$formdata->password.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="phone" class="col-sm-2 col-form-label">'.plus_get_string("phonenumber", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="phone" class="form-control" id="phone" placeholder="'.plus_get_string("phonenumber", "form").'" value="'.$formdata->phone.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="address" class="col-sm-2 col-form-label">'.plus_get_string("address", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="address" class="form-control" id="address" placeholder="'.plus_get_string("address", "form").'" value="'.$formdata->address.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="jobtitle" class="col-sm-2 col-form-label">'.plus_get_string("jobtitle", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="jobtitle" class="form-control" id="jobtitle" placeholder="'.plus_get_string("jobtitle", "form").'" value="'.$formdata->jobtitle.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="region" class="col-sm-2 col-form-label">Region</label>
                      <div class="col-sm-10">
                        <select name="region" class="form-control" id="region">'.$allregions.'</select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="provinces" class="col-sm-2 col-form-label">Provinces</label>
                      <div class="col-sm-10">
                        <select name="provinces" class="form-control" id="provinces">'.$allprovinces.'</select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="totalkeys" class="col-sm-2 col-form-label">'.plus_get_string("totalkeys", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="totalkeys" class="form-control" id="totalkeys" placeholder="'.plus_get_string("totalkeys", "form").'" value="'.$formdata->totalkeys.'">
                      </div>
                    </div>
                    <h4 class="card-title">Subscriptions</h4>
                    <div class="form-group row">
                      <label for="startdate" class="col-sm-2 col-form-label">'.plus_get_string("startdate", "form").'</label>
                      <div class="col-sm-10">
                        <input type="date" name="startdate" class="form-control" id="startdate" value="'.$formdata->startdate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="enddate" class="col-sm-2 col-form-label">'.plus_get_string("enddate", "form").'</label>
                      <div class="col-sm-10">
                        <input type="date" name="enddate" class="form-control" id="enddate" value="'.$formdata->enddate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="quantity" class="col-sm-2 col-form-label">'.plus_get_string("quantity", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="quantity" class="form-control" id="quantity" placeholder="'.plus_get_string("quantity", "form").'" value="'.$formdata->quantity.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="paymenttype" class="col-sm-2 col-form-label">'.plus_get_string("paymenttype", "form").'</label>
                      <div class="col-sm-10">
                        <select name="paymenttype" id="paymenttype">
                          <option value="0" '.($formdata->paymenttype == '0'?'selected':'').'>'.plus_get_string("schoolpay", "form").'</option>
                          <option value="1" '.($formdata->paymenttype == '1'?'selected':'').'>'.plus_get_string("parentpay", "form").'</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="paymenttype" class="col-sm-2 col-form-label">'.plus_get_string("freesubscription", "form").'</label>
                      <div class="col-sm-10">
                        <select name="presubscription" id="presubscription">
                          <option value="1" '.($formdata->presubscription == '1'?'selected':'').'>'.plus_get_string("yes", "form").'</option>
                          <option value="0" '.($formdata->presubscription == '0'?'selected':'').'>'.plus_get_string("no", "form").'</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="disablecalendar" class="col-sm-2 col-form-label">'.plus_get_string("disablecalendar", "form").'</label>
                      <div class="col-sm-10">
                        <select name="disablecalendar" id="disablecalendar">
                          <option value="1" '.($formdata->disablecalendar == '1'?'selected':'').'>'.plus_get_string("yes", "form").'</option>
                          <option value="0" '.($formdata->disablecalendar == '0'?'selected':'').'>'.plus_get_string("no", "form").'</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="flowtype" class="col-sm-2 col-form-label">'.plus_get_string("disableoffline", "form").'</label>
                      <div class="col-sm-10">
                        <select name="flowtype" id="flowtype">
                          <option value="1" '.($formdata->flowtype == '1'?'selected':'').'>'.plus_get_string("yes", "form").'</option>
                          <option value="0" '.($formdata->flowtype == '0'?'selected':'').'>'.plus_get_string("no", "form").'</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="disableoffline" class="col-sm-2 col-form-label">'.plus_get_string("flowtype", "institution").'</label>
                      <div class="col-sm-10">
                        <select name="disableoffline" id="disableoffline">
                          <option value="0" '.($formdata->disableoffline == '0'?'selected':'').'>'.plus_get_string("topic", "institution_flowtype").'</option>
                          <option value="1" '.($formdata->disableoffline == '1'?'selected':'').'>'.plus_get_string("component", "institution_flowtype").'</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="ispublic" class="col-sm-2 col-form-label">'.plus_get_string("ispublic", "institution").'</label>
                      <div class="col-sm-10">
                        <select name="ispublic" id="ispublic">
                          <option value="0" '.($formdata->ispublic == '0'?'selected':'').'>'.plus_get_string("no", "institution_ispublic").'</option>
                          <option value="1" '.($formdata->ispublic == '1'?'selected':'').'>'.plus_get_string("yes", "institution_ispublic").'</option>
                        </select>
                      </div>
                    </div>


                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <input type="hidden" name="institutionid" value="'.$formdata->institutionid.'"/>
                    <button type="submit" name="saveuser" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="/users" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '</div>
            </div>
          </div>';
$html .='<script>
  $(document).ready(function(){
    var selectedregion = null;    
    var allregionsdata = '.json_encode($allregionsdata).';
    $("#region").change(function(){
      var newoptions  ="";
      var selectedregion = $(this).val();
      var allprovincesdata = allregionsdata[selectedregion];
      console.log("allprovincesdata-", allprovincesdata);
      if(allprovincesdata && Array.isArray(allprovincesdata)){
        $.each( allprovincesdata, function( key, provinces ) {
          console.log("provinces- ", provinces);
          newoptions += \'<option value="\'+provinces+\'">\'+provinces+\'</option>\'
        });
      }
      $("#provinces").html(newoptions);
    });
    $("#region").trigger("change");
  })
  </script>';

  echo $html;
}