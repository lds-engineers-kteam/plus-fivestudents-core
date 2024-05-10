<?php
  require_once("../../config.php");
  require_login();
  require_internat();
  $groupid = optional_param("id", 0);
  $status = optional_param("status", 0);
  $userid = optional_param("userid", 0);
  $currentschoolyear = optional_param("currentschoolyear", 0);

  if(empty($groupid)){
    redirect("{$CFG->wwwroot}/groups/", "missing required parameter", 'error');
  }
  $group = online_GetGroupDetailsById($groupid, $currentschoolyear);
  $localgroup = get_group($groupid);
  if(empty($group) || empty($localgroup)){
    redirect("{$CFG->wwwroot}/groups/", "Group Not Found", 'error');
  }
  $OUTPUT->loadjquery();
  echo $OUTPUT->header();
  $html  = '';
  // echo "<pre>";
  // print_r($localgroup);
  // print_r($group);
  // echo "</pre>";
  // $html  .= '<pre>'.print_r($group->homeworks, true).'</pre>';
  $html .=  '<div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body haveaction">
                    <h4 class="card-title">'.get_string("groupdetails", "form").'</h4>
                    <a class="btn btn-warning card-body-action" href="'.$CFG->wwwroot.'/groups"><i class="mdi mdi-keyboard-backspace"></i></a>
                    <div class="row">
                      <div class="col-sm-2 col-xs-4"><strong>'.get_string("name", "form").': </strong></div><div class="col-sm-4  col-xs-8">'.$localgroup->name.'</div>
                      <div class="col-sm-2 col-xs-4"><strong>'.get_string("noofstudent", "form").': </strong></div><div class="col-sm-4 col-xs-8">'.sizeof($group->users).'</div>
                      <div class="col-sm-2 col-xs-4"><strong>'.get_string("level", "form").': </strong></div><div class="col-sm-4 col-xs-8">'.$localgroup->grade.'</div>
                      <div class="col-sm-2 col-xs-4"><strong>'.get_string("matter", "form").': </strong></div><div class="col-sm-4 col-xs-8">'.$localgroup->coursename.'</div>
                      <div class="col-sm-2 col-xs-4"><strong>'.get_string("teachers", "site").': </strong></div><div class="col-sm-4 col-xs-8">'.$localgroup->teachers.'</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
  $html .=  '<div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body haveaction">
                    <h4 class="card-title">'.get_string("students", "form").'</h4>
                    <div class="card-body-action">
                    </div>
                    <div class="table-responsive">
                      <table class="table table-striped plus_local_datatable "  id="studentlist">
                        <thead>
                          <tr>
                            <th>'.get_string("lastname", "form").'</th>
                            <th>'.get_string("firstname", "form").'</th>
                              <th>'.get_string("username", "student").'</th>
                            <th>'.get_string("chartname", "form").'</th>
                            <th>'.get_string("level", "form").'</th>
                            <th>'.get_string("passed", "form").'</th>
                            <th>'.get_string("homeworkcompleted", "form").'</th>
                            <th>'.get_string("totalhomeworkcompleted", "form").'</th>
                            <th>'.get_string("creationdate", "form").'</th>
                            <th>'.get_string("status", "form").'</th>
                          </tr>
                        </thead>
                        <tbody>';
                        if(is_array($group->users) && !empty($group->users)){
                          foreach ($group->users as $key => $user) {
                              // $edit_btn='<a href="'.$CFG->wwwroot.'/add-students/?id='.$groupid.'&userid='.$user->userid.'">Edit</a>';
                            // print_r($user);die;
    $html .=              '<tr>
    <td>'.$user->lastname.'</td>
    <td>'.$user->firstname.'</td>
    <td>'.($user->userflag?$user->username:'').'</td>
    <td>'.$user->alternatename.'</td>
    <td>'.$user->grade.'</td>
    <td>'.$user->passed.'</td>
    <td>'.$user->completed.'</td>
    <td>'.$user->totalcompleted.'</td>
    <td>'.plus_dateToFrench($user->createddate, "d F Y h:i A").'</td>';
    $html .=              '<td>';
    if($user->status == 0){
      $html .=              '<a href="'.$CFG->wwwroot.'/groups/details/?id='.$groupid.'&userid='.$user->userid.'&status=1">'.get_string("accept", "form").'</a> / ';
      $html .=              '<a href="'.$CFG->wwwroot.'/groups/details/?id='.$groupid.'&userid='.$user->userid.'&status=2">'.get_string("reject", "form").'</a>';
    } else {
      if($user->status == 1){
        $html .=              get_string("accepted", "form");
        $html .=              '&nbsp; &nbsp;<a href="'.$CFG->wwwroot.'/groups/details/?id='.$groupid.'&userid='.$user->userid.'&status=3">'.get_string("remove", "form").'</a>';
      } else {
        $html .=              get_string("rejected", "form");
      }
    }
    $html .=              '</td>
        </tr>';
                          }
                        } else {
    $html .=              '<tr><td colspan="8" class="text-center">'.get_string("norecordfound", "form").'</td></tr>';
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
                    <h4 class="card-title">'.get_string("homework", "site").'</h4>
                    <a class="btn btn-primary card-body-action" href="'.$CFG->wwwroot.'/homeworks/edit?groupid='.$group->id.'"><i class="mdi mdi-plus"></i></a>
                    <div class="table-responsive">
                      <table class="table table-striped plus_local_datatable nosort" id="himeworklist">
                        <thead>
                          <tr>
                            <th>'.get_string("action", "table").'</th>
                            <th>'.get_string("duedate", "form").'</th>
                            <th>'.get_string("publishdate", "form").'</th>
                            <th>'.get_string("name", "form").'</th>
                            <th>'.get_string("level", "form").'</th>
                            <th>'.get_string("semester", "form").'</th>
                            <th>'.get_string("lesson", "form").'</th>
                            <th>'.get_string("quiz", "form").'</th>
                            <th>'.get_string("status", "form").'</th>
                            <th>'.get_string("creationdate", "form").'</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>';
                        if(is_array($group->homeworks) && !empty($group->homeworks)){
                          foreach ($group->homeworks as $key => $homework) {
                            // echo "<pre>"; print_r($homework);die;
    $html .=                '<tr>
                              <td class="p-1 text-center"><a class="p-1 m-1" style="font-size:32px;" href="'.$CFG->wwwroot.'/homeworks/edit?groupid='.$homework->groupid.'&id='.$homework->id.'"><i class="mdi mdi-lead-pencil"></i></a> &nbsp; 
                              <a class="p-1 m-1" style="font-size:32px;" href="'.$CFG->wwwroot.'/homeworks/report/?groupid='.$homework->groupid.'&homeworkid='.$homework->id.'"><i class="mdi mdi-library-books"></i></a></td>
                              <td>'.plus_dateToFrench($homework->duedate).'</td>
                              <td>'.plus_dateToFrench($homework->homeworkdate).'</td>
                              <td>'.$homework->name.'</td><td>'.$homework->grade.'</td>
                              <td>'.$homework->topicname.'</td>
                              <td>'.$homework->subtopicname.'</td>
                              <td>'.$homework->quizname.'</td>
                              <td>'.($homework->status?get_string("statuspublish", "form"):get_string("statusplanned", "form")).'</td>
                              <td>'.plus_dateToFrench($homework->createddate, "d F Y h:i A").'</td>
                              <td>
                                <a href="'.$CFG->wwwroot.'/homeworks/edit?groupid='.$homework->groupid.'&id='.$homework->id.'"><i class="mdi mdi-lead-pencil"></i> '.get_string("edit", "form").'</a> &nbsp; &nbsp; &nbsp; 
                                <a href="'.$CFG->wwwroot.'/homeworks/report/?groupid='.$homework->groupid.'&homeworkid='.$homework->id.'" target="_blank"><i class="mdi mdi-pulse"></i> '.get_string("report", "form").'</a>
                              </td>
                            </tr>';
                          }
                        } else {
    $html .=              '<tr><td colspan="9" class="text-center">'.get_string("norecordfound", "form").'</td></tr>';
                        }
    $html .=  '         </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
  echo $html;
  echo $OUTPUT->footer();
