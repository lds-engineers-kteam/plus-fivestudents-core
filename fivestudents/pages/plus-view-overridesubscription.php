<?php
function plus_override_subscription(){
 global $wp,$CFG;
 require_once($CFG->dirroot . '/api/moodlecall.php');

 $current_user = wp_get_current_user();
 $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }
 
  $searchreq->institutionid = plus_get_request_parameter("institutionid", 0);
  $searchreq->groupid = plus_get_request_parameter("groupid", 0);
  $searchreq->userid = plus_get_request_parameter("userid", array());
  $searchreq->expiry_date =plus_get_request_parameter("expiry_date",null);;
  $searchreq->status =plus_get_request_parameter("status",0);;
  $APIRES_INS = $MOODLE->get("getInstitutionData", null, $searchreq);
  $APIRES_DATA = $MOODLE->get("getAllInstitutionData", null);
  // echo "<pre>";
  // print_r($APIRES_DATA);
  $institutiondata=$APIRES_INS->data;
  $selected_ins=null;
  $selected_group=null;
  $students=null;
  if($searchreq->groupid && $searchreq->institutionid){
    $APIRES_GROUPS_DETAILS = $MOODLE->get("GetGroupDetailsByIdOnlySubscribed", null, (object)array('id'=>$searchreq->groupid,'institutionid'=>$searchreq->institutionid));
    // echo "<pre>";
    // print_r($APIRES_GROUPS_DETAILS);
    $students=$APIRES_GROUPS_DETAILS->data->users;
    //GetGroupDetailsById
  }
  if(isset($_POST['filter'])){
    // echo "<pre>";
    //  print_r($searchreq);
   
    if(isset($_POST['userid'])){

      if (in_array(0,  $searchreq->userid))
        {
           $searchreq->userid=0;
       }else{
        $searchreq->userid=implode(',',$_POST['userid']);
       }
    }
    if(isset($searchreq->expiry_date)){
      $searchreq->expiry_date=strtotime($searchreq->expiry_date);
      $responsedata=$MOODLE->get("saveInstitutionData", null, $searchreq);
      // echo "<pre>";
      // print_r($responsedata);
  
      // if($responsedata->status){
      //   plus_redirect(home_url()."/override-subscription");
      //   exit();
      // }
     
    }
  }


  $html='<link rel="stylesheet" href="'. __FILE__ .'/public/../../../vendors/select2/select2.min.css">
  <link rel="stylesheet" href="'. __FILE__ .'/public/../../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
