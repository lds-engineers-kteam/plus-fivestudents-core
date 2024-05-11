<?php
  require_once("../config.php");
  require_login();
  $syncnow = optional_param("syncnow", 0);
  if($syncnow){
    syncAllHomeworks();
    redirect("{$CFG->wwwroot}/homeworks/");
  }
  if(isset($_REQUEST['cancel'])){
    redirect("{$CFG->wwwroot}/homeworks/");
  }
  $searchreq = new stdClass();
  $searchreq->homeworkname = optional_param("homeworkname", "");
  $searchreq->teacher = optional_param("teacher", "");
  $searchreq->group = optional_param("group","");
  $searchreq->course = optional_param("course","");
  $searchreq->schoolyear = optional_param("schoolyear",0);
  $searchreq->users = optional_param("users","");
  $searchreq->fromdate = optional_param("fromdate", "");
  $searchreq->todate = optional_param("todate", "");

  $OUTPUT->loadjquery();
  $homeworkdata = get_allhomeworks();
  $lastsynced = get_string("lastfetched",'form');
  if(is_object($homeworkdata) && !empty($homeworkdata->lastsynced)){
    $lastsynced = plus_dateToFrench($homeworkdata->lastsynced);
  }
  $groupdata = get_allgroups();
  echo $OUTPUT->header();

  $html = '';
  // $html .= '<pre>'.print_r($USER, true).'</pre>';
  // $html .= '<pre>'.print_r($groupdata, true).'</pre>';
  // $html .= '<pre>'.print_r($homeworkdata, true).'</pre>';
  $html .= '<div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
            <div class="card">
              <div class="card-body haveaction">
                <p class="card-title mb-0">'.get_string("homeworks",'site').' <span class="badge">'.get_string("lastsynced",'site').': '.$lastsynced.'</span></p>
                <div class="text-right">
                '.(has_internet()?'<a class="btn btn-primary" href="'.$CFG->wwwroot.'/homeworks?syncnow=1">'.get_string("syncnow",'site').'</a>':'').'
                </div>
                <br>
                <form class="forms-sample">
                  <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">'.get_string("name", "form").'</label>
                    <div class="col-sm-10">
                      <input type="text" name="homeworkname" class="form-control" id="homeworkname" placeholder="'.get_string("name", "form").'" value="'.$searchreq->homeworkname.'">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="group" class="col-sm-2 col-form-label">'.get_string("group", "form").'</label>
                    <div class="col-sm-10">
                      <select class="form-control" id="group" name="group">
                        <option value="">'.get_string("selectgroup", "form").'</option>';
                        foreach($groupdata->groups as $group_row){
                          if(!in_array($group_row->id, $USER->groupids)){continue;}
                          $selected='';
                          if($group_row->id == $searchreq->group){
                            $selected='selected';
                          }
                          $html.='<option value="'.$group_row->id.'" '.$selected.'>'.$group_row->name.'</option>';
                        }

                     $html.= '</select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="group" class="col-sm-2 col-form-label">'.get_string("matter", "form").'</label>
                    <div class="col-sm-10">
                      <select class="form-control" id="course" name="course"></select>
                    </div>
                  </div>
                 <!-- <div class="form-group row">
                  <label for="users" class="col-sm-2 col-form-label">d\'élèves</label>
                  <div class="col-sm-10">
                  <input type="text" name="users" class="form-control" id="users" value="'.$searchreq->users.'">
                  </div>
                  </div>-->
                  <div class="form-group row">
                  <label for="fromdate" class="col-sm-2 col-form-label">'.get_string("from_date", "form").'</label>
                  <div class="col-sm-10">
                  <input type="date" name="fromdate" class="form-control" id="fromdate" value="'.$searchreq->fromdate.'">
                  </div>
                  </div>
                  <div class="form-group row">
                  <label for="todate" class="col-sm-2 col-form-label">'.get_string("to_date", "form").'</label>
                  <div class="col-sm-10">
                  <input type="date" name="todate" class="form-control" id="todate" value="'.$searchreq->todate.'">
                  </div>
                  </div>
                  <input type="hidden" name="start" value="0"/>
                  <input type="hidden" name="limit" value="10"/>
                  <button type="submit" name="filter" class="btn btn-primary mr-2">'.get_string("search", "form").'</button>
                  <button type="submit" name="cancel" class="btn btn-light">'.get_string("cancel", "form").'</button>
                </form>
              </div>
            </div>
          </div>';
 // $html .= '<pre>'.print_r($USER->subjects, true).'</pre>';
 
  $html .=  '<div class="col-12 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="userlist" class="table plus_local_datatable table-borderless">
                      <thead>
                        <tr>
                          <th>'.get_string("duedate", "form").'</th>
                          <th>'.get_string("publishdate", "form").'</th>
                          <th>'.get_string("name", "form").'</th>
                          <th>'.get_string("level", "form").'</th>
                          <th>'.get_string("group", "form").'</th>
                          <th>'.get_string("matter", "form").'</th>
                          <th>'.get_string("semester", "form").'</th>
                          <th>'.get_string("lesson", "form").'</th>
                          <th>'.get_string("homework", "form").'</th>
                          <th>'.get_string("totoalquestion", "form").'</th>
                          <th>'.get_string("status", "form").'</th>
                          <th>'.get_string("creationdate", "form").'</th>
                          <th class="border-bottom pb-2"></th>
                        </tr>
                      </thead>
                      <tbody>';
  if(is_object($homeworkdata) && is_array($homeworkdata->homeworks)){
    foreach ($homeworkdata->homeworks as $key => $homework) {
      $skipped = false;
      if(!in_array($homework->groupid, $USER->groupids)){$skipped=true;}
      if(!in_array($homework->coursetype, $USER->subjects)){$skipped=true;}
      if(!empty($searchreq->homeworkname) && !str_contains($homework->name, $searchreq->homeworkname)){$skipped=true;}
      if(!empty($searchreq->group) && $homework->groupid !=  $searchreq->group){$skipped=true;}
      if(!empty($searchreq->course) && $homework->courseid !=  $searchreq->course){$skipped=true;}

      if(!empty(strtotime($searchreq->fromdate)) && $homework->duedate <  strtotime($searchreq->fromdate)){$skipped=true;}
      if(!empty(strtotime($searchreq->todate)) && $homework->duedate >  strtotime($searchreq->todate)){$skipped=true;}
      if($skipped){continue;}
      $html .='<tr>
        <td>'.plus_dateToFrench($homework->duedate).'</td>
        <td>'.plus_dateToFrench($homework->homeworkdate).'</td>
        <td>'.$homework->name.'</td>
        <td>'.$homework->grade.'</td>
        <td>'.$homework->group_name.'</td>
        <td>'.$homework->offlineCourseName.'</td>
        <td>'.$homework->topicname.'</td>
        <td>'.$homework->subtopicname.'</td>
        <td>'.$homework->quizname.'</td>
        <td>'.$homework->totalquestion.'</td>
        <td>'.($homework->status?'Published':'Planned').'</td>
        <td>'.plus_dateToFrench($homework->createddate).'</td>
        <td><a href="'.$CFG->wwwroot.'/homeworks/report/?homeworkid='.$homework->id.'"><i class="mdi mdi-pulse"></i> '.get_string("report", "form").'</a></td>
      </tr>';
    }
  }
  $html .='
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>';
  $html .='
  <script>
    var groupdata = '.json_encode($groupdata->groups).';
    var formdata = '.json_encode($searchreq).';
    var usersubjects = '.json_encode($USER->subjects).';
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
          if(!Array.isArray(usersubjects) || !usersubjects.includes(course.coursetype)){
            return;
          }
          var sel = ``;
          if(formdata && formdata.course == course.id){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${course.id}">${course.fullname}</option>`
        });
        $("#course").html(newoptions);
        $("#course").trigger("change");
      });
    $("#group").trigger("change");
    });
  </script>
  ';

  echo $html;
  echo $OUTPUT->footer();
