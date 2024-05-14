<?php
function plus_view_dashboardstatus_report(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->categoryid = plus_get_request_parameter("categoryid", 0);
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->homeworkid = plus_get_request_parameter("homeworkid", 0);
  $formdata->filtertype = plus_get_request_parameter("filtertype", 1);
  $formdata->fromdate = plus_get_request_parameter("fromdate", date("Y-m-d"));
  $formdata->todate = plus_get_request_parameter("todate", date("Y-m-d"));
  $formdata->attempt = plus_get_request_parameter("attempt", 1);
  $formdata->status = plus_get_request_parameter("status", 0);
  $APIRES = $MOODLE->get("DashboardStatusReport", null, $formdata);
  $html='';

  // $html .=   '<div class="table-responsive">'.(gettype($APIRES) ).'</div>';
  // $html .=   '<div class="table-responsive">'.(is_object($APIRES)?json_encode($APIRES):$APIRES).'</div>';
  // if(!is_object($APIRES)){ 
  //   $html .=  '<div class="alert alert-danger">There is some error</div>';     
  //   return $html;
  // }
  // echo "<pre>";
  // print_r($formdata);
  // echo "</pre>";
  // die;
  $html .=  '<div class="row">';
  $html .=  '<div class="col-md-12 grid-margin">
              <div class="row mb-4">
                <div class="col-sm-9"><h3 class="font-weight-bold"></h3></div>
                <div class="col-sm-3 text-right"><button onclick="exportData(\'dashboardstatusreport\')"> Export</button></div>
                <!--<div class="col-12 col-xl-4">
                 <div class="justify-content-end d-flex">
                  <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                     <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                      <a class="dropdown-item" href="#">January - March</a>
                      <a class="dropdown-item" href="#">March - June</a>
                      <a class="dropdown-item" href="#">June - August</a>
                      <a class="dropdown-item" href="#">August - November</a>
                    </div>
                  </div>
                 </div>
                </div>-->
              </div>
            </div>
          </div>';

  $html .='<div class="table-responsive"><table  class="table table-striped nosort" id="dashboardstatusreport">
  <tr>
      <th style="width:140px;">'.plus_get_string("firstname", "form").'</th>
      <th style="width:140px;">'.plus_get_string("lastname", "form").'</th>
      <th style="width:140px;">'.plus_get_string("chartname", "form").'</th>
      <th style="width:100px;">'.plus_get_string("level", "form").'</th>
      <th style="width:100px;">'.plus_get_string("group", "form").'</th>
      <th style="width:140px;">'.plus_get_string("matter", "form").'</th>
      <th style="width:140px;">'.plus_get_string("semester", "form").'</th>
      <th style="width:140px;">'.plus_get_string("lesson", "form").'</th>
      <th style="width:140px;">'.plus_get_string("name", "form").'</th>
      <th style="width:140px;">'.plus_get_string("quiz", "form").'</th>
      <th style="width:110px;">'.plus_get_string("duedate", "form").'</th>
      <th style="width:90px;">'.plus_get_string("status", "form").'</th>
      <th style="width:90px;">'.plus_get_string("completiondate", "form").'</th>
      <th style="width:25px;">'.plus_get_string("score", "form").'</th>';
  $html .='</tr>';
  // $html .='<table class="reporttable" border="1" style="border-color: #e0ebeb;table-layout: fixed; width:100%;">';
  $htmldata = "";
  foreach ($APIRES->data as $key => $student) {
    $htmldata .='<tr>
      <td>'.$student->firstname.'</td>
      <td>'.$student->lastname.'</td>
      <td>'.$student->charname.'</td>
      <td>'.$student->gradename.'</td>
      <td>'.$student->group_name.'</td>
      <td>'.$student->coursename.'</td>
      <td>'.$student->topicname.'</td>
      <td>'.$student->subtopicname.'</td>
      <td>'.$student->name.'</td>
      <td>'.$student->quizname.'</td>
      <td>'.plus_dateToFrench($student->duedate).'</td>
      <td>'.plus_get_string("status_".$student->status, "form").'</td>
      <td>'.plus_dateToFrench($student->timefinish).'</td>
      <td>'.$student->score.'</td>
      </tr>';
  }
  $htmlhead = "";
  $html .= $htmlhead.$htmldata;
  $html .='</table></div>';
  return $html;
}
