<?php
function plus_studentProfileFilter(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->categoryids = plus_get_request_parameter("categoryids", array());
  $formdata->groupids = plus_get_request_parameter("groupids", array());
  $formdata->groupid = plus_get_request_parameter("groupid",0);
  $formdata->topic = plus_get_request_parameter("topic", array());
  $formdata->student_id=plus_get_request_parameter("student_id", array());

  // echo "<pre>";
  // echo "<br> <br> <br> <br> <br> <br>";
  // print_r($formdata->categoryids);
  // $formdata->courseid = plus_get_request_parameter("courseid", 0);
  /*$formdata->name = plus_get_request_parameter("homeworkname", "");
  $formdata->mode = plus_get_request_parameter("mode", "");*/
  // $formdata->type = plus_get_request_parameter("type", 1);
  // $formdata->subtopic = plus_get_request_parameter("subtopic", "");
  // $formdata->quiz = plus_get_request_parameter("quiz", "");
  // $formdata->status = plus_get_request_parameter("status", 1);
  // $formdata->disablehints = plus_get_request_parameter("disablehints", 0);
  // $formdata->disableexplanation = plus_get_request_parameter("disableexplanation", 0);
  // $formdata->homeworkdate = plus_get_request_parameter("homeworkdate", date("Y-m-d\TH:i",time()));
  // $formdata->duedate = plus_get_request_parameter("duedate", date("Y-m-d\TH:i",time()));
  // $formdata->additional_quiz = plus_get_request_parameter("additional_quiz", array());
  // $formdata->additional_homeworkdate = plus_get_request_parameter("additional_homeworkdate", array());
  // $formdata->additional_duedate = plus_get_request_parameter("additional_duedate", array());
  $formdata->duedate = plus_get_request_parameter("duedate", date("Y-m-d\TH:i",time()));
  $formdata->homeworkdate = plus_get_request_parameter("homeworkdate", date("Y-m-d\TH:i",time()));
  if(empty($formdata->groupid)){
    // plus_redirect(home_url()."/groups/");
    // exit;
  }
  $APIRES = $MOODLE->get("GetGroupById", null, array("id"=>$formdata->groupid));
  $APIREScompetenciesdata = $MOODLE->get("getCompetencyFormData", null, array());
  // echo "<pre>";
  // print_r($APIREScompetenciesdata);
  $html='';
  $COURSE = null;
  // echo "<pre>";
  // print_r($APIRES);
  // echo "</pre>";
  // die;

  if(isset($_POST['savehomework'])){
   
    // plus_redirect(home_url().'/group-details/?id='.$formdata->groupid);
    // exit;
  }
  // if(!empty($formdata->id)){
  //   $APIRES1 = $MOODLE->get("GetHomeWorkById", null, array("id"=>$formdata->id));
  //   if($APIRES1->code == 200 and $APIRES1->data->id == $formdata->id){
  //     $formdata->categoryid = $APIRES1->data->categoryid;
  //     $formdata->courseid = $APIRES1->data->courseid;
  //     $formdata->groupid = $APIRES1->data->groupid;
  //     $formdata->name = $APIRES1->data->name;
  //     $formdata->topic = $APIRES1->data->topic;
  //     $formdata->mode = $APIRES1->data->mode;
  //     $formdata->type = $APIRES1->data->type;
  //     $formdata->subtopic = $APIRES1->data->subtopic;
  //     $formdata->quiz = $APIRES1->data->quiz;
  //     $formdata->disablehints = $APIRES1->data->disablehints;
  //     $formdata->disableexplanation = $APIRES1->data->disableexplanation;
  //     $formdata->status = $APIRES1->data->status;
  //     $formdata->homeworkdate = date("Y-m-d\TH:i", $APIRES1->data->homeworkdate);
  //     $formdata->duedate = date("Y-m-d\TH:i", $APIRES1->data->duedate);
  //   } 
  // }
  // if($APIRES->code == 200 and $APIRES->data->id == $formdata->groupid){
  //   $group = $APIRES->data;
  //   $formdata->categoryid = $group->categoryid;
  //   $formdata->courseid = $group->courseid;
  //   $APIREScourse = $MOODLE->get("GetCourseModeDetails", null, array("id"=>$group->courseid));
  //   if($APIREScourse->code == 200 and $APIREScourse->data->course->id == $group->courseid){
  //     $COURSE = $APIREScourse->data->course;
  //   }
  // } else {
  //  // plus_redirect(home_url()."/groups/");
   // exit;
 // }
 
  $allGrades=array();
  $selectedGroups=array();
  $selectedTopics=array();
  $selectedStudents=array();
  $selectedSubtopic=array();
  foreach($APIREScompetenciesdata->data as $competenciesdata){
    array_push($allGrades,$competenciesdata);   
  }
  // echo "<pre>";
  // print_r($allGrades);
   $allmode = array();
  $alltopics = array();
  $selectedmod = new stdClass();
  $selectedmod->topics = array();
  $selectedtopic = new stdClass();
  $selectedtopic->subtopics = array();
  $selectedsubtopic = new stdClass();
  $selectedsubtopic->quizes = array();
  $selectedquiz = null;
  $adittionalhomeworks='';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">'.plus_get_string("title", "studentprofilefilter").'</h4>
                  <form method="GET" class="forms-sample" autocomplete="off" action="/student-profile">
                    <div class="form-group row">
                      <label for="homeworkname" class="col-sm-2 col-form-label">'.plus_get_string("gradelevel", "studentprofilefilter").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="categoryids[]" id="categoryids" multiple>
                          ';
                          foreach($allGrades as $grades){
                            $selected='';
                            if(in_array($grades->categoryid, $formdata->categoryids)){
                              array_push($selectedGroups,$grades->groups);
                               $selected='selected';
                               //array_push($selectedGroups,)
                            }
                            $html .='<option value="'.$grades->categoryid.'" '.$selected.'> '.$grades->name.'</option>';
                          }
                          // echo "<pre>";
                          // print_r($selectedGroups);
                    $html .= '      </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="homeworkname" class="col-sm-2 col-form-label">'.plus_get_string("group", "studentprofilefilter").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="groupids[]" id="std_group" multiple>';

                      

                       $html .=' </select>
                      </div>
                    </div>
                   
                    <div class="form-group row">
                      <label for="homeworkname" class="col-sm-2 col-form-label">'.plus_get_string("unitsemester", "studentprofilefilter").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="semenster[]" id="std_semester" multiple>
                    
                        </select>
                      </div>
                    </div>
                      <div class="form-group row">
                      <label for="homeworkname" class="col-sm-2 col-form-label">'.plus_get_string("lession", "studentprofilefilter").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="lession[]" id="std_lession" multiple>
          
                        </select>
                      </div>
                    </div>
                     <div class="form-group row">
                      <label for="homeworkname" class="col-sm-2 col-form-label">'.plus_get_string("student", "studentprofilefilter").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="student[]" id="std_id" multiple>
                         
                        </select>
                      </div>
                    </div>
                     <div class="form-group row">
                      <label for="homeworkname" class="col-sm-2 col-form-label">'.plus_get_string("language", "studentprofilefilter").'</label>
                      <div class="col-sm-10">
                        <select class="form-control" name="lang" id="std_lang">
                          <option value="">'.plus_get_string("selectlanguage", "studentprofilefilter").'</option>
                          <option value="FR">FR</option>
                          <option value="AR">AR</option>
                          <option value="EN">EN</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="homeworkdate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("startdate", "studentprofilefilter").'</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="homeworkdate" class="form-control" id="homeworkdate" value="'.$formdata->homeworkdate.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="duedate" required="required" class="col-sm-2 col-form-label">'.plus_get_string("enddate", "studentprofilefilter").'</label>
                      <div class="col-sm-10">
                        <input type="datetime-local" required="required" name="duedate" class="form-control" id="duedate" value="'.$formdata->duedate.'">
                      </div>
                    </div>
                
                    <button type="submit" name="savehomework" class="btn btn-primary mr-2">'.plus_get_string("filter", "form").'</button>
                    <a href="'.(empty($formdata->groupid)?'/student-profile':'/group-details/?id='.$formdata->groupid).'" class="btn btn-warning">'.plus_get_string("return", "form").'</a>
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
  $(document).ready(function(){
    
    var allmode = '.json_encode($allmode).';
    var alltopics = '.json_encode($selectedmod->topics).';
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
	  $(".viewtask").show();
     
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
                       });*/
             
  /*$("#duedate").datetimepicker({dateFormat: "yy-mm-dd",
                                   timeFormat: "hh:mm",
                                   separator: "T"
                       });*/
             
  $(function(){
    var allGrades='.json_encode($allGrades).';
    var allGroups='.json_encode($selectedGroups).';
    var allTopic='.json_encode($selectedTopics).';
    var allMember='.json_encode($selectedStudents).';
    var allSubtopic='.json_encode($selectedSubtopic).';
    console.log("allGrades--- ",allGrades);
      $("body").on("change","#categoryids",function(){
        var all_seleted_group=[];
        var seletect_groups=[];
        var groupoption="";
        var grades=getSelectedCategories().map(function(category) {
        return parseInt(category); });

        console.log("Grades-- ",grades);
        grades.forEach(function(item){
          console.log("grade---     ",item);
            var grade=allGrades?.find(function(gradeitem){
                  return parseInt(gradeitem.categoryid)==item;
            });
            all_seleted_group.push(grade);
          
          });
          console.log("all_seleted_group-- ",all_seleted_group);
          all_seleted_group?.forEach(function(groupsItem){
            groupsItem.groups?.forEach(function(group){
              seletect_groups.push(group);
              groupoption +=\'<option value="\'+group.groupid+\'">\'+group.name+\'</option>\';
                console.log("group-- ",group);
              });
              $("#std_group").html(groupoption);
              console.log("groupsssss--- ",groupsItem);
            });

            allGroups=seletect_groups;
            $("#std_group").trigger("change");
            console.log("allGroups------  ",allGroups);
        });

        $("body").on("change","#std_group",function(){
          var seletect_topic=[];
          var seletect_member=[];
          var select_sub_topic=[]
          var member_option="";
          var topic_option="";
          console.log("dfgfdfege  ",allGroups);
          var groupsarr=getSelectedGroup()?.map(function(group) {
        return parseInt(group); });
        groupsarr?.forEach(function(groupid){
            var seletedgroupdata=allGroups?.find(element => parseInt(element.groupid)==groupid);
            if(seletedgroupdata.topics){
              seletect_topic.push(seletedgroupdata.topics);
              seletect_member.push(seletedgroupdata.group_member);
            }
            
          });
          console.log("seletect_topic---- ",seletect_topic);
          console.log("seletect_member---- ",seletect_member);

          seletect_member?.forEach(function(members){
            console.log("members",members);
            members?.forEach(function(member){

              member_option +=\'<option value="\'+member.userid+\'">\'+member.firstname+\' \'+member.lastname+\'</option>\';
              });
          });
           seletect_topic?.forEach(function(topics){
            topics?.forEach(function(topic){
              select_sub_topic.push(topic);
              console.log("----------tttt---------",topic);
                var topic_name=topic.name?topic.name:\'General\';
              topic_option +=\'<option value="\'+topic.id+\'">\'+topic_name+\'</option>\';
              });
             // allTopic=
          });
          $("#std_id").html(member_option);
          $("#std_semester").html(topic_option);
          allTopic=select_sub_topic;
          console.log("dddddddddddddddddddd  all topic  ",allTopic);
           $("#std_semester").trigger("change");
        // allGroups?.forEach(function(groupitem){

        //     console.log("allGroupsallGroupsallGroups-- ",groupitem);

        //   });
          console.log("--------Groups------",groupsarr);
        });

        $("body").on("change","#std_semester",function(){
          var subtopic_option="";
            var topicsArr=getSelectedTopic()?.map(function(topic) {
        return parseInt(topic); });
        var subtopicArr=[];
        topicsArr?.forEach(function(topicid){
            var topicdata=allTopic?.find(element => parseInt(element.id)==topicid);
            if(topicdata.subtopics){

              subtopicArr.push(topicdata.subtopics);
            }
          });
        allTopic?.forEach(function(topics){
          console.log("ttttooopicks",topics);
          });
          console.log("subtopicArrsubtopicArr",subtopicArr);
          subtopicArr?.forEach(function(subtopics){
              subtopics?.forEach(function(subtopic){
                var sub_topic_name=subtopic.name?subtopic.name:\'General\';
                subtopic_option +=\'<option value="\'+subtopic.id+\'">\'+sub_topic_name+\'</option>\';
                console.log("rrrrrrrrrrrrrrr subs",subtopic);
              });
            });
            $("#std_lession").html(subtopic_option);
          console.log("topicsArrtopicsArrtopicsArr",topicsArr);
          });
    });
    function getSelectedCategories(){
      return $("#categoryids").val();
    }
    function getSelectedGroup(){
      return $("#std_group").val();
    }
    function getSelectedTopic(){
      return $("#std_semester").val();
    }

  </script>';

    return $html;
  }