';
  // $html .= '<div class="table-responsive">'.is_object($APIRESWeeklyData)?json_encode($APIRESWeeklyData):$APIRESWeeklyData.'</div>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                <h4>Subscription Override Form</h4>
                 <!-- <h4 class="card-title">'.plus_get_string("school_weekly_report", "site").'</h4>-->
                  
                  <form class="forms-sample" id="override-subscription-form" method="POST">
                 
                   
                   <div class="form-group row">
                      <label for="school" class="col-sm-2 col-form-label">Institution</label>
                      <div class="col-sm-10">
                      <select class="form-control change-data" name="institutionid" id="institutionid" required="required">
                        <option value="">Select Institution</option>';
                       if(sizeof($institutiondata)){
                          foreach($institutiondata as $ins){
                            $select='';
                            if($ins->id==$searchreq->institutionid){
                              $selected_ins=$ins;
                              $select='selected';
                            }
                            $html .='<option value="'.$ins->id.'" '.$select.'>'.$ins->institution.'</option>';
                          }
                       }
                  $html .='    </select>
                        
                      </div>
                    </div>      
          
         <!-- <div class="form-group row">
                      <label for="gradelevel" class="col-sm-2 col-form-label">'.plus_get_string("gradelevel", "site").'</label>
                      <div class="col-sm-10">
                        <select name="gradelevel[]" id="gradelevel" class="form-control" multiple>';
             

             $html.= '</select> 
                      </div>
                    </div>-->';
                if($searchreq->institutionid){

             $html.='<div class="form-group row">
                      <label for="group" class="col-sm-2 col-form-label">'.plus_get_string("group", "site").'</label>
                      <div class="col-sm-10">
            <select class="form-control change-data" id="groupid" name="group" >';
                    if(isset($selected_ins)){
                      if(sizeof($selected_ins->groups)){
                         $html.='<option value="0" >All</option>';
                        foreach($selected_ins->groups as $group){
                           $select='';
                            if($group->id==$searchreq->groupid){
                              $selected_group=$group;
                              $select='selected';
                            }
                            $html.='<option value="'.$group->id.'" '.$select.'> '.$group->name.'</option>';
                        }
                      }
                    }

             $html.= '</select> 
                      </div>
                    </div>';
                    if($searchreq->groupid){

               $html.='<div class="form-group row">
                      <label for="group" class="col-sm-2 col-form-label">Student List</label>
                      <div class="col-sm-10">
            <select class="form-control" id="studentid" name="userid[]" multiple>';
                    if(isset($selected_group) && isset($students)){
                      if(sizeof($students)){
                         $html.='<option value="0" selected >All</option>';
                        foreach($students as $student){
                           // $select='';
                           //  if($student->userid==$searchreq->userid){
                           //    $selected_student=$student;
                           //    $select='selected';
                           //  }
                            $html.='<option value="'.$student->userid.'" > '.$student->alternatename.'</option>';
                        }
                      }
                    }

             $html.= '</select> 
                      </div>
                    </div>';

                    }

             $html .='   <div class="form-group row">
              <label for="showteacher" class="col-sm-2 col-form-label" >Expiry Date *</label>
              <div class="col-sm-10">
                <input type="datetime-local" name="expiry_date" class="form-control" required="required" >
            </div>
          </div>

         <!-- <div class="form-group row">
              <label for="showteacher" class="col-sm-2 col-form-label" >Status *</label>
              <div class="col-sm-10">
                <select name="status" class="form-control" required="required">
                  <option value="">Select Status</option>
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
            </div>
          </div>-->
                    ';

                }
            $html.= '
          
          
          <!--<div class="form-group row">
                      <label for="group" class="col-sm-2 col-form-label">'.plus_get_string("limit", "form").'</label>
                      <div class="col-sm-10">
            <select class="form-control" name="limit">
           
              ';

             $html.= '</select> 
                      </div>
                    </div>-->
          
          
          
          
                    ';
                      if($searchreq->institutionid){

                   $html.= ' <button type="submit" name="filter" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <button type="submit" name="cancel" class="btn btn-light">'.plus_get_string("cancel", "form").'</button>';
                  }
                   $html.= ' </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">Subscription Override List</h4>
                  <table class="table">
                    <thead>
                      <tr>
                        <th>S.NO</th>
                        <th>Institution</th>
                        <th>Group</th>
                        <th>Students</th>
                        <th>Expiry Date</th>
                      
                      </tr>
                    </thead>
                    <tbody>';
                    if(sizeof($APIRES_DATA->data)){
                      $i=1;
                      foreach($APIRES_DATA->data as $data){
                        $no_of_students='-';
                        $student_arr=explode(",",$data->userid);
                        $data->name=($data->name ? $data->name :"All");
                        // echo "<pre>";
                        // print_r($student_arr);
                        if(sizeof($student_arr)){
                          if(in_array(0,$student_arr)){
                           

                              $no_of_students='All';
                            
                          }else{
                             $no_of_students=count($student_arr);
                          }
                        }
                        $html .='<tr>
                        <td>'.$i++.'</td>
                        <td>'.$data->institution.'</td>
                        <td>'.$data->name.'</td>
                        <td>'.$no_of_students.'</td>
                        <td>'.plus_dateToFrench($data->expirydate, 'd F Y h:i A').'</td>
                  
                        </tr>';
                      }
                    }
                   
                  $html .='  </tbody>
                  </table>
