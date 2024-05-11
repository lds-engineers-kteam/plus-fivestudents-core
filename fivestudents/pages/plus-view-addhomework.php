<?php
function plus_add_homework(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->categoryid = plus_get_request_parameter("categoryid", 0);
  $formdata->courseid = plus_get_request_parameter("courseid", 0);
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->name = plus_get_request_parameter("homeworkname", "");
  $formdata->mode = plus_get_request_parameter("mode", "");
  $formdata->topic = plus_get_request_parameter("topic", "");
  $formdata->type = plus_get_request_parameter("type", 1);
  $formdata->subtopic = plus_get_request_parameter("subtopic", array(0));
  $formdata->quiz = plus_get_request_parameter("quiz", "");
  $formdata->status = plus_get_request_parameter("status", 1);
  $formdata->disablehints = plus_get_request_parameter("disablehints", 0);
  $formdata->disableexplanation = plus_get_request_parameter("disableexplanation", 0);
  $formdata->disabletranslation = plus_get_request_parameter("disabletranslation", 0);
  $formdata->disabletimer = plus_get_request_parameter("disabletimer", 0);
  $formdata->homeworkdate = plus_get_request_parameter("homeworkdate", date("Y-m-d\TH:i",time()));
  $formdata->duedate = plus_get_request_parameter("duedate", date("Y-m-d\TH:i",time()));
  $formdata->immediatefeedback = plus_get_request_parameter("immediatefeedback", 0);
  $formdata->additional_quiz = plus_get_request_parameter("additional_quiz", array());
  $formdata->additional_homeworkdate = plus_get_request_parameter("additional_homeworkdate", date("Y-m-d\TH:i",time()));
  $formdata->additional_duedate = plus_get_request_parameter("additional_duedate", date("Y-m-d\TH:i",time()));
  $formdata->filterarea = plus_get_request_parameter("filterarea", 0);
  $formdata->students = plus_get_request_parameter("students", array());
  if(empty($formdata->groupid)){
    plus_redirect(home_url()."/groups/");
    exit;
  }
  $APIRES = $MOODLE->get("GetGroupById", null, array("id"=>$formdata->groupid));
  $istutringgrades = array('14','25','26','4','5');
  $html='';
  $COURSE = null;
  $COURSES = null;

  if(isset($_POST['savehomework'])){
    if($formdata->mode == "challenge" && $formdata->filterarea == 2){
      $formdata->filterarea = 0;
    }
    $APIREShomework = $MOODLE->get("SaveHomeWork", null, $formdata);
    // echo "<pre>";
    // // print_r($_POST);
    // print_r($formdata);
    // print_r($APIREShomework);
    // echo "</pre>";die;
    plus_redirect(home_url().'/group-details/?id='.$formdata->groupid);
    exit;
  }
  if(!empty($formdata->id)){
    $APIRES1 = $MOODLE->get("GetHomeWorkById", null, array("id"=>$formdata->id));
    if($APIRES1->code == 200 and $APIRES1->data->id == $formdata->id){
      $formdata->categoryid = $APIRES1->data->categoryid;
      $formdata->courseid = $APIRES1->data->courseid;
      $formdata->groupid = $APIRES1->data->groupid;
      $formdata->name = $APIRES1->data->name;
      $formdata->topic = $APIRES1->data->topic;
      $formdata->mode = $APIRES1->data->mode;
      $formdata->type = $APIRES1->data->type;
      $formdata->subtopic = explode(",", $APIRES1->data->subtopic);
      $formdata->quiz = $APIRES1->data->quiz;
      $formdata->disablehints = $APIRES1->data->disablehints;
      $formdata->immediatefeedback = $APIRES1->data->immediatefeedback;
      $formdata->disableexplanation = $APIRES1->data->disableexplanation;
      $formdata->disabletranslation = $APIRES1->data->disabletranslation;
      $formdata->disabletimer = $APIRES1->data->disabletimer;
      $formdata->filterarea = $APIRES1->data->filterarea;
      $formdata->students = explode(",", $APIRES1->data->students);
      $formdata->status = $APIRES1->data->status;
      $formdata->homeworkdate = date("Y-m-d\TH:i", $APIRES1->data->homeworkdate);
      $formdata->duedate = date("Y-m-d\TH:i", $APIRES1->data->duedate);
    } 
  }
  if($APIRES->code == 200 and $APIRES->data->id == $formdata->groupid){
    $group = $APIRES->data;
    $formdata->categoryid = $group->categoryid;
    $APIREScourse = $MOODLE->get("GetCoursesModeDetails", null, array("ids"=>$group->courseid,"categoryid"=>$group->categoryid));
    if($APIREScourse->code == 200){
      $COURSES = $APIREScourse->data->courses;
    }
  } else {
    plus_redirect(home_url()."/groups/");
    exit;
  }
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
                  <label for="students" class="col-sm-2 col-form-label">'.plus_get_string("students", "form").'</label>
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
                  <label for="courseid" class="col-sm-2 col-form-label">'.plus_get_string("course", "form").'</label>
                  <div class="col-sm-10"><select name="courseid" class="form-control" id="courseid" required="required"><option value="">'.plus_get_string("select", "form").' '.plus_get_string("course", "form").' </option>';
  if($COURSES && sizeof($COURSES)>0){
    foreach ($COURSES as $course) {
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
  $topicquiz .= '<option '.(($formdata->type == 0)?'selected':'').' value="0">'.plus_get_string("homework", "homework_type").'</option>';
  $topicquiz .= '<option '.(($formdata->type == 1)?'selected':'').' value="1">'.plus_get_string("assessment", "homework_type").'</option>';
  if($selectedcourse && $selectedcourse->coursetype==1 && in_array($selectedcourse->category, $istutringgrades)){
    $topicquiz .= '<option '.(($formdata->type == 2)?'selected':'').' value="2">'.plus_get_string("tutoring", "homework_type").'</option>';
  }
  $topicquiz .= ' </select><input type="hidden" class="form-control" id="mode" value="challenge"  name="mode"></div>
                </div>';
  $topicquiz .= '<div class="form-group row">
                  <label for="topic" class="col-sm-2 col-form-label">'.plus_get_string("semester", "form").' *</label>
                  <div class="col-sm-10"><select name="topic" class="form-control" id="topic" required="required">';
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
  $subtopictitle = plus_get_string("lesson", "form");
  if(!empty($selectedtopic) && sizeof($selectedtopic->subtopics)>0){
    foreach ($selectedtopic->subtopics as $subtopic) {
      $sel = "";
      if(in_array($subtopic->id, $formdata->subtopic) ){
        $sel = "selected";
        $selectedsubtopic = $subtopic;
        if(sizeof($subtopic->subtopics)>0){
          $subtopictitle = plus_get_string("component", "form");
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
                  <label for="quiz" class="col-sm-2 col-form-label">'.plus_get_string("quiz", "form").' *
				  <button type="button" class="btn btn-lg viewtask" data-id="">'.plus_get_string("view", "form").'</button>
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
                            <label for="exampleInputUsername1">'.plus_get_string("group", "form").'*</label>
                            <select name="additional_quiz[]" class="form-control" required="required">';
  foreach ($group->relatedGroups as $key => $rgroup) {
      $adittionalhomeworkshtml .= '<option value="'.$rgroup->id.'">'.$rgroup->name.'</option>';
  }                            
  $adittionalhomeworkshtml .=  '</select>
                          </div>
                          <div class="form-group col-sm-3">
                            <label for="exampleInputUsername1">'.plus_get_string("publishdate", "form").' *</label>
                            <input type="text" required="required" name="additional_homeworkdate[]" class="form-control customdatepicker1">
                          </div>
                          <div class="form-group col-sm-3">
                            <label for="exampleInputUsername1">'.plus_get_string("duedate", "form").' *</label>
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
                  <h4 class="card-title">'.plus_get_string("add", "form").' '.plus_get_string("homework", "site").'</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="homeworkname" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="homeworkname" class="form-control" id="homeworkname" placeholder="'.plus_get_string("name", "form").'" value="'.$formdata->name.'">
                      </div>
                    </div>'.$topicquiz.'
                    <div class="form-group row for_lessons only_lessons">
                      <label for="disablehints" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disablehints == 1?' checked="checked" ':'').' id="disablehints" name="disablehints" /></label>
                      <div class="col-sm-10">
                        <label for="disablehints" class="col-form-label">'.plus_get_string("disablehints", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row for_lessons only_lessons">
                      <label for="disableexplanation" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disableexplanation == 1?' checked="checked" ':'').' id="disableexplanation" name="disableexplanation" /></label>
                      <div class="col-sm-10">
                        <label for="disableexplanation" class="col-form-label">'.plus_get_string("disableexplanation", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row for_lessons only_lessons">
                      <label for="disabletranslation" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disabletranslation == 1?' checked="checked" ':'').' id="disabletranslation" name="disabletranslation" /></label>
                      <div class="col-sm-10">
                        <label for="disabletranslation" class="col-form-label">'.plus_get_string("disabletranslation", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row for_lessons for_challenge">
                      <label for="disabletimer" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disabletimer == 1?' checked="checked" ':'').' id="disabletimer" name="disabletimer" /></label>
                      <div class="col-sm-10">
                        <label for="disabletimer" class="col-form-label">'.plus_get_string("disabletimer", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="homeworkdate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("publishdate", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="homeworkdate" class="form-control" id="homeworkdate" value="'.$formdata->homeworkdate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="duedate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("duedate", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="duedate" class="form-control" id="duedate" value="'.$formdata->duedate.'">
                      </div>
                    </div>
                    <div class="form-group row is_imidiatefeedback">
                      <label for="immediatefeedback" class="col-sm-2 col-form-label">'.plus_get_string("imidiatefeedback", "form").'</label>
                      <div class="col-sm-10">
                        <input type="checkbox" '.($formdata->immediatefeedback==1?' checked ':'').' name="immediatefeedback" class="form-control1" id="immediatefeedback" value="1">
                      </div>
                    </div>
                    <div class="form-group row filterarea">
                      <label class="col-sm-2 col-form-label">'.plus_get_string("filterarea", "form").'</label>
                      <div class="col-sm-10">
                        <label for="filterareanone" class="col-form-label text-right"><input type="radio" value="0" '.($formdata->filterarea == 0?' checked="checked" ':'').' id="filterareanone" name="filterarea" /> '.plus_get_string("none", "form").'</label>&nbsp;&nbsp;&nbsp;
                        <label for="filterareainclude" class="col-form-label text-right"><input type="radio" value="1" '.($formdata->filterarea == 1?' checked="checked" ':'').' id="filterareainclude" name="filterarea" /> '.plus_get_string("include", "form").'</label>&nbsp;&nbsp;&nbsp;
                        <label for="filterareaexclude" class="col-form-label text-right is_imidiatefeedback"><input type="radio" value="2" '.($formdata->filterarea == 2?' checked="checked" ':'').' id="filterareaexclude" name="filterarea" /> '.plus_get_string("excludefromimidiatefeedback", "form").'</label>
                      </div>
                    </div>'.$students.'
                    <div class="form-group row">
                      <label for="status" class="col-sm-2 col-form-label">'.plus_get_string("status", "form").'</label>
                      <div class="col-sm-10">
                        <select name="status" class="form-control" id="status" required="required">
                          <option '.($formdata->status==1?'selected':'').' value="1">'.plus_get_string("statuspublish", "form").'</option>
                          <option '.($formdata->status!=1?'selected':'').' value="0">'.plus_get_string("statusplanned", "form").'</option>
                        </select>
                      </div>
                    </div>'.$adittionalhomeworks.'
                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <input type="hidden" name="categoryid" value="'.$formdata->categoryid.'"/>
                    <input type="hidden" name="groupid" value="'.$formdata->groupid.'"/>
                    <button type="submit" name="savehomework" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="'.(empty($formdata->groupid)?'/homework':'/group-details/?id='.$formdata->groupid).'" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
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
                    <h5 class="modal-title">'.plus_get_string("questionviewer", "form").'</h5>
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
  ?>
<script src="https://getfirebug.com/firebug-lite-debug.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link href="https://code.jquery.com/ui/1.11.4/themes/south-street/jquery-ui.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.1/jquery-ui-timepicker-addon.css" rel="stylesheet"/>
<?php 
  $html .=  '
  <script>
    var adittionalhomeworkshtml = `'.$adittionalhomeworkshtml.'`;
  $(document).ready(function(){
    var group = '.json_encode($group).';
    var allcourse = '.json_encode($allcourse).';
    var allmode = '.json_encode($selectedcourse->mode).';
    var selectedcourse = '.json_encode($selectedcourse).';
    var alltopics = '.json_encode($selectedmod->topics).';
    var allsubtopic = '.json_encode($selectedtopic->subtopics).';
    var formdata = '.json_encode($formdata).';
    var istutringgrades = '.json_encode($istutringgrades).';
    var primarygrades = ["14","4","5","25","26"];

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
      var newoptions = \'<option value="">'.plus_get_string("select", "form").' Mod</option>\';
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
        newtypes += `<option ${formdata.type=="0"?"selected":""} value="0">'.plus_get_string("homework", "homework_type").'</option>`;
        newtypes += `<option ${formdata.type=="1"?"selected":""} value="1">'.plus_get_string("assessment", "homework_type").'</option>`;
        if(istutringgrades.includes(selectedcourse.category) && selectedcourse.coursetype == 1){
          newtypes += `<option ${formdata.type=="2"?"selected":""} value="2">'.plus_get_string("tutoring", "homework_type").'</option>`;
        }
      } else {
        allmode = [];
      }
      $("#type").html(newtypes);
      $("#type").trigger("change");
      
    });
    $("#mode").change(function(){
      modechanged();
      var mode = $(this).val();
      var selectedmode = allmode.find(x => x.type === mode);
      var newoptions = \'\';
      if(selectedmode && Array.isArray(selectedmode.topics)){
        alltopics = selectedmode.topics;
        $.each( selectedmode.topics, function( key, topic ) {
          var sel = ``;
          if(Array.isArray(topic.forgrades) && topic.forgrades.length > 0 && !topic.forgrades.includes(group.categoryid)){
            console.log("skipped topic - ", topic);
            return;
          }
          if(formdata && formdata.topic == topic.id){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${topic.id}">${topic.name}</option>`
        });
      } else {
        alltopics = [];
      }
      $("#topic").html(newoptions);
      $("#topic").trigger("change");
    });
    $("#topic").change(function(){
      var topicid = $(this).val();
      var selectedtopic = alltopics.find(x => x.id === topicid);
      var newoptions = \'\';
      if(selectedtopic && Array.isArray(selectedtopic.subtopics)){
        allsubtopic = selectedtopic.subtopics;
        $.each( selectedtopic.subtopics, function( key, topic ) {
          var sel = ``;
          if(Array.isArray(topic.forgrades) && topic.forgrades.length > 0 && !topic.forgrades.includes(group.categoryid)){
            console.log("skipped topic - ", topic);
            return;
          }
          if(formdata && formdata.subtopic.includes(topic.id)){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${topic.id}">${topic.name}</option>`
        });
      } else {
        allsubtopic = [];

      }
      $("#subtopic").html(newoptions);
      $("#subtopic").trigger("change");
    });
    $("#subtopic").change(function(){
      var subtopicid = $(this).val();
      var elementid = $(this).attr("id");
      var selectedsubtopic = allsubtopic.find(x => x.id === subtopicid);
      console.log("selectedsubtopic- ", selectedsubtopic);
      var newoptions = \'\';
      var selectedtype = $("#type").val();

      if(selectedsubtopic && Array.isArray(selectedsubtopic.quizes)){
        $.each( selectedsubtopic.quizes, function( key, quiz ) {
          if(Array.isArray(quiz.forgrades) && quiz.forgrades.length > 0 && !quiz.forgrades.includes(group.categoryid)){
            console.log("skipped quiz - ", quiz);
            return;
          }
          if(selectedtype == 2 || (selectedtype ==1 && primarygrades.includes(selectedcourse.category) && selectedcourse.coursetype == 1) ){
            if(quiz.istutoring != 1 ){return;}
          } else {
            if(quiz.istutoring == 1 ){return;}
          }
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
        
        var newelement = `<div class="form-group row additional_subtopicselements"><label for="subtopic${selectedsubtopic.id}" class="col-sm-2 col-form-label">'.plus_get_string("lesson", "form").' *</label><div class="col-sm-10"><select name="subtopic[]" class="form-control" id="subtopic${selectedsubtopic.id}" additional_subtopics required="required">`;
        var hasmoresubtopic = false;
        $.each( selectedsubtopic.subtopics, function( key, subtopic ) {
          if(Array.isArray(subtopic.forgrades) && subtopic.forgrades.length > 0 && !subtopic.forgrades.includes(group.categoryid)){
            console.log("skipped subtopic - ", subtopic);
            return;
          }
          additionalallsubtopic[subtopic.id] = subtopic;
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
        $(`[for="${elementid}"]`).html("'.plus_get_string("component", "form").'");
        $("#allsubtopic").append(newelement);
        $(`#subtopic${selectedsubtopic.id}`).trigger("change");
      } else {
        $(`[for="${elementid}"]`).html("'.plus_get_string("lesson", "form").'");
      }
    });
    $(document).on("change", "[additional_subtopics]", function(){
      var subtopicid = $(this).val();
      var elementid = $(this).attr("id");
      var selectedsubtopic = additionalallsubtopic[subtopicid];
      var newoptions = \'\';
      var selectedtype = $("#type").val();
      if(selectedsubtopic && Array.isArray(selectedsubtopic.quizes)){
        $.each( selectedsubtopic.quizes, function( key, quiz ) {
          if(Array.isArray(quiz.forgrades) && quiz.forgrades.length > 0 && !quiz.forgrades.includes(group.categoryid)){
            console.log("skipped quiz - ", quiz);
            return;
          }
          if(selectedtype == 2 || (selectedtype ==1 && primarygrades.includes(selectedcourse.category) &&  selectedcourse.coursetype == 1)){
            if(quiz.istutoring != 1 ){return;}
          } else {
            if(quiz.istutoring == 1 ){return;}
          }
          var sel = ``;
          if(formdata && formdata.quiz == quiz.cm){
            sel = `Selected`;
          }
          newoptions += `<option ${sel} value="${quiz.cm}">${quiz.name}</option>`;
        });
      }
      $("#quiz").html(newoptions);
      $("#quiz").trigger("change");
      if(selectedsubtopic && Array.isArray(selectedsubtopic.subtopics) && selectedsubtopic.subtopics.length > 0){
        $(`[for="${elementid}"]`).html("'.plus_get_string("component", "form").'");
        var newelement = `<div class="form-group row additional_subtopicselements"><label for="subtopic${selectedsubtopic.id}" class="col-sm-2 col-form-label">'.plus_get_string("lesson", "form").' *</label><div class="col-sm-10"><select name="subtopic[]" class="form-control" id="subtopic${selectedsubtopic.id}" additional_subtopics required="required">`;
        var hasmoresubtopic = false;
        $.each( selectedsubtopic.subtopics, function( key, subtopic ) {
          if(Array.isArray(subtopic.forgrades) && subtopic.forgrades.length > 0 && !subtopic.forgrades.includes(group.categoryid)){
            console.log("skipped subtopic - ", subtopic);
            return;
          }

          additionalallsubtopic[subtopic.id] = subtopic;
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
        $(`[for="${elementid}"]`).html("'.plus_get_string("component", "form").'");
        $("#allsubtopic").append(newelement);
        $(`#subtopic${selectedsubtopic.id}`).trigger("change");
      } else {
        $(`[for="${elementid}"]`).html("'.plus_get_string("lesson", "form").'");
      }
    });
    $("#courseid").trigger("change");
  });
  $("#addmoregroup").click(function(){
    $("#additionaltask").append(adittionalhomeworkshtml);
      $(".customdatepicker").datetimepicker({dateFormat: "yy-mm-dd",
                 timeFormat: "hh:mm",
                 separator: "T"
     });
  });
  $("#additionaltask").on("click", ".removegroup", function (){
    $(this).closest(".row").remove();
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
        $(".only_lessons").hide();
        $(".only_lessons input[type=\"checkbox\"]").prop("checked", true);
        $(".is_imidiatefeedback").hide();
        $(".is_imidiatefeedback input[type=\"checkbox\"]").prop("checked", false);
        var selectedarea = $(`[name="filterarea"]`);
        if(selectedarea == 2){
          $(`[name="filterarea"]`).val(0);
        }
      } else {
        $(".is_imidiatefeedback").show();
        $(".only_lessons").show();
        $(".for_challenge").hide();
        $(".only_lessons input[type=\"checkbox\"]").prop("checked", false);
        $(".for_challenge input[type=\"checkbox\"]").prop("checked", false);
      }
  }
  
 	 $(".viewtask").hide();
  $("#quiz").change(function(){
	  var quizid = $("#quiz").val();
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
          displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("questionnotfound", "form").'", "info");
        }
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("failedtogetquestion", "form").'", "error");        
      }
  	});          
  });
  /*$("#homeworkdate").datetimepicker({dateFormat: "yy-mm-dd",
                                   timeFormat: "hh:mm",
                                   separator: "T"
                       });
             
  $("#duedate").datetimepicker({dateFormat: "yy-mm-dd",
                                   timeFormat: "hh:mm",
                                   separator: "T"
                       });*/
             
  
  </script>';

    echo $html;
  }