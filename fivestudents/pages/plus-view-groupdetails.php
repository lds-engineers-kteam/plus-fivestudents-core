<?php
function plus_view_groupdetails(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $groupid = plus_get_request_parameter("id", 0);
  $status = plus_get_request_parameter("status", 0);
  $userid = plus_get_request_parameter("userid", 0);
  $currentschoolyear = plus_get_request_parameter("currentschoolyear", 0);

  if(!empty($userid) && !empty($status) && in_array($status, array(1,2,3))){
    $APIRES = $MOODLE->get("UpdateStudentsStatus", null, array("groupid"=>$groupid, "userid"=>$userid, "status"=>$status));
  
    plus_redirect(home_url()."/group-details/?id=".$groupid);
    exit;
  }
  $APIRES = $MOODLE->get("GetGroupDetailsById", null, array("id"=>$groupid, "currentschoolyear"=>$currentschoolyear));
  $html='';
  // echo "<pre>";
  //   print_r($APIRES);
  //   die;  
  // $html .=  '<pre>'.print_r($APIRES, true).'</pre>';

  $enabledcourse = $APIRES->INSTITUTION->enablecourses;
  // echo "<script>console.log(" . $enablecourses = json_encode($enabledcourse) . ");</script>";
  // echo "<script>console.log(" . json_encode($APIRES) . ");</script>";
  if($enabledcourse){
      $new_array = [];
      foreach ($enabledcourse as $inner_array) {
        if(is_array($inner_array)){
          $new_array = array_merge($new_array, $inner_array);
        }
      }
  }

  if($APIRES->code == 200 and $APIRES->data->id == $groupid){
    $group = $APIRES->data;

  $html .=  '<div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body haveaction">
                    <h4 class="card-title">'.plus_get_string("groupdetails", "form").'</h4>
                    <a class="btn btn-warning card-body-action" href="'.$CFG->wwwroot.'/groups"><i class="mdi mdi-keyboard-backspace"></i></a>
                    <div class="row">
                      <div class="col-sm-2 col-xs-4"><strong>'.plus_get_string("name", "form").': </strong></div><div class="col-sm-4  col-xs-8">'.$group->name.'</div>
                      <div class="col-sm-2 col-xs-4"><strong>'.plus_get_string("noofstudent", "form").': </strong></div><div class="col-sm-4 col-xs-8">'.sizeof($group->users).'</div>
                      <div class="col-sm-2 col-xs-4"><strong>'.plus_get_string("level", "form").': </strong></div><div class="col-sm-4 col-xs-8">'.$group->grade.'</div>
                      <div class="col-sm-2 col-xs-4"><strong>'.plus_get_string("matter", "form").': </strong></div><div class="col-sm-4 col-xs-8">'.$group->coursename.'</div>
                      <div class="col-sm-2 col-xs-4"><strong>'.plus_get_string("teachers", "site").': </strong></div><div class="col-sm-4 col-xs-8">'.$group->teachers.'</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
  $html .=  '<div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body haveaction">
                    <h4 class="card-title">'.plus_get_string("students", "form").'</h4>
                    <div class="card-body-action">
                    '.(current_user_can('plus_viewusersubscription')?'
                      <a class="btn btn-primary" href="'.$CFG->wwwroot.'/student-restriction/?groupid='.$group->id.'">Restrict Module</a>
                      <a class="btn btn-primary" href="'.$CFG->wwwroot.'/add-students/?id='.$group->id.'"><i class="mdi mdi-plus"></i></a>
                      <span class="btn btn-primary copyLink" data-id="'.$group->id.'"><i class="mdi mdi-content-copy"></i></span>
                      <span data-id="'.$group->id.'" class="btn btn-primary copyCode"><i class="mdi mdi-content-copy"></i>'.plus_get_string("copycode", "form").'</span>
                      <span data-id="'.$group->id.'" class="btn btn-primary copyExamCode"><i class="mdi mdi-content-copy"></i>'.plus_get_string("copyexamcode", "form").'</span>
                      ':'').'
                      
                    </div>
                    <div class="table-responsive">
                      <table class="table table-striped plus_local_datatable "  id="studentlist">
                        <thead>
                          <tr>
                            <th>'.plus_get_string("lastname", "form").'</th>
                            <th>'.plus_get_string("firstname", "form").'</th>
                              <th>'.plus_get_string("username", "student").'</th>
                            <th>'.plus_get_string("chartname", "form").'</th>
                            <th>'.plus_get_string("level", "form").'</th>
                            <th>'.plus_get_string("passed", "form").'</th>
                            <th>'.plus_get_string("homeworkcompleted", "form").'</th>
                            <th>'.plus_get_string("totalhomeworkcompleted", "form").'</th>
                            <th>'.plus_get_string("creationdate", "form").'</th>
                            '.(current_user_can('plus_viewusersubscription')?'
                              <th>Subscription Type</th>
                              <th>Expiry</th>
                              <th>'.plus_get_string("status", "form").'</th>
                              <th></th>
                              <th></th>
                              ':'').'
                            '.(current_user_can('plus_editstudents')?'<th></th>':'').'
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>';
                        if(is_array($group->users) && !empty($group->users)){
                          foreach ($group->users as $key => $user) {
                            $edit_btn='<a href="'.$CFG->wwwroot.'/add-students/?id='.$groupid.'&userid='.$user->userid.'">'.plus_get_string("edit", "form").'</a>';
                            $html .=              '<tr>
                            <td>'.$user->lastname.'</td>
                            <td>'.$user->firstname.'</td>
                            <td>'.($user->userflag?$user->username:'').'</td>
                            <td>'.$user->alternatename.'</td>
                            <td>'.$user->grade.'</td>
                            <td>'.$user->passed.'</td>
                            <td>'.$user->completed.'</td>
                            <td>'.$user->totalcompleted.'</td>
                            <td>'.plus_dateToFrench($user->createddate, "d F Y h:i A").'</td>
                            ';
                            if(current_user_can('plus_viewusersubscription')){
                              $html .=              '
                              <td>'.$user->subscriptionType.'</td>
                              <td>'.$user->expiryDate.'</td>
                              ';
                              $html .=              '<td>';
                              if($user->status == 0){
                                $html .=              '<a href="'.$CFG->wwwroot.'/group-details/?id='.$groupid.'&userid='.$user->userid.'&status=1">'.plus_get_string("accept", "form").'</a> / ';
                                $html .=              '<a href="'.$CFG->wwwroot.'/group-details/?id='.$groupid.'&userid='.$user->userid.'&status=2">'.plus_get_string("reject", "form").'</a>';
                              } else {
                                if($user->status == 1){
                                  $html .=              plus_get_string("accepted", "form");
                                  $html .=              '&nbsp; &nbsp;<a href="'.$CFG->wwwroot.'/group-details/?id='.$groupid.'&userid='.$user->userid.'&status=3">'.plus_get_string("remove", "form").'</a>';
                                } else {
                                  $html .=              plus_get_string("rejected", "form");
                                }
                              }
                              $html .=              '</td>';
                              $html .=              '
                              
                                  '.($APIRES->INSTITUTION->paymenttype?'<td><a href="/student-subscription/?id='.$groupid.'&userid='.$user->userid.' " >Payment details</a></td>':'<td><a href="'.$CFG->wwwroot.'/student-date-subscription/?id='.$groupid.'&userid='.$user->userid.' " >Subscritption details</a></td>').'
                              <td><span class="btn btn-primary copyMigrationCode" data-id="'.$user->userid.'"><i class="mdi mdi-content-copy"></i></span></td>';
                            }
                            $html .=              '
                            '.(current_user_can('plus_editstudents')?'<td>'.$edit_btn.'</td>':'').'
                            <td><a href="'.$CFG->wwwroot.'/monthly-report/?groupid='.$groupid.'&userid='.$user->userid.'"> '.plus_get_string("heading", "report").' </a></td>
                                </tr>';
                          }
                        } else {
    $html .=              '<tr><td colspan="8" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
                        }
    $html .=  '         </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
  $html .=  '<div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body haveaction">
                    <h4 class="card-title">'.plus_get_string("homeworks", "site").'</h4>
                    <a class="btn btn-primary card-body-action" href="'.$CFG->wwwroot.'/add-homework?groupid='.$group->id.'"><i class="mdi mdi-plus"></i></a>
                    <div class="table-responsive">
                      <table class="table table-striped plus_local_datatable nosort" id="himeworklist">
                        <thead>
                          <tr>
                            <th>'.plus_get_string("action", "table").'</th>
                            <th>'.plus_get_string("duedate", "form").'</th>
                            <th>'.plus_get_string("publishdate", "form").'</th>
                            <th>'.plus_get_string("name", "form").'</th>
                            <th>'.plus_get_string("level", "form").'</th>
                            <th>'.plus_get_string("matter", "form").'</th>
                            <th>'.plus_get_string("semester", "form").'</th>
                            <th>'.plus_get_string("lesson", "form").'</th>
                            <th>'.plus_get_string("quiz", "form").'</th>
                            <th>'.plus_get_string("status", "form").'</th>
                            <th>'.plus_get_string("creationdate", "form").'</th>
                            <th>'.plus_get_string("action", "table").'</th>
                          </tr>
                        </thead>
                        <tbody>';
                        if(is_array($group->homeworks) && !empty($group->homeworks)){
                          foreach ($group->homeworks as $key => $homework) {
                              if(!empty($new_array) && !in_array($homework->courseid, $new_array)){continue;}else{
                              // echo "<pre>"; print_r($homework);die;
                              $html .= '<tr class="'.$homework->courseid.'"><td class="p-1 text-center"><a class="p-1 m-1" style="font-size:32px;" href="'.$CFG->wwwroot.'/add-homework?groupid='.$homework->groupid.'&id='.$homework->id.'"><i class="mdi mdi-lead-pencil"></i></a> &nbsp; <a class="p-1 m-1" style="font-size:32px;" href="'.$CFG->wwwroot.'/homework-report/?groupid='.$homework->groupid.'&homeworkid='.$homework->id.'"><i class="mdi mdi-library-books"></i></a></td><td>'.plus_dateToFrench($homework->duedate).'</td><td>'.plus_dateToFrench($homework->homeworkdate).'</td><td>'.$homework->name.'</td><td>'.$homework->grade.'</td><td>'.$homework->coursename.'</td><td>'.$homework->topicname.'</td><td>'.$homework->subtopicname.'</td><td>'.$homework->quizname.'</td><td>'.($homework->status?plus_get_string("statuspublish", "form"):plus_get_string("statusplanned", "form")).'</td><td>'.plus_dateToFrench($homework->createddate, "d F Y h:i A").'</td><td><a href="'.$CFG->wwwroot.'/add-homework?groupid='.$homework->groupid.'&id='.$homework->id.'"><i class="mdi mdi-lead-pencil"></i> '.plus_get_string("edit", "form").'</a> &nbsp; &nbsp; &nbsp; <a href="'.$CFG->wwwroot.'/homework-report/?groupid='.$homework->groupid.'&homeworkid='.$homework->id.'"><i class="mdi mdi-library-books"></i> '.plus_get_string("report", "form").'</a></td></tr>';
                          }
                         }
                        } else {
    $html .=              '<tr><td colspan="9" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
                        }
    $html .=  '         </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
$html .=  '<script>
$(document).ready(function(){
  $(".copyLink").click(function(){
    var groupid = $(this).data("id");
    var reqargs = {
        "groupid": groupid
    };
    var shortlinksetting = getAPIRequest("getGroupLinkID",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      console.log("response", response);
      if(response.data && response.data.shortLink){
        navigator.clipboard.writeText(response.data.shortLink);
        displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("copylinksuccess", "form").'", "info");
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("copylinkfailed", "form").'", "error");
      }
    });
  });
  $(".copyCode").click(function(){
    var groupid = $(this).data("id");
    var reqargs = {
        "groupid": groupid
    };
    navigator.clipboard.writeText(groupid);
    var shortlinksetting = getAPIRequest("getGroupCode",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      if(response.data && response.data.grouplinkid){
        navigator.clipboard.writeText(response.data.grouplinkid);
        displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("copycodesuccess", "form").'", "info");
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("copycodefailed", "form").'", "error");
      }
    });
  });
  $(".copyExamCode").click(function(){
    var groupid = $(this).data("id");
    var reqargs = {
        "groupid": groupid
    };
    navigator.clipboard.writeText(groupid);
    var shortlinksetting = getAPIRequest("getGroupExamCode",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      if(response.data && response.data.grouplinkid){
        navigator.clipboard.writeText(response.data.grouplinkid);
        displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("copycodesuccess", "form").'", "info");
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("copycodefailed", "form").'", "error");
      }
    });
  });
  $(".copyMigrationCode").click(function(){
    var userid = $(this).data("id");
    var reqargs = {
        "userid": userid
    };
    var shortlinksetting = getAPIRequest("generateMigrationCode",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      if(response.data && response.data.migrationCode){
        navigator.clipboard.writeText(response.data.migrationCode);
        displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("copycodesuccess", "form").'", "info");
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("copycodefailed", "form").'", "error");
      }
    });
  });
});

</script>';
  } else {
    $html.='<div class="alert alert-danger">'.plus_get_string("invalidrequest", "form").'</div>';

  } 
  return $html;
}