<?php
function plus_view_addevent(){
  global $wp, $MOODLESESSION;
  if ( !is_user_logged_in() || !current_user_can('plus_editevents') || ( $MOODLESESSION->INSTITUTION && $MOODLESESSION->INSTITUTION->disablecalendar == 1)) {
    return plus_view_noaccess();
  }


  $MOODLE = new MoodleManager();
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->name = plus_get_request_parameter("title", "");
  $formdata->description = plus_get_request_parameter("eventdescription", "");
  $formdata->institutionid = plus_get_request_parameter("institutionid", 0);
  $formdata->teacherid = plus_get_request_parameter("teacherid", 0);
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->courseid = plus_get_request_parameter("courseid", 0);
  $formdata->startdate = plus_get_request_parameter("startdate", date("Y-m-d\TH:i",time()));
  $formdata->enddate = plus_get_request_parameter("enddate", date("Y-m-d\TH:i",strtotime("+2 hours")));
  $formdata->repeatevent = plus_get_request_parameter("repeat", 0);
  $formdata->repeatdata = (object)array(
    "repeatstart"=>plus_get_request_parameter("repeatstart", date("Y-m-d",time())),
    "repeatend"=>plus_get_request_parameter("repeatend", date("Y-m-d",time())),
    "repeatevery"=>plus_get_request_parameter("repeatevery", 0),
    "repeatdays"=>plus_get_request_parameter("repeatdays", array()),
  );
  $formdata->repeatstart = plus_get_request_parameter("repeatstart", date("Y-m-d",time()));
  $formdata->repeatend = plus_get_request_parameter("repeatstart", date("Y-m-d",time()));
  $formdata->repeatevery = plus_get_request_parameter("repeatevery", 0);
  $formdata->repeatdays = plus_get_request_parameter("repeatdays", array());
  $formdata->startsurvey = plus_get_request_parameter("startsurvey", 0);
  $formdata->endsurvey = plus_get_request_parameter("endsurvey", 0);
  $formdata->returnto = plus_get_request_parameter("returnto", "events");

  if(isset($_POST['saveevents'])){
  //   if(($_POST['id'] > 0) && (!empty($_POST['id']))){ 
  //     $postdata = new stdClass();
  //     $postdata->id = $_POST['id'];
  //     $postdata->name = '';
  //     $postdata->description = '';
  //     $postdata->institutionid = $_POST['institutionid'];
  //     $postdata->teacherid = $_POST['teacherid'];
  //     $postdata->groupid = $_POST['groupid'];
  //     $postdata->courseid = $_POST['courseid'];
  //     $postdata->startdate = $_POST['startdate'];
  //     $postdata->enddate = $_POST['enddate'];
  //     $postdata->repeatevent = (!empty($_POST['repeatevent'])?$_POST['repeatevent']:0);
  //     $postdata->repeatdata = (object)array(
  //       "repeatstart"=> $_POST['repeatstart'],
  //       "repeatend"=> $_POST['repeatend'],
  //       "repeatevery"=> $_POST['repeatevery'],
  //       "repeatdays"=> (!empty($_POST['repeatdays'])?$_POST['repeatdays']:array()),
  //     );
  //     $postdata->repeatstart = $_POST['repeatstart'];
  //     $postdata->repeatend = $_POST['repeatend'];
  //     $postdata->repeatevery = $_POST['repeatevery']; 
  //     $postdata->repeatdays = (!empty($_POST['repeatdays'])?$_POST['repeatdays']:array());
  //     $postdata->startsurvey = 0;
  //     $postdata->endsurvey = 0;
  //     $postdata->returnto = $_POST['returnto'];
      
  //     $se = $MOODLE->get("editEvents", null, $postdata);
  // }else{
    $se = $MOODLE->get("saveEvents", null, $formdata);
  // }
  // die;
    if($formdata->returnto == "calendar"){
      plus_redirect(home_url().'/calendar/');
    } else {
      plus_redirect(home_url().'/events/');
    }
    exit;
  }
  $html  ='';
  $APIRES = $MOODLE->get("getEventByID", null, $formdata);
  if(isset($APIRES->data)){
    if(isset($APIRES->data->event)){
      // $html  .='<pre>'.print_r($APIRES->data->event, true).'</pre>';
      $event = $APIRES->data->event;
      $formdata->id = $event->id;
      $formdata->name = $event->name;
      $formdata->description = $event->description;
      $formdata->institutionid = $event->institutionid;
      $formdata->teacherid = $event->teacherid;
      $formdata->groupid = $event->groupid;
      $formdata->courseid = $event->courseid;
      $formdata->repeatevent = $event->repeatevent;
      $formdata->startdate = date("Y-m-d\TH:i",$event->timestart);
      $formdata->enddate = date("Y-m-d\TH:i",$event->timeend);
      $repeatdata = unserialize($event->repeatdata);
      if($repeatdata){
        $formdata->repeatdata = $repeatdata;
      }
      // $html  .='<pre>'.print_r($formdata, true).'</pre>';
    }
  }
  // if ( !empty($formdata->id) || !current_user_can('plus_editownevents')) {
  //   return plus_view_noaccess();
  // }
  $institutionData = $MOODLE->get("institutionWithTeacher", null,array());
 
  $institutions = array();
  $selectedinstitution = null;
  $selectedteacher = null;
  $selectedgroup = null;
  if($institutionData && !empty($institutionData->data)){
    if(is_array($institutionData->data)){
      $institutions = $institutionData->data;
    } else {
      $institutions = array($institutionData->data);
    }
  }
  $institutionoptions = '<option value="" >'.plus_get_string("select", "form").' '.plus_get_string("school", "site").' </option>';
  if(is_array($institutions)){
    foreach ($institutions as $key => $institution) {
      $sel = '';
      if($formdata->institutionid == $institution->id){
        $selectedinstitution = $institution;
        $sel='selected';
      }
      $institutionoptions .= '<option '.$sel.' value="'.$institution->id.'" '.($formdata->institutionid == $institution->id?'selected':'').' >'.$institution->institution.' </option>';
    }
  }
  $teachersoption = '<option value="0" >My Event</option>';
  if($selectedinstitution && !empty($selectedinstitution->teachers)){
    foreach ($selectedinstitution->teachers as $teacher) {
      $sel = '';
      if($formdata->teacherid == $teacher->id){
        $selectedteacher = $teacher;
        $sel='selected';
      }
      $teachersoption .= '<option value="'.$teacher->id.'" '.$sel.'>'.$teacher->firstname.' '.$teacher->lastname.' </option>';
    }
  }
  $groupsoption = '<option value="0" >'.plus_get_string("all", "site").' '.plus_get_string("group", "form").' </option>';
  if($selectedteacher && !empty($selectedteacher->groups)){
    // echo '<pre>';
    // print_r($selectedteacher->groups);
    // echo '</pre>';
    // die;
    foreach ($selectedteacher->groups as $group) {
      $sel = '';
      if($formdata->groupid == $group->id){
        $selectedgroup = $group;
        $sel='selected';
      }
      $groupsoption .= '<option value="'.$group->id.'" '.$sel.'>'.$group->name.'</option>';
    }
  }
  $coursesoption = '<option value="0" >'.plus_get_string("all", "site").' '.plus_get_string("matter", "form").' </option>';
  if($selectedgroup && !empty($selectedgroup->courses)){
    foreach ($selectedgroup->courses as $course) {
      $sel = '';
      if($formdata->courseid == $course->id){
        $selectedcourse = $course;
        $sel='selected';
      }
      $coursesoption .= '<option value="'.$course->id.'" '.$sel.'>'.$course->fullname.'</option>';
    }
  }
  // $html .= '<pre>'.print_r($institutionData, true).'</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("editevent", "form").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="institutionid" class="col-sm-2 col-form-label">'.plus_get_string("school", "site").' *</label>
                      <div class="col-sm-10">
                        <select name="institutionid" required id="institutionid" class="form-control">
                        '.$institutionoptions.'
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="teacherid" class="col-sm-2 col-form-label">'.plus_get_string("user", "form").' *</label>
                      <div class="col-sm-10">
                        <select name="teacherid" id="teacherid" class="form-control">
                        '.$teachersoption.'
                        </select>
                      </div>
                    </div>
                     <div class="form-group row">
                      <label for="groupid" class="col-sm-2 col-form-label">'.plus_get_string("group", "form").' *</label>
                      <div class="col-sm-10">
                        <select name="groupid" id="groupid" class="form-control">
                        '.$groupsoption.'
                        </select>
                      </div>
                    </div>
                     <div class="form-group row">
                      <label for="courseid" class="col-sm-2 col-form-label">'.plus_get_string("matter", "form").' </label>
                      <div class="col-sm-10">
                        <select name="courseid" id="courseid" class="form-control">
                        '.$coursesoption.'
                        </select>
                      </div>
                    </div>
                    <!--<div class="form-group row">
                      <label for="title" class="col-sm-2 col-form-label">'.plus_get_string("eventtitle", "form").' *</label>
                      <div class="col-sm-10">
                        <input  autocomplete="off" type="text" name="title" class="form-control" id="title" placeholder="'.plus_get_string("eventtitle", "form").'" value="'.$formdata->name.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="eventdescription" class="col-sm-2 col-form-label">'.plus_get_string("eventdescription", "form").' </label>
                      <div class="col-sm-10">
                      <textarea id="eventdescription" name="eventdescription" rows="20" cols="80" class="form-control summernote editor">'.$formdata->description.'</textarea>
                      </div>
                    </div>-->
                    <div class="form-group row">
                      <label for="startdate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("startdate", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="startdate" class="form-control" id="startdate" value="'.$formdata->startdate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="enddate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("enddate", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="enddate" class="form-control" id="enddate" value="'.$formdata->enddate.'">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="repeat" class="col-sm-2 col-form-label">'.plus_get_string("eventrepeat", "form").'</label>
                      <div class="col-sm-10 '.($formdata->repeatevent == 1?'haverepeat':'').'">
                        <select  autocomplete="off" name="repeat" id="repeatevent" class="form-control">
                          <option value="0" '.($formdata->repeatevent == 0?'selected':'').'>'.plus_get_string("norepeat", "form").'</option>
                          <option value="1" '.($formdata->repeatevent == 1?'selected':'').'>'.plus_get_string("repeat", "form").'</option>
                        </select>
                        <div class="repeatsetting '.($formdata->repeatevent == 1?'active':'').'">
                          <div class="row">
                            <div class="col-12"><h5>'.plus_get_string("eventrepeatsetting", "form").'</h5></div>
                            <div class="col-3">'.plus_get_string("startdate", "form").'</div>
                            <div class="col-9">
                              <input name="repeatstart" type="date" class="form-control" value="'.$formdata->repeatdata->repeatstart.'">
                            </div>
                            <div class="col-3 mt-2">'.plus_get_string("eventrepeatevery", "form").'</div>
                            <div class="col-9 mt-2">
                              <select name="repeatevery" class="repeatevery">
                                <option '.($formdata->repeatdata->repeatevery==0?'selected':"").' value="0">'.plus_get_string("eventrepeatweekly", "form").'</option>
                                <option '.($formdata->repeatdata->repeatevery==1?'selected':"").' value="1">'.plus_get_string("eventrepeatmonthly", "form").'</option>
                              </select>
                            </div>
                            <div class="col-12 mt-2">
                              <label><input name="repeatdays[]" '.(in_array(1, $formdata->repeatdata->repeatdays)?'checked':'').' type="checkbox" name="" value="1"> '.plus_get_string("monday", "form").'</label>&nbsp;&nbsp;&nbsp;
                              <label><input name="repeatdays[]" '.(in_array(2, $formdata->repeatdata->repeatdays)?'checked':'').' type="checkbox" name="" value="2"> '.plus_get_string("tuesday", "form").'</label>&nbsp;&nbsp;&nbsp;
                              <label><input name="repeatdays[]" '.(in_array(3, $formdata->repeatdata->repeatdays)?'checked':'').' type="checkbox" name="" value="3"> '.plus_get_string("wednesday", "form").'</label>&nbsp;&nbsp;&nbsp;
                              <label><input name="repeatdays[]" '.(in_array(4, $formdata->repeatdata->repeatdays)?'checked':'').' type="checkbox" name="" value="4"> '.plus_get_string("thursday", "form").'</label>&nbsp;&nbsp;&nbsp;
                              <label><input name="repeatdays[]" '.(in_array(5, $formdata->repeatdata->repeatdays)?'checked':'').' type="checkbox" name="" value="5"> '.plus_get_string("friday", "form").'</label>&nbsp;&nbsp;&nbsp;
                              <label><input name="repeatdays[]" '.(in_array(6, $formdata->repeatdata->repeatdays)?'checked':'').' type="checkbox" name="" value="6"> '.plus_get_string("saturday", "form").'</label>&nbsp;&nbsp;&nbsp;
                              <label><input name="repeatdays[]" '.(in_array(7, $formdata->repeatdata->repeatdays)?'checked':'').' type="checkbox" name="" value="7"> '.plus_get_string("sunday", "form").'</label>
                            </div>
                            <div class="col-3 mt-2">'.plus_get_string("enddate", "form").'</div>
                            <div class="col-9 mt-2">
                              <input name="repeatend" type="date" class="form-control" value="'.$formdata->repeatdata->repeatend.'">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <input type="hidden" name="returnto" value="'.$formdata->returnto.'"/>
                    <button type="submit" name="saveevents" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="'.($formdata->returnto=='calendar'?'/calendar':'/events').'" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
                  </form>
                </div>
              </div>
            </div>';
  $html .=  '</div>
            </div>
          </div>';
   
  $html .='
  <script type="text/javascript">
    // tinymce.init({ selector:"textarea" });
    var institutions = '.json_encode($institutions).';
    var selectedinstitution = '.json_encode($selectedinstitution).';
    var selectedteacher = '.json_encode($selectedteacher).';
    var selectedgroup = '.json_encode($selectedgroup).';
    var selectedcourse = '.json_encode($selectedcourse).';
    var formdata = '.json_encode($formdata).';
    $(document).on("change", "#repeatevent", function(){
      var qtypeval = $(this).val();
      if(qtypeval == 1){
        $(this).closest("div").addClass("haverepeat");
      } else {
        $(this).closest("div").removeClass("haverepeat");
      }
    });
    $(document).on("change", "#institutionid", function(){
      var institutionid = $(this).val();
      selectedinstitution = institutions.find(x => x.id === institutionid);
      var newoptions = \'<option value="0">My Event</option>\';
      if(selectedinstitution && Array.isArray(selectedinstitution.teachers)){
        var allteacher = selectedinstitution.teachers;
        $.each( allteacher, function( key, teacher ) {
          var sel = ``;
          if(formdata && formdata.teacherid == teacher.id){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${teacher.id}">${teacher.firstname} ${teacher.lastname}</option>`
        });
      }
      $("#teacherid").html(newoptions);
      $("#teacherid").trigger("change");
    });
    $(document).on("change", "#teacherid", function(){
      var teacherid = $(this).val();
      if(selectedinstitution && Array.isArray(selectedinstitution.teachers)){
        selectedteacher = selectedinstitution.teachers.find(x => x.id === teacherid);
      }
      var newoptions = \'<option value="0">'.plus_get_string("all", "site").' '.plus_get_string("group", "form").'</option>\';

      if(selectedteacher && Array.isArray(selectedteacher.groups)){
        var allgroup = selectedteacher.groups;
        $.each( allgroup, function( key, group ) {
          var sel = ``;
          if(formdata && formdata.groupid == group.id){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${group.id}">${group.name}</option>`
        });
      }
      $("#groupid").html(newoptions);
      $("#groupid").trigger("change");
    });
    $(document).on("change", "#groupid", function(){
      var groupid = $(this).val();
      if(selectedteacher && Array.isArray(selectedteacher.groups)){
        selectedgroup = selectedteacher.groups.find(x => x.id === groupid);
      }
      var newoptions = \'<option value="0">'.plus_get_string("all", "site").' '.plus_get_string("matter", "form").'</option>\';
      if(selectedgroup && Array.isArray(selectedgroup.courses)){
        var allcourse = selectedgroup.courses;
        $.each( allcourse, function( key, course ) {
          var sel = ``;
          if(formdata && formdata.courseid == course.id){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${course.id}">${course.fullname}</option>`
        });
      }
      $("#courseid").html(newoptions);
      $("#courseid").trigger("change");
    });
  </script>';
  return $html;
}

?>




     

