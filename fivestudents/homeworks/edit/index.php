<?php
  require_once("../../config.php");
  require_login();
  require_internat();
  $formdata = new stdClass();
  $formdata->id = optional_param("id", 0);
  $formdata->id = optional_param("id", 0);
  $formdata->categoryid = optional_param("categoryid", 0);
  $formdata->courseid = optional_param("courseid", 0);
  $formdata->groupid = optional_param("groupid", 0);
  $formdata->name = optional_param("homeworkname", "");
  $formdata->mode = optional_param("mode", "");
  $formdata->topic = optional_param("topic", "");
  $formdata->type = optional_param("type", 1);
  $formdata->subtopic = optional_param("subtopic", array(0));
  $formdata->quiz = optional_param("quiz", "");
  $formdata->status = optional_param("status", 1);
  $formdata->disablehints = optional_param("disablehints", 0);
  $formdata->disableexplanation = optional_param("disableexplanation", 0);
  $formdata->disabletranslation = optional_param("disabletranslation", 0);
  $formdata->disabletimer = optional_param("disabletimer", 0);
  $formdata->homeworkdate = optional_param("homeworkdate", date("Y-m-d\TH:i",time()));
  $formdata->duedate = optional_param("duedate", date("Y-m-d\TH:i",time()));
  $formdata->immediatefeedback = optional_param("immediatefeedback", 0);
  $formdata->additional_quiz = optional_param("additional_quiz", array());
  $formdata->additional_homeworkdate = optional_param("additional_homeworkdate", date("Y-m-d\TH:i",time()));
  $formdata->additional_duedate = optional_param("additional_duedate", date("Y-m-d\TH:i",time()));
  $formdata->filterarea = optional_param("filterarea", 0);
  $formdata->students = optional_param("students", array());
  if(empty($formdata->groupid)){
    redirect("{$CFG->wwwroot}/groups/", "please go to group details page to add Homework");
    exit;
  }
  $group = online_GetGroupById($formdata->groupid);
  $istutringgrades = array('14','25','26','4','5');
  $localgroup = get_group($formdata->groupid);
  if(empty($group) || empty($localgroup)){
    redirect("{$CFG->wwwroot}/groups/", "Group Not Found", 'error');
  }
  if(isset($_POST['savehomework'])){
    online_SaveHomeWork($formdata);
    redirect("{$CFG->wwwroot}/groups/details/?id={$formdata->groupid}");
    exit;
  }
  if(!empty($formdata->id)){
    $homework = online_GetHomeWorkById($formdata->id);
    $formdata->categoryid = $homework->categoryid;
    $formdata->courseid = $homework->courseid;
    $formdata->groupid = $homework->groupid;
    $formdata->name = $homework->name;
    $formdata->topic = $homework->topic;
    $formdata->mode = $homework->mode;
    $formdata->type = $homework->type;
    $formdata->subtopic = explode(",", $homework->subtopic);
    $formdata->quiz = $homework->quiz;
    $formdata->disablehints = $homework->disablehints;
    $formdata->immediatefeedback = $homework->immediatefeedback;
    $formdata->disableexplanation = $homework->disableexplanation;
    $formdata->disabletranslation = $homework->disabletranslation;
    $formdata->disabletimer = $homework->disabletimer;
    $formdata->filterarea = $homework->filterarea;
    $formdata->students = explode(",", $homework->students);
    $formdata->status = $homework->status;
    $formdata->homeworkdate = date("Y-m-d\TH:i", $homework->homeworkdate);
    $formdata->duedate = date("Y-m-d\TH:i", $homework->duedate);
  }
  $formdata->categoryid = $group->categoryid;
  $COURSES = online_GetCoursesModeDetails($group->courseid)->courses;
  echo $OUTPUT->header();
  $html='';
  // $html .='<pre>'.print_r($homework, true).'</pre>';
  $allcourse = array();
  $selectedcourse = new stdClass();
  $selectedcourse->mode = array();
  $selectedmod = new stdClass();
  $selectedmod->topics = array();
  $selectedtopic = new stdClass();
  $selectedtopic->subtopics = array();
  $selectedsubtopic = new stdClass();
  $selectedsubtopic->quizes = array();
  $selectedquiz = null;
  $topicquiz = '';
  $students = '';
  $students .= '<div class="form-group row">
                  <label for="students" class="col-sm-2 col-form-label">'.get_string("students", "form").'</label>
                  <div class="col-sm-10">';
  if($group->students && sizeof($group->students)>0){
    foreach ($group->students as $student) {
      $sel = "";
      if(in_array($student->userid, $formdata->students)){
        $sel = "checked";
      }
      $students .= '<label for="student'.$student->userid.'" class="col-form-label"><input type="checkbox" '.$sel.' name="students[]" class="form-control1" id="student'.$student->userid.'" value="'.$student->userid.'"> '.$student->lastname.' '.$student->firstname.'</label>&nbsp; &nbsp; &nbsp;';
    }
  }
  $students .= '</div>
                </div>';

  $topicquiz .= '<div class="form-group row">
                  <label for="courseid" class="col-sm-2 col-form-label">'.get_string("course", "form").'</label>
                  <div class="col-sm-10"><select name="courseid" class="form-control" id="courseid" required="required"><option value="">'.get_string("select", "form").' '.get_string("course", "form").' </option>';
  if($COURSES && sizeof($COURSES)>0){
    foreach ($COURSES as $course) {
      if(!is_array($USER->subjects) || !in_array($course->coursetype, $USER->subjects)){
        continue;
      }
      array_push($allcourse, $course);
      $sel = "";
      if($course->id == $formdata->courseid){
        $sel = "selected";
        $selectedcourse = $course;
      }
      $topicquiz .= '<option '.$sel.' value="'.$course->id.'">'.$course->fullname.'</option>';
    }
  }
  $topicquiz .= ' </select></div>
                </div>';

  $topicquiz .= '<div class="form-group row">
                  <label for="type" class="col-sm-2 col-form-label">Type</label>
                  <div class="col-sm-10"><select name="type" class="form-control" id="type" required="required">';
  $topicquiz .= '<option '.(($formdata->type == 0)?'selected':'').' value="0">'.get_string("homework", "homework_type").'</option>';
  $topicquiz .= '<option '.(($formdata->type == 1)?'selected':'').' value="1">'.get_string("assessment", "homework_type").'</option>';
  if(isset($selectedcourse->coursetype) && $selectedcourse->coursetype==1 && in_array($selectedcourse->category, $istutringgrades)){
    $topicquiz .= '<option '.(($formdata->type == 2)?'selected':'').' value="2">'.get_string("tutoring", "homework_type").'</option>';
  }

  $topicquiz .= ' </select><input type="hidden" class="form-control" id="mode" value="challenge"  name="mode"></div>
                </div>';
  $topicquiz .= '<div class="form-group row">
                  <label for="topic" class="col-sm-2 col-form-label">'.get_string("semester", "form").' *</label>
                  <div class="col-sm-10"><select name="topic" class="form-control" id="topic" required="required"><option value="">'.get_string("select", "form").' '.get_string("semester", "form").'</option>';
  if(!empty($selectedmod) && sizeof($selectedmod->topics)>0){
    foreach ($selectedmod->topics as $topic) {
      $sel = "";
      if($topic->id == $formdata->topic){
        $sel = "selected";
        $selectedtopic = $topic;
      }
      $topicquiz .= '<option '.$sel.' value="'.$topic->id.'">'.$topic->name.'</option>';
    }
  }
  $topicquiz .= ' </select></div>
                </div>';
  $subtopicquiz = '';
  $subtopictitle = get_string("lesson", "form");
  if(!empty($selectedtopic) && sizeof($selectedtopic->subtopics)>0){
    foreach ($selectedtopic->subtopics as $subtopic) {
      $sel = "";
      if(in_array($subtopic->id, $formdata->subtopic) ){
        $sel = "selected";
        $selectedsubtopic = $subtopic;
        if(sizeof($subtopic->subtopics)>0){
          $subtopictitle = get_string("component", "form");
        }
      }
      $subtopicquiz .= '<option '.$sel.' value="'.$subtopic->id.'">'.$subtopic->name.'</option>';
    }
  }
  $topicquiz .= '<div id="allsubtopic">
                  <div class="form-group row">
                  <label for="subtopic" class="col-sm-2 col-form-label">'.$subtopictitle.' *</label>
                  <div class="col-sm-10"><select name="subtopic[]" class="form-control" id="subtopic" required="required">'.$subtopicquiz.'</select></div>
                </div>
                </div>';
  $topicquiz .= '<div class="form-group row">
                  <label for="quiz" class="col-sm-2 col-form-label">'.get_string("quiz", "form").' *
          <button type="button" class="btn btn-lg viewtask" data-id="">'.get_string("view", "form").'</button>
          </label>
                  <div class="col-sm-10"><select name="quiz" class="form-control" id="quiz" required="required">';
  if(!empty($selectedsubtopic) && sizeof($selectedsubtopic->quizes)>0){
    foreach ($selectedsubtopic->quizes as $quiz) {
      $sel = "";
      if($quiz->cm == $formdata->quiz){
        $sel = "selected";
      }
      $topicquiz .= '<option '.$sel.' value="'.$quiz->cm.'">'.$quiz->name.'</option>';
    }
  }
  $topicquiz .= '</select></div>
                </div>';
  $adittionalhomeworkshtml = '<div class="row">
                          <div class="form-group col-sm-4">
                            <label for="exampleInputUsername1">'.get_string("group", "form").'*</label>
                            <select name="additional_quiz[]" class="form-control" required="required">';
  foreach ($group->relatedGroups as $key => $rgroup) {
      $adittionalhomeworkshtml .= '<option value="'.$rgroup->id.'">'.$rgroup->name.'</option>';
  }                            
  $adittionalhomeworkshtml .=  '</select>
                          </div>
                          <div class="form-group col-sm-3">
                            <label for="exampleInputUsername1">'.get_string("publishdate", "form").' *</label>
                            <input type="text" required="required" name="additional_homeworkdate[]" class="form-control customdatepicker1">
                          </div>
                          <div class="form-group col-sm-3">
                            <label for="exampleInputUsername1">'.get_string("duedate", "form").' *</label>
                            <input type="text" required="required" name="additional_duedate[]" class="form-control customdatepicker1">
                          </div>
                          <div class="col-sm-2 flex-vcenter">
                            <span class="btn btn-danger removegroup"><i class="mdi mdi-minus"></i></span>
                          </div>
                        </div>';
