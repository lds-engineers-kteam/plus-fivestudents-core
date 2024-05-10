<?php
function plus_view_viewsurvey(){
  global $wp;
  if ( !is_user_logged_in() || !current_user_can('plus_cansubmitsurvey')) {
    return plus_view_noaccess();
  }
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", "");
  $formdata->returnto = plus_get_request_parameter("returnto", "");
  $formdata->answer = plus_get_request_parameter("answer", array());
  $html='';
  $APIRES = $MOODLE->get("getSurveyByID", null, $formdata);
  if(isset($APIRES->data)){
    if(isset($APIRES->data->survey)){
      $survey = $APIRES->data->survey;
      $formdata->surveyid = $survey->id;
      $formdata->name = $survey->name;
      $formdata->description = $survey->description;
    }
    if(isset($APIRES->data->questions)){
      $formdata->question = $APIRES->data->questions;
    }
  }
  if($formdata->surveyid != $formdata->id){
    return plus_alert("Invalid Survey", "/");
  }
  if($_POST['saveanswer']){
    $SAVEAPIRES = $MOODLE->get("saveSurveyResponse", null, $formdata);
    plus_redirect(home_url()."/surveys");
    exit;
  }
  // $html .='<pre>'.print_r($formdata, true).'</pre>';
  $questions = '';
  if(is_array($formdata->question) && sizeof($formdata->question) > 0){
      $questions .= '<form class="forms-sample" method="post">';
    foreach ($formdata->question as $key => $question) {
      $questions .= plus_prepare_question($question);
    }
    $questions .= '
      <input type="hidden" name="id" value="'.$formdata->id.'"/>
      <button type="submit" name="saveanswer" value="saveanswer" class="btn btn-primary mr-2">Save</button></form>
    ';
  } else {
    $questions = plus_alert("Question Not found");
  }
  $html .=  '
    <div class="row">
      <div class="col-md-12 grid-margin transparent">
        <div class="row">';
  $html .=  '
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card answersurvey">
              <div class="card-body">
                <h4 class="card-title text-center">'.$survey->name.'</h4>
                <div class="card-desc">'.$survey->description.'</div>
              </div>
              <div class="card-body">'.$questions.'</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    ';

  return $html;
}



     

