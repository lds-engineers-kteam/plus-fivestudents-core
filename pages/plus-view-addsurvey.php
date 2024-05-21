<?php
function plus_view_addsurvey(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->name = plus_get_request_parameter("surveyname", "");
  $formdata->description = plus_get_request_parameter("description", "");
  $formdata->question = plus_get_request_parameter("question", array());
  $formdata->deletedquestions = plus_get_request_parameter("deletedquestions", "");
  $formdata->deletedoptions = plus_get_request_parameter("deletedoptions", "");

  if(isset($_POST['savesurvey'])){ 
    $saveAPI = $MOODLE->get("SaveSurvey", null, $formdata);
    plus_redirect(home_url()."/surveys");
    exit;
  }
  $APIRES = $MOODLE->get("getSurveyByID", null, $formdata);
  if(isset($APIRES->data)){
    if(isset($APIRES->data->survey)){
      $survey = $APIRES->data->survey;
      $formdata->id = $survey->id;
      $formdata->name = $survey->name;
      $formdata->description = $survey->description;
    }
    if(isset($APIRES->data->questions)){
      $formdata->question = $APIRES->data->questions;
    }
  }
  $html ='';
  // $html .='<pre>'.print_r($APIRES, true).'</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("editsurvey", "form").'</h4>
                  <form class="forms-sample" method="post">
                    <div class="form-group row">
                      <label for="surveyname" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" required name="surveyname" class="form-control" id="name" placeholder="'.plus_get_string("name", "form").'" value="'.$formdata->name.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="name" class="col-sm-2 col-form-label">'.plus_get_string("description", "form").'</label>
                      <div class="col-sm-10">
                        <textarea id="description" name="description" rows="25" cols="80" class="form-control turneditor">'.$formdata->description.'</textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="name" class="col-sm-2 col-form-label" style="justify-content: space-between; display: flex; flex-direction: column;">
                      '.plus_get_string("questions", "form").'<br/>
                      <button type="button" addsurveyquestion><i class="mdi mdi-plus"></i></button>
                      </label>
                      <div class="col-sm-10">
                        <div class="questions" surveyquestion>';
            if(is_array($formdata->question) && sizeof($formdata->question) > 0){
              foreach ($formdata->question as $question) {
                $html .=  plus_prepare_edit_question($question);
              }
            } else {
              $html .=  plus_prepare_edit_question();
            }
            $html .=  ' </div>
                      </div>
                    </div>
                    <input type="hidden" name="id" value="'.$formdata->id.'"/>
                    <input type="hidden" name="deletedquestions" id="deletedquestions" value=""/>
                    <input type="hidden" name="deletedoptions" id="deletedoptions" value=""/>
                    <button type="submit" name="savesurvey" class="btn btn-primary mr-2">'.plus_get_string("save", "form").'</button>
                    <a href="'.$CFG->wwwroot.'/surveys" class="btn btn-light">'.plus_get_string("cancel", "form").'</a>
                  </form>
                </div>
              </div>
            </div>';
  $html .=  '</div>
            </div>
          </div>';

  $html .='
  <script>
    tinymce.init({ selector:".turneditor" });
    var blankquestion = `'.plus_prepare_edit_question().'`;
    var blankquestionoption = `'.plus_prepare_edit_question_option().'`;
    $(document).on("click", "[addsurveyquestion]", function(){
      $("[surveyquestion]").append(blankquestion);
      tinymce.init({ selector:".turneditor" });
      $(".turneditor").removeClass("turneditor");
      updateQuestionids();
    });
    $(document).on("click", "[removesurveyquestion]", function(){
      var questionid = $(this).data("id");
      var oldids = $("#deletedquestions").val();
      oldids = oldids.split(",");
      oldids.push(questionid);
      $("#deletedquestions").val(oldids.join(","));
      $(this).closest(".question").remove();
      updateQuestionids();
    });
    $(document).on("change", "[questiontype]", function(){
      var qtypeval = $(this).val();
      if(qtypeval != "shortanswer"){
        $(this).closest(".question").addClass("haveoptions");
      } else {
        $(this).closest(".question").removeClass("haveoptions");
      }
    });
    $(document).on("click", "[addquestionoption]", function(){
      $(this).closest("[options]").find(".optionslist").append(blankquestionoption);
      updateQuestionids();
    });
    $(document).on("click", "[removequestionoption]", function(){
      var optionid = $(this).data("id");
      var oldids = $("#deletedoptions").val();
      oldids = oldids.split(",");
      oldids.push(optionid);
      $("#deletedoptions").val(oldids.join(","));
      $(this).closest(".optionitem").remove();
      updateQuestionids();
    });
    $(document).on("change", "[havecustomcommentheading]", function(){
      if($(this).is(":checked")){
        $(this).closest(".optionitem").addClass("havecustomcomment");
      } else {
        $(this).closest(".optionitem").removeClass("havecustomcomment");
      }
    });

    function updateQuestionids(){
      var counter = 0;
      $(document).find(".question").each(function(item){
        counter++;
        var that = this;
        $(that).data("counter", counter);
        console.log(`element counter `, counter);
        $(that).find(".questioncounter").html(`Question ${counter}`);
        $(that).find(".optionitem").each(function(index){
          console.log(`counter: ${counter}, index: `, index);
          $(this).data("counter", index+1);

        });
      });
      $(document).find(".forminputelement").each(function(forminputelement){
        var name = $(this).data("name");
        var parentc = $(this).closest(".question").data("counter");
        $(this).attr("name", `question[${parentc}][${name}]`);
        console.log(`element counter ${parentc}: name: `, name);
      });
      $(document).find(".optionelement").each(function(index){
        var name = $(this).data("name");
        var questioncounter = $(this).closest(".question").data("counter");
        var optioncounter = $(this).closest(".optionitem").data("counter");
        $(this).attr("name", `question[${questioncounter}][option][${optioncounter}][${name}]`);
        console.log(`questioncounter: ${questioncounter}: optioncounter: ${optioncounter} index: `, index);
      });
    }
    $(document).ready(function(){
      updateQuestionids();

    });
  </script>';
  return $html;
}



     

