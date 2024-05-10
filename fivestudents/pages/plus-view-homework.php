<?php
function plus_view_homework(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }
  $searchreq->homeworkname = plus_get_request_parameter("homeworkname", "");
  $searchreq->teacher = plus_get_request_parameter("teacher", "");
  $searchreq->group = plus_get_request_parameter("group","");
  $searchreq->courseid = plus_get_request_parameter("courseid","");
  $searchreq->schoolyear = plus_get_request_parameter("schoolyear",0);
  $searchreq->users = plus_get_request_parameter("users","");
  $searchreq->fromdate = plus_get_request_parameter("fromdate", "");
  $searchreq->todate = plus_get_request_parameter("todate", "");
  $searchreq->start = plus_get_request_parameter("start", 0);
  $searchreq->limit = plus_get_request_parameter("limit", 10);
  $searchreq->total = 0;
  $APIRES = $MOODLE->get("BrowseHomework", null, $searchreq);
  $APIRESCHOOL = $MOODLE->get("getSchoolyears");
  $APIRES22 = $MOODLE->get("getGroupByInstituteId", null);
  $selectedgroup=null;  

  $enabledcourse = $APIRES22->INSTITUTION->enablecourses;
  // echo "<script>console.log(" . json_encode($APIRES) . ");</script>";
  if($enabledcourse){
      $new_array = [];
      foreach ($enabledcourse as $inner_array) {
          $new_array = array_merge($new_array, $inner_array);
      }
  }
  // echo "<script>console.log(" . json_encode($new_array) . ");</script>";

  $html='<link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2/select2.min.css">
  <link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">'.plus_get_string("homeworks", "site").'</h4>
                  <form class="forms-sample">
                    <div class="form-group row">
                      <label for="name" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="homeworkname" class="form-control" id="homeworkname" placeholder="'.plus_get_string("name", "form").'" value="'.$searchreq->homeworkname.'">
                      </div>
                    </div>
                    <!--<div class="form-group row">
                      <label for="teacher" class="col-sm-2 col-form-label">Enseignants</label>
                      <div class="col-sm-10">
                        <input type="text" name="teacher" class="form-control" id="teacher" placeholder="Enseignants" value="'.$searchreq->teacher.'">
                      </div>
                    </div>-->
                    <div class="form-group row">
                    <label for="group" class="col-sm-2 col-form-label">'.plus_get_string("schoolyear", "site").'</label>
                    <div class="col-sm-10">
                    <select class="form-control" id="schoolyear" name="schoolyear">
                      <option value="">'.plus_get_string("selectschoolyear", "form").'</option>';
                      foreach($APIRESCHOOL->data as $schoolyear){
                        $selected='';
                        if($schoolyear->id == $searchreq->schoolyear){
                          $selected='selected';
                        }
                        $html.='<option value="'.$schoolyear->id.'" '.$selected.'>'.$schoolyear->name.'</option>';
                      }

                   $html.= '</select>
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="group" class="col-sm-2 col-form-label">'.plus_get_string("group", "form").'</label>
                    <div class="col-sm-10">
                    <select class="form-control" id="group" name="group">
                      <option value="">'.plus_get_string("selectgroup", "form").'</option>';
                      foreach($APIRES22->data as $group_row){
                       
                          $selected='';
                          if($group_row->id == $searchreq->group){
                            $selectedgroup = $group_row;
                            $selected='selected';
                          }
                          $html.='<option value="'.$group_row->id.'" '.$selected.'>'.$group_row->name.'</option>';
                      }

                   $html.= '</select>
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="courseid" class="col-sm-2 col-form-label">'.plus_get_string("matter", "form").'</label>
                    <div class="col-sm-10">
                    <select class="form-control" id="courseid" name="courseid"></select>
                    </div>
                    </div>
                   <!-- <div class="form-group row">
                    <label for="users" class="col-sm-2 col-form-label">d\'élèves</label>
                    <div class="col-sm-10">
                    <input type="text" name="users" class="form-control" id="users" value="'.$searchreq->users.'">
                    </div>
                    </div>-->
                    <div class="form-group row">
                    <label for="fromdate" class="col-sm-2 col-form-label">'.plus_get_string("from_date", "form").'</label>
                    <div class="col-sm-10">
                    <input type="date" name="fromdate" class="form-control" id="fromdate" value="'.$searchreq->fromdate.'">
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="todate" class="col-sm-2 col-form-label">'.plus_get_string("to_date", "form").'</label>
                    <div class="col-sm-10">
                    <input type="date" name="todate" class="form-control" id="todate" value="'.$searchreq->todate.'">
                    </div>
                    </div>
                    <input type="hidden" name="start" value="0"/>
                    <input type="hidden" name="limit" value="10"/>
                    <button type="submit" name="filter" class="btn btn-primary mr-2">'.plus_get_string("search", "form").'</button>
                    <button type="submit" name="cancel" class="btn btn-light">'.plus_get_string("cancel", "form").'</button>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"></h4>';
  $html .=        '<div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("action", "table").'</th>
                          <th>'.plus_get_string("duedate", "form").'</th>
                          <th>'.plus_get_string("publishdate", "form").'</th>
                          <th>'.plus_get_string("name", "form").'</th>
                          <th>'.plus_get_string("level", "form").'</th>
                          <th>'.plus_get_string("group", "form").'</th>
                          <th>'.plus_get_string("semester", "form").'</th>
                          <th>'.plus_get_string("lesson", "form").'</th>
                          <th>'.plus_get_string("quiz", "form").'</th>
                          <th>'.plus_get_string("status", "form").'</th>
                          <th>'.plus_get_string("creationdate", "form").'</th>
                          <th>'.plus_get_string("action", "table").'</th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_object($APIRES->data)){
                foreach ($APIRES->data->homework as $key => $homework) {
                  if(!empty($new_array) && !in_array($homework->courseid, $new_array)){continue;}else{
      $html .=         '<tr>
                        <td class="p-1 text-center"><a class="p-1 m-1" style="font-size:32px;" href="/homework-report/?groupid='.$homework->groupid.'&homeworkid='.$homework->id.'&schoolyear='.$homework->schoolyear.'"><i class="mdi mdi-library-books"></i>  &nbsp; </a></td>
                        <td>'.plus_dateToFrench($homework->duedate).'</td>
                        <td>'.plus_dateToFrench($homework->homeworkdate).'</td>
                        <td>'.$homework->name.'</td><td>'.$homework->grade.'</td>
                        <td>'.$homework->group_name.'</td>
                        <td>'.$homework->topicname.'</td>
                        <td>'.$homework->subtopicname.'</td>
                        <td>'.$homework->quizname.'</td>
                        <td>'.($homework->status?plus_get_string("statuspublish", "form"):plus_get_string("statusplanned", "form")).'</td>
                        <td>'.plus_dateToFrench($homework->createddate, "d F Y h:i A").'</td>
                        <td><a href="/homework-report/?groupid='.$homework->groupid.'&homeworkid='.$homework->id.'&schoolyear='.$homework->schoolyear.'"><i class="mdi mdi-library-books"></i> '.plus_get_string("report", "form").'</a></td></tr>';  
                  }                
                }
                $searchreq->total = $APIRES->data->total; 
                $searchreq->start = $APIRES->data->start;
               $searchreq->limit = $APIRES->data->limit;
              }        
            $html .=  '</tbody>
                    </table>
                  </div>';
  $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "homework");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '</div>
            </div>
          </div>

  <script>
    var groupdata = '.json_encode($APIRES22->data).';
    var formdata = '.json_encode($searchreq).';
    var newarray = '.json_encode($new_array).';

    var allcourses = [];
    $(document).ready(function(){
      $("#group").change(function(){
        var groupid = $(this).val();
        var selectedgroup = groupdata.find(x => x.id === groupid);
        console.log("selectedgroup- ", selectedgroup);
        if(selectedgroup && Array.isArray(selectedgroup?.courses)){
          allcourses = selectedgroup?.courses;
        } else {
          allcourses = [];
        }
        var newoptions = "";
        $.each( allcourses, function( key, course ) {
          var sel = ``;
          if(formdata && formdata.courseid == course.id){
            sel = `Selected`;
          }
          if (Array.isArray(newarray) && newarray.includes(course.id)) {
            newoptions += `<option ${sel} value="${course.id}">${course.fullname}</option>`
          }
        });
        $("#courseid").html(newoptions);
        $("#courseid").trigger("change");
      });
    $("#group").trigger("change");
    });
  </script>

          ';
 

  return $html;
}