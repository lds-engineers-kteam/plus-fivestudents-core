<?php
function plus_classProfileFilter(){
  global $CLASSPROFILEAVGDATA, $CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $MOODLESESSION = wp_get_moodle_session();
  $CLASSPROFILEAVGDATA = array();
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $filtereddataJson = "";
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->schoolyear = plus_get_request_parameter("schoolyear", $MOODLESESSION->currentschoolyear);
  $formdata->categoryid = plus_get_request_parameter("categoryid", 0);
  $formdata->courseid = plus_get_request_parameter("courseid", 0);
  $formdata->quiz = plus_get_request_parameter("quiz", "");
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->students = plus_get_request_parameter("students", array());
  $formdata->showreport = plus_get_request_parameter("showreport", "");
  $formdata->fromdate = plus_get_request_parameter("fromdate", date("Y-m-d\TH:i",$MOODLESESSION->currentYear->startdate?:time()));
  $formdata->todate = plus_get_request_parameter("todate", date("Y-m-d\TH:i",$MOODLESESSION->currentYear->enddate?:time()));

  $SchoolyearAPI = $MOODLE->get("getAllSchoolyear", null, $formdata);
  $allschoolyear = array();
  if($SchoolyearAPI->data){
    $allschoolyear = $SchoolyearAPI->data;
    if($MOODLESESSION->currentschoolyear != $formdata->schoolyear){
      $school_key = array_search($formdata->schoolyear, array_column($allschoolyear, 'id'));
      if($school_key !== false){
        if($schoolyear = $allschoolyear[$school_key]){
          $formdata->fromdate = date("Y-m-d\TH:i",$schoolyear->startdate?:time());
          $formdata->todate = date("Y-m-d\TH:i",$schoolyear->enddate?:time());
        }
      }
    }
  }
  $APIREScompetenciesdata = $MOODLE->get("getclassProfileFilter", null, $formdata);
  if($formdata->showreport == 'show'){
    $filtereddata = $MOODLE->get("getclassProfileReport1", null, $formdata);
  }
  $html ='';
  // $html .='<pre>'.print_r($APIREScompetenciesdata, true).'</pre>';
  $allGrades=array();
  $selectedgrade=null;
  $selectedGroup=null;
  $selectedcourse=array();
  $selectedquiz=array();
  $selectedStudents=array();
  foreach($APIREScompetenciesdata->data as $competenciesdata){
    array_push($allGrades,$competenciesdata);   
  }
  // echo "<script>console.log(" . json_encode($APIREScompetenciesdata) . ");</script>";

  $enabledcourse = $APIREScompetenciesdata->INSTITUTION->enablecourses;
  if($enabledcourse){
      $new_array = [];
      foreach ($enabledcourse as $inner_array) {
        if(is_array($inner_array)){
          $new_array = array_merge($new_array, $inner_array);
        }
      }
  }
  echo "<script>console.log(" . json_encode($new_array) . ");</script>";
  $html .= '<style type="text/css">
  .datareport .smalldot {
    width: 15px;
    height: 15px;
  }
  table.datareport *{
    vertical-align: middle;
}
</style>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">'.plus_get_string("classprofile", "report").'</h4>
                  <form method="GET" id="classprofilereport" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="schoolyear" class="col-sm-2 col-form-label">'.plus_get_string("schoolyear", "site").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="schoolyear" id="schoolyear">
                          ';
                          foreach($allschoolyear as $schoolyear){
                            $selected=($formdata->schoolyear == $schoolyear->id)?'selected':'';
                            $current=$schoolyear->current?'(Current)':'';
                            $html .='<option value="'.$schoolyear->id.'" '.$selected.'> '.$schoolyear->name.$current.'</option>';
                          }
                    $html .= '</select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="categoryid" class="col-sm-2 col-form-label">'.plus_get_string("level", "form").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="categoryid" id="categoryid">
                          <option value="">'.plus_get_string("select", "form").' '.plus_get_string("level", "form").'</option>';
                          foreach($allGrades as $grades){
                            $selected='';
                            if($formdata->categoryid == $grades->categoryid){
                              $selected='selected';
                              $selectedgrade = $grades;
                            }
                            $html .='<option value="'.$grades->categoryid.'" '.$selected.'> '.$grades->name.'</option>';
                          }
                    $html .= '      </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="groupid" class="col-sm-2 col-form-label">'.plus_get_string("group", "form").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="groupid" id="groupid">';
                          if(isset($selectedgrade) && is_array($selectedgrade->groups)){
                            foreach($selectedgrade->groups as $group){
                              $selected='';
                              if($formdata->groupid == $group->groupid){
                                $selected='selected';
                                $selectedGroup = $group;
                              }
                              $html .='<option value="'.$group->groupid.'" '.$selected.'> '.$group->name.'</option>';
                            }
                          }
                       $html .=' </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="courseid" class="col-sm-2 col-form-label">'.plus_get_string("matter", "form").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="courseid" id="courseid">';
                          if(isset($selectedGroup) && is_array($selectedGroup->courses)){
                            foreach($selectedGroup->courses as $course){
                               // if(!empty($new_array) && !in_array($task->courseid, $new_array)){continue;}else{
                                $selected='';
                                if($formdata->courseid == $course->id){
                                  $selected='selected';
                                  $selectedcourse = $course;
                                }
                                $html .='<option value="'.$course->id.'" '.$selected.'> '.$course->fullname.'</option>';
                              }
                            // }
                          }
                       $html .=' </select>
                      </div>
                    </div>
                      <div class="form-group row">
                      <label for="quiz" class="col-sm-2 col-form-label">'.plus_get_string("homework", "form").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="quiz" id="quiz">';
                          if(isset($selectedcourse) && is_array($selectedcourse->quizes)){
                            $html .='<option value="" '.$selected.'>All</option>';
                            foreach($selectedcourse->quizes as $quiz){
                              $selected='';
                              if($formdata->quiz == $quiz){
                                $selected='selected';
                              }
                              $html .='<option value="'.$quiz.'" '.$selected.'> '.$quiz.'</option>';
                            }
                          }
                       $html .=' </select>
                      </div>
                    </div>
                     <div class="form-group row">
                      <label for="students" class="col-sm-2 col-form-label">'.plus_get_string("students", "form").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="students[]" id="students" multiple>';
                          if(isset($selectedGroup) && is_array($selectedGroup->courses)){
                            foreach($selectedGroup->group_member as $member){
                              $selected='';
                              if(in_array($member->userid, $formdata->students)){
                                $selected='selected';
                              }
                              $html .='<option value="'.$member->userid.'" '.$selected.'> '.$member->firstname.' '.$member->lastname.'</option>';
                            }
                          }
                       $html .=' </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="fromdate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("from", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="fromdate" class="form-control" id="fromdate" value="'.$formdata->fromdate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="todate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("to", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="todate" class="form-control" id="todate" value="'.$formdata->todate.'">
                      </div>
                    </div>
                    <button type="submit" name="showreport" id="showreport" value="show" class="btn btn-primary mr-2">'.plus_get_string("search", "form").'</button>
                    <a href="/sushiltest" class="btn btn-warning">'.plus_get_string("cancel", "form").'</a>
                    <a href="' . $CFG->wwwroot . (empty($formdata->groupid) ? '/class-profile' : '/group-details/?id=' . $formdata->groupid) . '" class="btn btn-warning">' . plus_get_string("return", "form") . '</a>
                  </form>
                </div>
              </div>
            </div>';
if($formdata->showreport){
  $html .=  '<br/><hr/><br/>';
  if($filtereddata->data){
     $filtereddataJson = $filtereddata->data->topicdata;
    // $html .= '<pre>'.print_r($filtereddata->data->topicdata, true).'</pre>'; 
    $html .=  '<br/><hr/><br/>';
    $html .=  '<div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <!--<h4 class="card-title">Report</h4>-->';
    if(sizeof($filtereddata->data->topicdata) > 0){
      $html .=          '<div class="row table-scroll">
                          <table class="datareport classprofilereport">';
      $html .=  classprofilereport_tr($filtereddata->data->topicdata);
      $html .='      <tr><td></td>';
      foreach ($filtereddata->data->topicdata as $key => $topicdata) {
        $childount = $topicdata->childcount/4;
        for ($i=0; $i < $childount; $i++) { 
        $html .=  '<td ><p class="text-left font-weight-bold mb-0">'.plus_get_string("status_failed", "form").'</p></td>
                   <td ><p class="text-left font-weight-bold mb-0">'.plus_get_string("statusbasic", "report").'</p></td>
                   <td ><p class="text-left font-weight-bold mb-0">'.plus_get_string("statusgood", "report").'</p></td>
                   <td ><p class="text-left font-weight-bold mb-0">'.plus_get_string("statusexcelent", "report").'</p></td>';
        }
      }
      $html .='      </tr>';
      $studenthtml = '';
      foreach ($filtereddata->data->studentdata as $key => $student) {
        $studenthtml .= '<tr><td>'.$student->firstname.' '.$student->lastname.'</td>';
        $studenthtml .=  classprofilereport_td($filtereddata->data->topicdata, $student->scoredata, $filtereddata->data->xpsetting);
        $studenthtml .=' </tr>';
      }
      $html .= '<tr><td>'.plus_get_string("groupaverage", "form").'</td>';
      $html .=  classprofilereport_avg($filtereddata->data->topicdata, $filtereddata->data->xpsetting);
      $html .=' </tr>';
      $html .= $studenthtml;
      $html .=          ' </table>
                        </div>';
    } else {
      $html .=              '<div class="alert alert-info">'.plus_get_string('norecordfound','form').'</div>';
    }

    $html .=  '</div>
            </div>
          </div>';

  }
  // $html .='<pre>'.print_r($formdata, true).'</pre>';
  // $html .='<pre>'.print_r($filtereddata, true).'</pre>';
    

  //   $html .=  '<div class="col-md-12 grid-margin stretch-card">
  //               <div class="card">
  //                 <div class="card-body">
  //                   <h4 class="card-title">Report</h4>';
                    
  // $html .= '
  //       <div class="row table-scroll">
  //         <table class="datareport">
  //           <tr>
  //             <th>Student</th>
  //             <th colspan="16" class="text-center font-weight-bold" >Unit1</th>
  //             <th colspan="16" class="text-center font-weight-bold" >Unit2</th>
  //             <th colspan="16" class="text-center font-weight-bold" >Unit3</th>
  //             <th colspan="16" class="text-center font-weight-bold" >Unit4</th>
  //           </tr>
  //           <tr>
  //             <td></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u1l1</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u1l2</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u1l3</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u1l4</p></td>

  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u2l1</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u2l2</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u2l3</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u2l4</p></td>

  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u3l1</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u3l2</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u3l3</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u3l4</p></td>

  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u4l1</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u4l2</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u4l3</p></td>
  //             <td colspan="4"><p class="text-center font-weight-bold mb-0">u4l4</p></td>

  //           </tr>
  //           <tr>
  //             <td></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>



  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>



  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>



  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //             <td ><p class="text-left font-weight-bold mb-0">Not reached</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Minimally Achieved</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Satisfying</p></td>
  //             <td ><p class="text-left font-weight-bold mb-0">Excellent</p></td>

  //           </tr>
  //           <tr>
  //             <td>Sanjana Dagar</td>
  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //           </tr>
  //           <tr>
  //             <td>Sanjana Dagar</td>
  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //           </tr>
  //           <tr>
  //             <td>Sanjana Dagar</td>
  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //           </tr>
  //           <tr>
  //             <td>Sanjana Dagar</td>
  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //             <td class="text-center"><span class="smalldot red"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>

  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot yellow"></span></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span></td>
  //             <td class="text-center"></td>
              
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"></td>
  //             <td class="text-center"><span class="smalldot blue"></span></td>
              

  //           </tr>
  //           <!--<tr>
  //             <td class="font-weight-bold mb-0">Average</td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>

  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>

  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>

  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>

  //             <td class="text-center"><span class="smalldot blue"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
  //             <td class="text-center"><span class="smalldot red"></span><p class="font-weight-bold mb-0"></p></td>
  //           </tr>-->
  //         </table>
  //       </div>
  //  ';   

  $html .=  '
                </div>
              </div>
            </div>';

  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '</div>
            </div>
          </div>';
            
}

  $html .=  '
  <script>
  var topicdata='.json_encode($filtereddataJson).';
  var newarray = '.json_encode($new_array).';
  $(function(){
    var allGrades='.json_encode($allGrades).';
    var selectedgrade=null;
    var selectedgroup=null;
    var selectedcourse=null;
    $("body").on("change","#schoolyear",function(){
      $("#showreport").val("schoolyearchanged");
      $("#classprofilereport").trigger("submit");
    });
    $("body").on("change","#categoryid",function(){
      var categoryid=$(this).val();
      var groupoption="";
      console.log("categoryid-- ",categoryid);
      selectedgrade=allGrades?.find(function(gradeitem){
        return gradeitem.categoryid==categoryid;
      });
      if(selectedgrade){
        selectedgrade.groups?.forEach(function(group){
          groupoption +=\'<option value="\'+group.groupid+\'">\'+group.name+\'</option>\';
        });
      }
      $("#groupid").html(groupoption);
      $("#groupid").trigger("change");
    });
    $("body").on("change","#groupid",function(){
      var groupid=$(this).val();
      var courseoption="";
      var studentoption="";
      console.log("groupid-- ",groupid);
      if(selectedgrade){
        selectedgroup=selectedgrade?.groups?.find(function(groupitem){
          return groupitem.groupid==groupid;
        });
        if(selectedgroup){
          selectedgroup.courses?.forEach(function(course){
            if (Array.isArray(newarray) && newarray.includes(course.id)) {
              courseoption +=\'<option value="\'+course.id+\'">\'+course.fullname+\'</option>\';
            }
          });
          selectedgroup.group_member?.forEach(function(member){
            studentoption +=\'<option value="\'+member.userid+\'">\'+member.firstname+\' \'+member.lastname+\'</option>\';
          });
        }
      }
      $("#courseid").html(courseoption);
      $("#courseid").trigger("change");
      $("#students").html(studentoption);
      $("#students").trigger("change");
    });
    $("body").on("change","#courseid",function(){
      var courseid=$(this).val();
      var quizoption=\'<option value="">'.plus_get_string("all", "site").'</option>\';
      if(selectedgroup){
        selectedcourse=selectedgroup?.courses?.find(function(courseitem){
          return courseitem.id==courseid;
        });
        if(selectedcourse){
          selectedcourse.quizes?.forEach(function(quiz){
            quizoption +=\'<option value="\'+quiz+\'">\'+quiz+\'</option>\';
          });
        }
      }
      $("#quiz").html(quizoption);
    });
  });
  </script>';

    return $html;
}