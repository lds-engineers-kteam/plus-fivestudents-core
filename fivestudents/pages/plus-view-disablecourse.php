<?php
function plus_view_disablecourse(){
  global $wp, $MOODLESESSION;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->institutionid = plus_get_request_parameter("id", 0);
  $formdata->name = "";
  $formdata->disablecourses = plus_get_request_parameter("courses", array());
  $html ='';
  $Allsubjects = '';
  if(!current_user_can('manage_plususers')){
    plus_redirect(home_url()."/users");
    exit;
  }
  if(isset($_POST['save'])){
    // echo "<pre>";
    // print_r($formdata);
    // die;
    $APIRES = $MOODLE->get("disableCourseForInstitution", null, $formdata);
    if($APIRES->code == 200){
      plus_redirect(home_url()."/users");
      exit;
    }
  }
  if(!empty($formdata->institutionid)){
    $APIRES = $MOODLE->get("getInstitutionById", null, array("id"=>$formdata->institutionid));
    if($APIRES->code == 200 and $APIRES->data->id == $formdata->institutionid){
      $formdata->name = $APIRES->data->institution;
      $formdata->disablecourses = (array) unserialize($APIRES->data->disablecourses);
    }
  } else {
    plus_redirect(home_url()."/users");
    exit;
  }

  $APIRESgrades = $MOODLE->get("GetGrades", null, array());
  if(isset($APIRESgrades) && $APIRESgrades->data && $APIRESgrades->data->grades){
    foreach ($APIRESgrades->data->grades as $key => $grades) {
      if($grades->visible != 1 || $grades->pvisible != 1){continue;}
      if(is_array($grades->courses) && sizeof($grades->courses) > 0){
        $gradeid = $grades->id;
        $Allsubjects .='<h3>'.$grades->name.'</h3>';
        foreach ($grades->courses as $course) {
          $courseid = $course->id;
          if($course->visible != 1){ continue; }
          $checked = '';
          if(isset($formdata->disablecourses[$gradeid]) && in_array($courseid, $formdata->disablecourses[$gradeid])){
            $checked = 'checked';
          }
          $Allsubjects .= '&nbsp; &nbsp; &nbsp;<label class="checkbox-inline"><input type="checkbox" name="courses['.$grades->id.'][]" '.$checked.' value="'.$course->id.'"> '.$course->fullname.' </label><br/>';        }
      }
    }
  }
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">'.plus_get_string("title", "disablecourse").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">'.plus_get_string("school", "disablecourse").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" class="form-control" id="institution" disabled placeholder="'.plus_get_string("school", "disablecourse").'" value="'.$formdata->name.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="teachers" class="col-sm-2">'.plus_get_string("subjects", "disablecourse").'</label>
                      <div class="col-sm-10">
                        '.$Allsubjects.'
                      </div>
                    </div>
                    <input type="hidden" name="id" value="'.$formdata->institutionid.'"/>
                    <button type="submit" name="save" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="/users" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
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

  $("#categoryid").change(function(){
    var gradeid = $(this).val();
    var selectedgrade = allgrades.find(x => x.id === gradeid);
    var newoptions = \'<option value="">'.plus_get_string("select", "form").' '.plus_get_string("matter", "form").'</option>\';
    if(selectedgrade && Array.isArray(selectedgrade.courses)){
      $.each( selectedgrade.courses, function( key, course ) {
        newoptions += \'<option value="\'+course.id+\'">\'+course.fullname+\'</option>\'
      });
    }
    $("#courseid").html(newoptions);
  });
})
</script>';
  return $html;
}