<!--<div class="card-body-action">
  <button  class="btn btn-primary" onclick="htmltopdfexport(\'print_password\', \'User details\')"> '.plus_get_string("print", "form").'</button>-->
  <!--<button  class="btn btn-primary" onclick="exportData(\'weeklyreporttable\')"> Export</button>
</div> -->                 
                  ';
  // $html .=        '<div class="table-responsive" id="print_password">';
  //                   if(isset($password_data)){
  //                     $counter = 0;
  //                       $printcontent = '<div class="cutterpage">';
  //                     foreach($password_data as $row){
  //                       if($counter!=0 && $counter%30 == 0){
  //                         $printcontent .= '</div><div class="cutterpage">';
  //                       }
  //         $printcontent .= '<p  class="print-password-data box">
                    
  //                     <span>'.plus_get_string("firstname", "student").': </span>
  //                     <span>'.$row->firstname.'</span>
  //                    <br/> 
  //                     <span>'.plus_get_string("lastname", "student").': </span>
  //                     <span>'.$row->lastname.'</span>
  //                    <br/> 
  //                     <span>'.plus_get_string("group", "site").': </span>
  //                     <span>'.$row->groupname.'</span>
  //                    <br/> 
  //                     <span>'.plus_get_string("username", "student").':</span>
  //                     <span>'.$row->username.'</span>
  //                    <br/> 
  //                     <span>'.plus_get_string("password", "student").': </td>
  //                     <span>'.$row->password.'</span>
                   
  //         </p>';              
  //         $counter++;

  //                     }
  //                       $printcontent .= '</div>';
  //                       $html .= $printcontent;
  //                   }
                    
  // $html .=        '</div>';
  //$html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "user");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '
 
  </div>
            </div>
          </div><script src="'.__FILE__ .'/public/../../../vendors/select2/select2.min.js"></script><script src="'.__FILE__ .'/public/../../../js/select2.js"></script>';
          $html.= ' <script> $(function(){
          var all_grade_level = '.json_encode(@$all_grade_level).';
          var allgroup=new Array();
       
          $("#gradelevel").change(function(){
              var gradelevelid = $(this).val();
              console.log("gradelevelid", gradelevelid);
              var selectedgroup = all_grade_level.filter(x => gradelevelid.includes(x.id));
              if(gradelevelid.includes("0")){
                selectedgroup = all_grade_level;
              }
              console.log("selectedgroup- ", selectedgroup)
              var newoptions = \'<option value="0">'.plus_get_string("all", "site").'</option>\';
              $.each(selectedgroup, function(key,grade){
                if(grade && Array.isArray(grade.group)){
                  allgroup += grade.group;
                  $.each( grade.group, function( key1, group ) {
                    newoptions += \'<option value="\'+group.id+\'">\'+group.name+\'</option>\'
                  });
                } else {
                  allgroup = [];
                }
              });
            $("#group").html(newoptions);
           
            });

            //change data
            $("body").on("change",".change-data",function(){
                var institutionid=getInstituteId();
                var groupid=getGroupId();
                if(institutionid !="" && groupid && groupid !=""){
                  console.log("institutionid and groupo id not empty");
                   window.location.href=`'.$CFG->wwwroot.'/override-subscription/?institutionid=${institutionid}&groupid=${groupid}`;
                }
                 else if(institutionid !="" ){
                  console.log("institutionid ",institutionid);
                  window.location.href='.$CFG->wwwroot.'`/override-subscription/?institutionid=${institutionid}`;
                }
              });
              function getInstituteId(){
                return $("#institutionid").val();
              }
              function getGroupId(){
                 return $("#groupid").val();
              }
              var today = new Date().toISOString().slice(0, 16);
              var expiryelement = document.getElementsByName("expiry_date");
              console.log("today- ", today)
              console.log("expiryelement- ", expiryelement)
              if(expiryelement.length){
                expiryelement[0].min = today;
              }
});</script>';
  echo $html;
}