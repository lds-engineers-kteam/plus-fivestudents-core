<?php
function plus_add_group(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->name = plus_get_request_parameter("groupname", "");
  $formdata->categoryid = plus_get_request_parameter("categoryid", 0);
  $formdata->courseid = plus_get_request_parameter("courseid", array(0));
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->teachers = plus_get_request_parameter("teachers", array());
  $formdata->totalstudents = plus_get_request_parameter("totalstudents", null);
  $formdata->istraining = plus_get_request_parameter("istraining", 0);
  if(isset($_POST['savegroup'])){
    $res1 = $MOODLE->get("SaveGroup", "", $formdata);
    plus_redirect(home_url()."/groups");
    exit;
  }
  if(!empty($formdata->id)){
    $APIRES = $MOODLE->get("GetGroupById", null, array("id"=>$formdata->id));
    if($APIRES->code == 200 and $APIRES->data->id == $formdata->id){
      $formdata->name = $APIRES->data->name;
      $formdata->categoryid = $APIRES->data->categoryid;
      $formdata->courseid = explode(",", $APIRES->data->courseid);
      $formdata->groupid = $APIRES->data->groupid;
      $formdata->teachers = $APIRES->data->teachers;
      $formdata->totalstudents = $APIRES->data->totalstudents;
      $formdata->istraining = $APIRES->data->istraining;
    } 
  }
  $APIRESteachers = $MOODLE->get("BrowseTeachers", null, array("start"=>0, "limit"=>1000));
  $teachers = $APIRESteachers->data->users;
  $Allteachers = "";
  foreach ($teachers as $key => $teacher) {
  $Allteachers .= '<label class="checkbox-inline"><input type="checkbox" name="teachers[]" '.(in_array($teacher->id, $formdata->teachers)?"checked":"").' value="'.$teacher->id.'"> '.$teacher->firstname.' '.$teacher->lastname.' </label>&nbsp; &nbsp; &nbsp; ';
  }
  $catcourses = '';
  $APIRESgrades = $MOODLE->get("GetGrades", null, array());
  $catcourses .= '<div class="form-group row">
                  <label for="groupname" class="col-sm-2 col-form-label">'.plus_get_string("level", "form").' *</label>
                  <div class="col-sm-10"><select name="categoryid" class="form-control" id="categoryid" required="required"><option value=""> '.plus_get_string("select", "form").' '.plus_get_string("level", "form").'</option>';
$selectedgrade="";
$allgrades = array();

// echo "<script>console.log(" . json_encode($APIRESgrades) . ");</script>";
// echo "<script>console.log(" . $enablecourses = json_encode($APIRESgrades->INSTITUTION->enablecourses) . ");</script>";
$enabledcourse = $APIRESgrades->INSTITUTION->enablecourses;
if($enabledcourse){
    $new_array = [];
    foreach ($enabledcourse as $inner_array) {
        $new_array = array_merge($new_array, $inner_array);
    }
}


if(is_object($APIRESgrades) && is_array($APIRESgrades->data->grades)){
  foreach ($APIRESgrades->data->grades as $key => $grade) {

    array_push($allgrades, $grade);
    $sel = "";
    if($grade->id == $formdata->categoryid){
      $sel = "selected";
      $selectedgrade = $grade;
    }
    $catcourses .= '<option '.$sel.' value="'.$grade->id.'">'.$grade->name.'</option>';
  }
}
  $catcourses .= ' </select></div>
                </div>';
  // echo "<script>console.log(" .json_encode($new_array). ");</script>";
  $catcourses .= '<div class="form-group row">
                  <label for="groupname" class="col-sm-2 col-form-label">'.plus_get_string("matter", "form").' *</label>
                  <div class="col-sm-10"><select name="courseid[]" class="form-control" id="courseid" multiple required="required"><option value="">'.plus_get_string("select", "form").' '.plus_get_string("matter", "form").'</option>';
if(!empty($selectedgrade) && is_array($selectedgrade->courses)){
  foreach ($selectedgrade->courses as $key => $course) {
    
      $sel = "";
      if(in_array($course->id, $formdata->courseid)){
        $sel = "selected";
      }
      $catcourses .= '<option '.$sel.' value="'.$course->id.'">'.$course->fullname.'</option>';
    }
}
  // $catcourses .= '   <input type="text" required="required" name="groupname" class="form-control" id="institution" placeholder="Group name" value="'.$formdata->groupname.'">';
  $catcourses .= '</select></div>
                </div>';
  $html = '';
  // $html .='<pre>'.print_r($APIRESgrades->data->grades, true).'</pre>';
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
                  <h4 class="card-title">'.plus_get_string("add", "form").' '.plus_get_string("group", "form").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="groupname" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="groupname" class="form-control" id="institution" placeholder="'.plus_get_string("name", "form").'" value="'.$formdata->name.'">
                      </div>
                    </div>'.$catcourses.'<div class="form-group row">
                      <label for="teachers" class="col-sm-2">'.plus_get_string("teacher", "form").'</label>
                      <div class="col-sm-10">
                        '.$Allteachers.'
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="totalstudents" class="col-sm-2 col-form-label">'.plus_get_string("noofuser", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="number" required="required" name="totalstudents" class="form-control" id="totalstudents" placeholder="'.plus_get_string("noofuser", "form").'" value="'.$formdata->totalstudents.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="istraining" class="col-sm-2 col-form-label">'.plus_get_string("training", "group").'*</label>
                      <div class="col-sm-10">
                        <label class="checkbox-inline"><input type="checkbox" name="istraining" '.($formdata->istraining == 1?"checked":"").' value="1"> &nbsp; &nbsp; &nbsp</label>
                      </div>
                    </div>
                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <input type="hidden" name="groupid" value="'.$formdata->groupid.'"/>
                    <button type="submit" name="savegroup" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="/groups" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '</div>
            </div>
          </div>
<script>
$(document).ready(function(){
  var allgrades = '.json_encode($allgrades).';
  var newarray = '.json_encode($new_array).';

  $("#categoryid").change(function(){
    var gradeid = $(this).val();
    var selectedgrade = allgrades.find(x => x.id === gradeid);
    var newoptions = \'<option value="">'.plus_get_string("select", "form").' '.plus_get_string("matter", "form").'</option>\';
    if(selectedgrade && Array.isArray(selectedgrade.courses)){
      $.each( selectedgrade.courses, function( key, course ) {
        if (Array.isArray(newarray) && newarray.includes(course.id)) {
          newoptions += \'<option value="\'+course.id+\'">\'+course.fullname+\'</option>\'
        }
      });
    }
    $("#courseid").html(newoptions);
  });
})
</script>';
  return $html;
}