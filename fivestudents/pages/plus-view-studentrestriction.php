<?php
function plus_view_studentrestriction(){
 global $wp,$CFG;
 require_once($CFG->dirroot . '/api/moodlecall.php');

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
  $formdata->subtopic = plus_get_request_parameter("subtopic", 0);
  $formdata->quiz = plus_get_request_parameter("quiz", 0);
  $formdata->status = plus_get_request_parameter("status", 1);
  $formdata->disablehints = plus_get_request_parameter("disablehints", 0);
  $formdata->disableexplanation = plus_get_request_parameter("disableexplanation", 0);
  $formdata->homeworkdate = plus_get_request_parameter("homeworkdate", "");
  $formdata->duedate = plus_get_request_parameter("duedate", "");
  $formdata->additional_quiz = plus_get_request_parameter("additional_quiz", array());
  $formdata->additional_homeworkdate = plus_get_request_parameter("additional_homeworkdate", array());
  $formdata->additional_duedate = plus_get_request_parameter("additional_duedate", array());
  $formdata->groups=plus_get_request_parameter("groups", array());
  if(empty($formdata->groupid)){
    plus_redirect(home_url()."/groups/");
    exit;
  }
  $APIRES = $MOODLE->get("GetGroupById", null, array("id"=>$formdata->groupid));
  $APIRESgetModulesRetriction = $MOODLE->get("getModulesRestriction", null, array());
  /*echo "<pre>";
  print_r($APIRESgetModulesRetriction);*/
  $html='';
  $COURSE = null;

 if($APIRES->code == 200 and $APIRES->data->id == $formdata->groupid){
    $group = $APIRES->data;
    $formdata->categoryid = $group->categoryid;
    $formdata->courseid = $group->courseid;
    $APIREScourse = $MOODLE->get("GetCourseModeDetails", null, array("id"=>$group->courseid));
   //  echo "<pre>";
   // print_r($APIREScourse);
    if($APIREScourse->code == 200 and $APIREScourse->data->course->id == $group->courseid){
      $COURSE = $APIREScourse->data->course;
      $GROUPS = $APIREScourse->data->groups;
      /*echo "<pre>";
      print_r($COURSE);*/
    }
  } else {
    plus_redirect(home_url()."/groups/");
    exit;
  }
  if(isset($_POST['savehomework'])){

    $APIRESmodulesrestriction = $MOODLE->get("addModulesRestrictions", null, $formdata);
    plus_redirect(home_url().'/student-restriction/?groupid='.$formdata->groupid);
    exit;
  }
  if(!empty($formdata->id)){
    $APIRES1 = $MOODLE->get("getModulesRestrictionById", null, array("id"=>$formdata->id));
    // echo "<pre>";
    // print_r($APIRES1);
    if($APIRES1->code == 200 and $APIRES1->data->id == $formdata->id){

      $formdata->categoryid = 0;//$APIRES1->data->categoryid;
      $formdata->courseid =0;// $APIRES1->data->courseid;
      // $formdata->groupid = $APIRES1->data->groupid;
      $formdata->name = '';//$APIRES1->data->name;
      $formdata->topic = $APIRES1->data->topic;
      $formdata->mode = "";//$APIRES1->data->mode;
      $formdata->type =1;// $APIRES1->data->type;
      $formdata->subtopic = $APIRES1->data->subtopic;
      $formdata->quiz = $APIRES1->data->quiz;
      $formdata->disablehints = 0;//$APIRES1->data->disablehints;
      $formdata->disableexplanation = 0;//$APIRES1->data->disableexplanation;
      $formdata->status = $APIRES1->data->status;
      $formdata->homeworkdate =null;// array();//date("Y-m-d", $APIRES1->data->homeworkdate)."T".date("H:i", $APIRES1->data->homeworkdate);
      $formdata->duedate =null;array();// date("Y-m-d", $APIRES1->data->duedate)."T".date("H:i", $APIRES1->data->duedate);
      $formdata->groups=array($formdata->groupid);
    } 
  }
 
  $allmode = array();
  $alltopics = array();
  $selectedmod = new stdClass();
  $selectedmod->topics = array();
  $selectedtopic = new stdClass();
  $selectedtopic->subtopics = array();
  $selectedsubtopic = new stdClass();
  $selectedsubtopic->quizes = array();
  $selectedquiz = null;
  $topicquiz = '';
  /*$topicquiz .= '<div class="form-group row">
                  <label for="type" class="col-sm-2 col-form-label">Type</label>
                  <div class="col-sm-10"><select name="type" class="form-control" id="type" required="required">';
  $topicquiz .= '<option '.(($formdata->type == 0)?'selected':'').' value="0">'.plus_get_string("homework", "site").'</option>';
  $topicquiz .= '<option '.(($formdata->type == 1)?'selected':'').' value="1">'.plus_get_string("assessment", "form").'</option>';
  $topicquiz .= ' </select></div>
                </div>';
  $topicquiz .= '<div class="form-group row">
                  <label for="mode" class="col-sm-2 col-form-label">Mode</label>
                  <div class="col-sm-10"><select name="mode" class="form-control" id="mode" required="required"><option value="">'.plus_get_string("select", "form").' Mode</option>';
  if($COURSE && sizeof($COURSE->mode)>0){
    foreach ($COURSE->mode as $mode) {
      array_push($allmode, $mode);
      $sel = "";
      if($mode->type == $formdata->mode){
        $sel = "selected";
        $selectedmod = $mode;
      }
      $topicquiz .= '<option '.$sel.' value="'.$mode->type.'">'.plus_get_string("mode_".$mode->type, "form").'</option>';
    }
  }
  $topicquiz .= ' </select></div>
                </div>';*/
  $topicquiz .= '<div class="form-group row">
                  <label for="topic" class="col-sm-2 col-form-label">'.plus_get_string("semester", "form").' *</label>
                  <div class="col-sm-10"><select name="topic" class="form-control" id="topic" required="required"><option value="">'.plus_get_string("select", "form").' '.plus_get_string("semester", "form").'</option>';
  if( sizeof($COURSE->mode)>0){
    foreach($COURSE->mode as $mode){
     foreach ($mode->topics as $topic) {
      $sel = "";
      array_push($alltopics,$topic);
      if($topic->id == $formdata->topic){
        $sel = "selected";
        $selectedtopic = $topic;
      }
      $topicquiz .= '<option '.$sel.' value="'.$topic->id.'">'.$topic->name.'</option>';
    }
}
   
  }
  $topicquiz .= ' </select></div>
                </div>';
  $topicquiz .= '<div class="form-group row">
                  <label for="subtopic" class="col-sm-2 col-form-label">'.plus_get_string("lesson", "form").' </label>
                  <div class="col-sm-10"><select name="subtopic" class="form-control" id="subtopic" ><option value="">'.plus_get_string("select", "form").' '.plus_get_string("lesson", "form").'</option>';
  if(!empty($selectedtopic) && sizeof($selectedtopic->subtopics)>0){
    foreach ($selectedtopic->subtopics as $subtopic) {
      $sel = "";
      if($subtopic->id == $formdata->subtopic){
        $sel = "selected";
        $selectedsubtopic = $subtopic;
      }
      $topicquiz .= '<option '.$sel.' value="'.$subtopic->id.'">'.$subtopic->name.'</option>';
    }
  }
  $topicquiz .= '</select></div>
                </div>';
  $topicquiz .= '<div class="form-group row">
                  <label for="quiz" class="col-sm-2 col-form-label">'.plus_get_string("quiz", "form").' 
          <button type="button" class="btn btn-lg viewtask" data-id="">'.plus_get_string("view", "form").'</button>
          </label>
                  <div class="col-sm-10"><select name="quiz" class="form-control" id="quiz"><option value="">'.plus_get_string("select", "form").' '.plus_get_string("quiz", "form").'</option>';
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
                            <input type="text" required="required" name="additional_homeworkdate[]" class="form-control customdatepicker">
                          </div>
                          <div class="form-group col-sm-3">
                            <label for="exampleInputUsername1">'.plus_get_string("duedate", "form").' *</label>
                            <input type="text" required="required" name="additional_duedate[]" class="form-control customdatepicker">
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
$adittionalhomeworks='';
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
                  <h4 class="card-title">User Restriction</h4>
                  <form method="post" class="forms-sample" autocomplete="off">
                    <div class="form-group row">
                      <label for="groups" class="col-sm-2 col-form-label">Select Group *</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="groups[]" multiple  required="required">';
                        foreach($GROUPS as $group){
                          $sel='';
                          if(count($formdata->groups)){
                            if(in_array($group->id, $formdata->groups)){
                              $sel='selected';
                            }
                          }
                          $html .= '<option value="'.$group->id.'" '.$sel.'>'.$group->name.'</option>';
                        }

  $html .= '          </select>
                      </div>
                    </div>'.$topicquiz.'
                    <div class="form-group row for_lessons" style="display:none;">
                      <label for="disablehints" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disablehints == 1?' checked="checked" ':'').' id="disablehints" name="disablehints" /></label>
                      <div class="col-sm-10">
                        <label for="disablehints" class="col-form-label">'.plus_get_string("disablehints", "form").'</label>
                        
                      </div>
                    </div>
                    <div class="form-group row for_lessons" style="display:none;">
                      <label for="disableexplanation" class="col-sm-2 col-form-label text-right"><input type="checkbox" value="1" '.($formdata->disableexplanation == 1?' checked="checked" ':'').' id="disableexplanation" name="disableexplanation" /></label>
                      <div class="col-sm-10">
                        <label for="disableexplanation" class="col-form-label">'.plus_get_string("disableexplanation", "form").'</label>
                        
                      </div>
                    </div>
                   <!-- <div class="form-group row">
                      <label for="homeworkdate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("publishdate", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="homeworkdate" class="form-control" id="homeworkdate" value="'.$formdata->homeworkdate.'">
                      </div>
                    </div>-->
                   <!-- <div class="form-group row">
                      <label for="duedate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("duedate", "form").' *</label>
                      <div class="col-sm-10">
                        <input type="text" required="required" name="duedate" class="form-control" id="duedate" value="'.$formdata->duedate.'">
                      </div>
                    </div>-->
                    <div class="form-group row">
                      <label for="status" class="col-sm-2 col-form-label">'.plus_get_string("status", "form").'</label>
                      <div class="col-sm-10">
                        <select name="status" class="form-control" id="status" >
                          <option '.($formdata->status==1?'selected':'').' value="1">Active</option>
                          <option '.($formdata->status!=1?'selected':'').' value="0">Inactive</option>
                        </select>
                      </div>
                    </div>'.$adittionalhomeworks.'
                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <input type="hidden" name="categoryid" value="'.$formdata->categoryid.'"/>
                    <input type="hidden" name="courseid" value="'.$formdata->courseid.'"/>
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
  $html .= '<div class="row">
            <table class="table" id="user-restriction">
                <thead>
                  <tr>
                    <th>S.no</th>
                    <th>Group Name</th>
                    <th>Topic Name</th>
                    <th>Subtopic Name</th>
                    <th>Quiz name</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>';
                if(count($APIRESgetModulesRetriction->data)>0){
                  $i=1;
                  foreach($APIRESgetModulesRetriction->data as $data){
                    $status=($data->status ? "Active" : "Inactive");
                    $html .='  <tr>
                      <td>'.$i++.'</td>
                      <td>'.$data->ipname.'</td>
                      <td>'.$data->topic_name.'</td>
                      <td>'.$data->subtopic_name.'</td>
                      <td>'.$data->name.'</td>
                      <td> '.$status.'</td>
                      <td>
                      <a href="./?groupid='.$data->groupid.'&id='.$data->id.'" >Edit</a><br>
                    </td>
                    </tr>';
                  }
                }
                  
              $html .=   ' </tbody>
            </table>
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
    var allmode = '.json_encode($allmode).';
    var alltopics = '.json_encode($alltopics).';
    var allsubtopic = '.json_encode($selectedtopic->subtopics).';
    $("#mode").change(function(){
      var mode = $(this).val();
      console.log("mode- ", mode)
      var selectedmode = allmode.find(x => x.type === mode);
      console.log("selectedmode- ", selectedmode)
      var newoptions = \'<option value="">'.plus_get_string("select", "form").' '.plus_get_string("semester", "form").'</option>\';
      if(selectedmode && Array.isArray(selectedmode.topics)){
        alltopics = selectedmode.topics;
        $.each( selectedmode.topics, function( key, topic ) {
          newoptions += \'<option value="\'+topic.id+\'">\'+topic.name+\'</option>\'
        });
      } else {
        alltopics = [];

      }
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
      var newoptions = \'<option value="">'.plus_get_string("select", "form").' '.plus_get_string("lesson", "form").'</option>\';
      if(selectedtopic && Array.isArray(selectedtopic.subtopics)){
        allsubtopic = selectedtopic.subtopics;
        $.each( selectedtopic.subtopics, function( key, topic ) {
          newoptions += \'<option value="\'+topic.id+\'">\'+topic.name+\'</option>\'
        });
      } else {
        allsubtopic = [];

      }
      console.log("newoptions- ", newoptions)
      $("#subtopic").html(newoptions);
      $("#subtopic").trigger("change");
    });
    $("#subtopic").change(function(){
      var subtopicid = $(this).val();
      console.log("subtopicid- ", subtopicid)
      var selectedsubtopic = allsubtopic.find(x => x.id === subtopicid);
      console.log("selectedsubtopic- ", selectedsubtopic)
      var newoptions = \'<option value="">'.plus_get_string("select", "form").' '.plus_get_string("quiz", "form").'</option>\';
      if(selectedsubtopic && Array.isArray(selectedsubtopic.quizes)){
        $.each( selectedsubtopic.quizes, function( key, quiz ) {
          newoptions += \'<option value="\'+quiz.cm+\'">\'+quiz.name+\'</option>\'
        });
      }
      $("#quiz").html(newoptions);
    });
    $("#mode").change(function(){
      modechanged();
    });
    modechanged();
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
  function modechanged(){
      var selectedmode1 = $("#mode").val();
      if(selectedmode1 == "quest"){
        $(".for_lessons").show();
      } else {
        $(".for_lessons").hide();
        $(".for_lessons input[type=\"checkbox\"]").prop("checked", false);
      }
  }
  
   $(".viewtask").hide();
  $("#quiz").change(function(){
    var quizid = $("#quiz").val();
    //$(".viewtask").show();
     
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
  $("#homeworkdate").datetimepicker({dateFormat: "yy-mm-dd",
                                   timeFormat: "hh:mm",
                                   separator: "T"
                       });
             
  $("#duedate").datetimepicker({dateFormat: "yy-mm-dd",
                                   timeFormat: "hh:mm",
                                   separator: "T"
                       });
             
  $(function(){
      $("#user-restriction").on("click",".change-status",function(){
          var id=$(this).attr("data-id");
          alert(id);
        });
    });
  </script>';

    echo $html;
  }