$adittionalhomeworks='';
if(sizeof($group->relatedGroups) > 0){
  $adittionalhomeworks = '<div class="form-group row">
                      <div class="col-sm-2 flex-vend">
                        <span id="addmoregroup" class="btn btn-primary"><i class="mdi mdi-plus"></i></span>
                      </div>
                      <div class="col-sm-10" id="additionaltask">'.$adittionalhomeworks.'</div>
                    </div>';
}
  // $html .=  '<div class="row table-responsive">'.((is_object($APIRES)?json_encode($APIRES):$APIRES)).'</div>';  
  // $html .=  '<div class="row table-responsive">'.((is_object($selectedmod)?json_encode($selectedmod):$selectedmod)).'</div>';  

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
                  <h4 class="card-title">'.get_string("add", "form").' '.get_string("homework", "site").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="homeworkname" class="col-sm-2 col-form-label">'.get_string("name", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="homeworkname" class="form-control" id="homeworkname" placeholder="'.get_string("name", "form").'" value="'.$formdata->name.'">
                      </div>
                    </div>'.$topicquiz.'
                    <div class="form-group row for_lessons">
                      <label for="disablehints" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disablehints == 1?' checked="checked" ':'').' id="disablehints" name="disablehints" /></label>
                      <div class="col-sm-10">
                        <label for="disablehints" class="col-form-label">'.get_string("disablehints", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row for_lessons">
                      <label for="disableexplanation" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disableexplanation == 1?' checked="checked" ':'').' id="disableexplanation" name="disableexplanation" /></label>
                      <div class="col-sm-10">
                        <label for="disableexplanation" class="col-form-label">'.get_string("disableexplanation", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row for_lessons">
                      <label for="disabletranslation" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disabletranslation == 1?' checked="checked" ':'').' id="disabletranslation" name="disabletranslation" /></label>
                      <div class="col-sm-10">
                        <label for="disabletranslation" class="col-form-label">'.get_string("disabletranslation", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row for_lessons for_challenge">
                      <label for="disabletimer" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disabletimer == 1?' checked="checked" ':'').' id="disabletimer" name="disabletimer" /></label>
                      <div class="col-sm-10">
                        <label for="disabletimer" class="col-form-label">'.get_string("disabletimer", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="homeworkdate" required="required" class="col-sm-2 col-form-label">'.get_string("publishdate", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="homeworkdate" class="form-control" id="homeworkdate" value="'.$formdata->homeworkdate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="duedate" required="required" class="col-sm-2 col-form-label">'.get_string("duedate", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="duedate" class="form-control" id="duedate" value="'.$formdata->duedate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="immediatefeedback" class="col-sm-2 col-form-label">'.get_string("imidiatefeedback", "form").'</label>
                      <div class="col-sm-10">
                        <input type="checkbox" '.($formdata->immediatefeedback==1?' checked ':'').' name="immediatefeedback" class="form-control1" id="immediatefeedback" value="1">
                      </div>
                    </div>
                    <div class="form-group row filterarea">
                      <label class="col-sm-2 col-form-label">'.get_string("filterarea", "form").'</label>
                      <div class="col-sm-10">
                        <label for="filterareanone" class="col-form-label text-right"><input type="radio" value="0" '.($formdata->filterarea == 0?' checked="checked" ':'').' id="filterareanone" name="filterarea" /> '.get_string("none", "form").'</label>&nbsp;&nbsp;&nbsp;
                        <label for="filterareainclude" class="col-form-label text-right"><input type="radio" value="1" '.($formdata->filterarea == 1?' checked="checked" ':'').' id="filterareainclude" name="filterarea" /> '.get_string("include", "form").'</label>&nbsp;&nbsp;&nbsp;
                        <label for="filterareaexclude" class="col-form-label text-right"><input type="radio" value="2" '.($formdata->filterarea == 2?' checked="checked" ':'').' id="filterareaexclude" name="filterarea" /> '.get_string("excludefromimidiatefeedback", "form").'</label>
                      </div>
                    </div>'.$students.'
                    <div class="form-group row">
                      <label for="status" class="col-sm-2 col-form-label">'.get_string("status", "form").'</label>
                      <div class="col-sm-10">
                        <select name="status" class="form-control" id="status" required="required">
                          <option '.($formdata->status==1?'selected':'').' value="1">'.get_string("statuspublish", "form").'</option>
                          <option '.($formdata->status!=1?'selected':'').' value="0">'.get_string("statusplanned", "form").'</option>
                        </select>
                      </div>
                    </div>'.$adittionalhomeworks.'
                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <input type="hidden" name="categoryid" value="'.$formdata->categoryid.'"/>
                    <input type="hidden" name="groupid" value="'.$formdata->groupid.'"/>
                    <button type="submit" name="savehomework" class="btn btn-primary mr-2">'.get_string("save", "form").'</button>
                    <a href="'.(empty($formdata->groupid)?$CFG->wwwroot.'/homeworks':$CFG->wwwroot.'/groups/details/?id='.$formdata->groupid).'" class="btn btn-warning">'.get_string("return", "form").'</a>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '</div>
            </div>
          </div>';
$html .= '<div id="questionViewer" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">'.get_string("questionviewer", "form").'</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer text-right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>';    

$html .= '<script src="https://getfirebug.com/firebug-lite-debug.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link href="https://code.jquery.com/ui/1.11.4/themes/south-street/jquery-ui.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.css" rel="stylesheet"/>';

  $html .=  '
  <script>
    var adittionalhomeworkshtml = `'.$adittionalhomeworkshtml.'`;
  $(document).ready(function(){
    var allcourse = '.json_encode($allcourse).';
    var allmode = '.json_encode($selectedcourse->mode).';
    var selectedcourse = '.json_encode($selectedcourse).';
    var alltopics = '.json_encode($selectedmod->topics).';
    var allsubtopic = '.json_encode($selectedtopic->subtopics).';
    var formdata = '.json_encode($formdata).';
    var istutringgrades = '.json_encode($istutringgrades).';
    var allmods = ["quest", "challenge", "quest"];
    var additionalallsubtopic = {};
    $("#type").change(function(){
      var type = $(this).val();
      var selmode = allmods[type];
      $("#mode").val(selmode);
      $("#mode").trigger("change");
    });
    $("#courseid").change(function(){
      var courseid = $(this).val();
      selectedcourse = allcourse.find(x => x.id === courseid);
      var newoptions = \'<option value="">'.get_string("select", "form").' Mod</option>\';
      var newtypes = \'\';
      if(selectedcourse){
        if(Array.isArray(selectedcourse.mode)){
          allmode = selectedcourse.mode;
        //   $.each( allmode, function( key, mode ) {
        //     var sel = ``;
        //     if(formdata && formdata.mode == mode.type){
        //       sel = `Selected`;
        //     }
        //     newoptions += `<option ${sel} value="${mode.type}">${mode.name}</option>`
        //   });
        } else {
          allmode = [];
        }
        newtypes += `<option value="0">'.get_string("homework", "homework_type").'</option>`;
        newtypes += `<option value="1">'.get_string("assessment", "homework_type").'</option>`;
        if(istutringgrades.includes(selectedcourse.category) && selectedcourse.coursetype==1){
          newtypes += `<option value="2">'.get_string("tutoring", "homework_type").'</option>`;
          console.log("newtypes tutoring- ", newtypes)
        }
      } else {
        allmode = [];
      }
      console.log("newoptions- ", newoptions)
      console.log("newtypes- ", newtypes)
      // console.log("selectedcourse- ", selectedcourse)
      // $("#mode").html(newoptions);
      $("#type").html(newtypes);
      $("#type").trigger("change");
    });
    $("#mode").change(function(){
      modechanged();
      var mode = $(this).val();
      console.log("mode- ", mode)
      console.log("allmode- ", allmode)
      var selectedmode = allmode.find(x => x.type === mode);
      console.log("selectedmode- ", selectedmode)
      var newoptions = \'\';
      if(selectedmode && Array.isArray(selectedmode.topics)){
        alltopics = selectedmode.topics;
        $.each( selectedmode.topics, function( key, topic ) {
          var sel = ``;
          if(formdata && formdata.topic == topic.id){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${topic.id}">${topic.name}</option>`
        });
      } else {
        alltopics = [];
      }
      console.log("selectedmode- ", selectedmode)
      console.log("newoptions- ", newoptions)
      $("#topic").html(newoptions);
      $("#topic").trigger("change");
    });
    $("#topic").change(function(){
      var topicid = $(this).val();
      console.log("alltopics- ", alltopics)
      console.log("topicid- ", topicid)
      var selectedtopic = alltopics.find(x => x.id === topicid);
      console.log("selectedtopic- ", selectedtopic)
      var newoptions = \'\';
      if(selectedtopic && Array.isArray(selectedtopic.subtopics)){
        allsubtopic = selectedtopic.subtopics;
        $.each( selectedtopic.subtopics, function( key, topic ) {
          var sel = ``;
          if(formdata && formdata.subtopic.includes(topic.id)){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${topic.id}">${topic.name}</option>`
        });
      } else {
        allsubtopic = [];

      }
      console.log("selectedtopic- ", selectedtopic)
      console.log("newoptions- ", newoptions)
      $("#subtopic").html(newoptions);
      $("#subtopic").trigger("change");
    });
    $("#subtopic").change(function(){
      var subtopicid = $(this).val();
      var elementid = $(this).attr("id");
      console.log("subtopicid- ", subtopicid)
      console.log("elementid- ", elementid)
      var selectedsubtopic = allsubtopic.find(x => x.id === subtopicid);
      console.log("selectedsubtopic- ", selectedsubtopic)
      var newoptions = \'\';
      var selectedtype = $("#type").val();
      if(selectedsubtopic && Array.isArray(selectedsubtopic.quizes)){
        $.each( selectedsubtopic.quizes, function( key, quiz ) {
          if(selectedtype == 2 ||(selectedtype ==1 && selectedcourse.coursetype == 1)){
            if(quiz.istutoring != 1 ){return;}
          } else {
            if(quiz.istutoring == 1 ){return;}
          }
          console.log("selectedcourse1", selectedcourse);
          console.log("quiz", quiz);
          var sel = ``;
          if(formdata && formdata.quiz == quiz.cm){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${quiz.cm}">${quiz.name}</option>`;
        });
      }
      $("#quiz").html(newoptions);
      $("#quiz").trigger("change");
      $(".additional_subtopicselements").remove();
      if(selectedsubtopic && Array.isArray(selectedsubtopic.subtopics) && selectedsubtopic.subtopics.length > 0){
        
        var newelement = `<div class="form-group row additional_subtopicselements"><label for="subtopic${selectedsubtopic.id}" class="col-sm-2 col-form-label">'.get_string("lesson", "form").' *</label><div class="col-sm-10"><select name="subtopic[]" class="form-control" id="subtopic${selectedsubtopic.id}" additional_subtopics required="required">`;
        var hasmoresubtopic = false;
        $.each( selectedsubtopic.subtopics, function( key, subtopic ) {
          additionalallsubtopic[subtopic.id] = subtopic;
          console.log(`hasmoresubtopic 1 FOR ${elementid}: `, hasmoresubtopic)
          if(!hasmoresubtopic && Array.isArray(subtopic.subtopics) && subtopic.subtopics.length > 0){
            hasmoresubtopic = true;
          }
          var sel = ``;
          if(formdata && formdata.subtopic.includes(subtopic.id)){
            sel = `Selected`;
          }
          newelement += `<option ${sel} value="${subtopic.id}">${subtopic.name}</option>`;
        });
        newelement += `</select></div></div>`;
        console.log(`FINAL hasmoresubtopic 1 FOR ${elementid}: `, hasmoresubtopic)
        $(`[for="${elementid}"]`).html("'.get_string("component", "form").'");
        $("#allsubtopic").append(newelement);
        $(`#subtopic${selectedsubtopic.id}`).trigger("change");
      } else {
        $(`[for="${elementid}"]`).html("'.get_string("lesson", "form").'");
      }
    });
    $(document).on("change", "[additional_subtopics]", function(){
      var subtopicid = $(this).val();
      var elementid = $(this).attr("id");
      console.log("subtopicid- ", subtopicid);
      console.log("additionalallsubtopic- ", additionalallsubtopic);
      var selectedsubtopic = additionalallsubtopic[subtopicid];
      console.log("selectedsubtopic-1- ", selectedsubtopic)
      console.log("formdata-1- ", formdata)
      var newoptions = \'\';
      var selectedtype = $("#type").val();
      if(selectedsubtopic && Array.isArray(selectedsubtopic.quizes)){
        $.each( selectedsubtopic.quizes, function( key, quiz ) {
          if(selectedtype == 2 || (selectedtype ==1 && selectedcourse.coursetype == 1)){
            if(quiz.istutoring != 1 ){return;}
          } else {
            if(quiz.istutoring == 1 ){return;}
          }
          var sel = ``;
          console.log("formdata-1-quiz ", quiz)
          if(formdata && formdata.quiz == quiz.cm){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${quiz.cm}">${quiz.name}</option>`;
        });
      }
      console.log("formdata-1-quiz updated")
      console.log("formdata-1-quiz updated newoptions", newoptions);
      $("#quiz").html(newoptions);
      $("#quiz").trigger("change");
      if(selectedsubtopic && Array.isArray(selectedsubtopic.subtopics) && selectedsubtopic.subtopics.length > 0){
        $(`[for="${elementid}"]`).html("'.get_string("component", "form").'");
        var newelement = `<div class="form-group row additional_subtopicselements"><label for="subtopic${selectedsubtopic.id}" class="col-sm-2 col-form-label">'.get_string("lesson", "form").' *</label><div class="col-sm-10"><select name="subtopic[]" class="form-control" id="subtopic${selectedsubtopic.id}" additional_subtopics required="required">`;
        var hasmoresubtopic = false;
        $.each( selectedsubtopic.subtopics, function( key, subtopic ) {
          additionalallsubtopic[subtopic.id] = subtopic;
          console.log(`hasmoresubtopic FOR ${elementid}: `, hasmoresubtopic)
          if(!hasmoresubtopic && Array.isArray(subtopic.subtopics) && subtopic.subtopics.length > 0){
            hasmoresubtopic = true;
          }
          var sel = ``;
          if(formdata && formdata.subtopic.includes(subtopic.id)){
            sel = `Selected`;
          }
          newelement += `<option ${sel} value="${subtopic.id}">${subtopic.name}</option>`;
        });
        newelement += `</select></div></div>`;
        console.log(`FINAL hasmoresubtopic FOR ${elementid}: `, hasmoresubtopic)
        $(`[for="${elementid}"]`).html("'.get_string("component", "form").'");
        $("#allsubtopic").append(newelement);
        $(`#subtopic${selectedsubtopic.id}`).trigger("change");
      } else {
        $(`[for="${elementid}"]`).html("'.get_string("lesson", "form").'");
      }
    });
    $("#addmoregroup").click(function(){
      console.log("clicked");
      $("#additionaltask").append(adittionalhomeworkshtml);
        $(".customdatepicker").datetimepicker({dateFormat: "yy-mm-dd",
                   timeFormat: "hh:mm",
                   separator: "T"
       });
    });
    $("#additionaltask").on("click", ".removegroup", function (){
      console.log("this- ", $(this));
      $(this).closest(".row").remove();
    });
    $("#quiz").change(function(){
      console.log("quiz changed--------------------------------- ")
      var quizid = $("#quiz").val();
      console.log("quizid- ", quizid)
      if(quizid){
       $(".viewtask").show();
      } else {
       $(".viewtask").hide();
      }
    });
    $(".viewtask").click(function(){
      var quizid = $("#quiz").val();
      var reqargs = {
        "quizid": quizid
      };
      var getquestionssetting = getAPIRequest("getQuizes",reqargs);
      $.ajax(getquestionssetting).done(function (response) {
        console.log("response", response);
        if(response.code == 200){
          if(response.data.questionstring != ""){
            $("#questionViewer").find(".modal-body").html(response.data.questionstring);
            $("#questionViewer").modal("show");
          } else {
            displayToast("'.get_string("success", "form").'","'.get_string("questionnotfound", "form").'", "info");
          }
        } else {
          displayToast("'.get_string("failed", "form").'","'.get_string("failedtogetquestion", "form").'", "error");        
        }
      });          
    });
    $(".viewtask").hide();
    $("#courseid").trigger("change");
  });
  function modechanged(){
      var selectedmode1 = $("#mode").val();
      // if(selectedmode1 == "quest"){
      //   $(".for_lessons").show();
      // } else {
      //   $(".for_lessons").hide();
      //   $(".for_lessons input[type=\"checkbox\"]").prop("checked", false);
      // }
      if(selectedmode1 == "challenge"){
        $(".for_challenge").show();
      } else {
        $(".for_challenge").hide();
        $(".for_challenge input[type=\"checkbox\"]").prop("checked", false);
      }
  }
             
  
  </script>';


  echo $html;
  echo $OUTPUT->footer();
