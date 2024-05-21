<?php
function plus_prepare_edit_question($question=null){
  $html='';
  $html .=  '<div class="form-group row question '.((!isset($question->questiontype) || $question->questiontype!='shortanswer')?'haveoptions':'').'">
    <div class="col-sm-12 formelement">
      <div class="row">
        <label class="col-9 questioncounter"></label>
        <label class="col-3 text-right"><button type="button" removesurveyquestion data-id="'.(($question && $question->id)?$question->id:0).'"><i class="mdi mdi-close"></i></button></label>
      </div>
    </div>
    <div class="col-sm-12 formelement">
      <input type="text" required data-name="questiontext" value="'.($question && $question->questiontext?:'').'"  class="form-control forminputelement"  placeholder="Question Text"/>
      <input type="hidden"  class="form-control forminputelement" data-name="id" value="'.(($question && $question->id)?$question->id:0).'" />
    </div>
    <div class="col-sm-12 formelement">
      <textarea class="form-control forminputelement turneditor" data-name="questiondescription" name="questiondescription" placeholder="Question Description">'.($question && $question->questiondescription?:'').'</textarea>
    </div>
    <div class="col-sm-12 formelement">
      <select data-name="lang"  class="form-control forminputelement">
        <option '.(($question && $question->lang && $question->lang=='ar')?'selected':'').' value="ar">'.plus_get_string("arabic", "form").'</option>
        <option '.(($question && $question->lang && $question->lang=='fr')?'selected':'').' value="fr">'.plus_get_string("french", "form").'</option>
        <option '.(($question && $question->lang && $question->lang=='en')?'selected':'').' value="en">'.plus_get_string("english", "form").'</option>
      </select>
    </div>
    <div class="col-sm-12 formelement">
      <label> <input type="checkbox" value="1" data-name="required" class="forminputelement" '.(($question && $question->required == 1)?'checked':'').' > &nbsp; Required</label><br>
    </div>

    <div class="col-sm-12 formelement">
      <select questiontype data-name="questiontype" class="form-control forminputelement">
        <option '.(($question && $question->questiontype && $question->questiontype=='shortanswer')?'selected':'').' value="shortanswer">'.plus_get_string("shortanswer", "form").'</option>
        <option '.(($question && $question->questiontype && $question->questiontype=='truefalse')?'selected':'').' value="truefalse">'.plus_get_string("true", "form").'/'.plus_get_string("false", "form").'</option>
        <option '.(($question && $question->questiontype && $question->questiontype=='singlechoice')?'selected':'').' value="singlechoice">'.plus_get_string("singlechoice", "form").'</option>
        <option '.(($question && $question->questiontype && $question->questiontype=='multichoice')?'selected':'').' value="multichoice">'.plus_get_string("multichoice", "form").'</option>
      </select>
    </div>
    <div class="col-sm-12 formelement" options>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label" style="justify-content: space-between; display: flex; flex-direction: column; margin-bottom: 0px;">
        '.plus_get_string("options", "form").'<br/>
        <button type="button" addquestionoption><i class="mdi mdi-plus"></i></button>
        </label>
        <div class="col-sm-10 optionslist">';
  if($question && !empty($question->options)){
    foreach ($question->options as $option) {
      $html .= plus_prepare_edit_question_option($option);
    }
  } else {
    $option = null;
    $html .= plus_prepare_edit_question_option($option);
  }
  $html .=  '
        </div>
      </div>
    </div>
  </div>';
  return $html;
}
function plus_prepare_edit_question_option($option=null){
  $html ='';
  $html .= '<div class="row optionitem '.(($option && !empty($option->commentquestion))?'havecustomcomment':'').'">
    <div class="col-10 " >
      <input type="hidden"  class="form-control optionelement" data-name="id" value="'.(($option && $option->id)?$option->id:0).'" />
      <input type="text"  required class="form-control optionelement" data-name="choice" placeholder="Option Text" value="'.(($option && $option->option)?$option->option:'').'" />
      <label class="col-form-label"><input class="optionelement" data-name="havecomment" type="checkbox" value="1" '.(($option && $option->havecomment)?'checked':'').' /> '.plus_get_string("havecomment", "form").'</label>
      <label class="col-form-label"><input class="optionelement" type="checkbox" data-name="havecommentheading" havecustomcommentheading value="1" '.(($option && !empty($option->commentquestion))?'checked':'').' /> '.plus_get_string("customcommenttext", "form").'</label>
      <input type="text"  data-name="commentheading" commentheader class="form-control optionelement" placeholder="" value="'.(($option && $option->commentquestion)?$option->commentquestion:'').'"/>
    </div>
    <label class="col-2 text-right"><button type="button" removequestionoption  data-id="'.(($option && $option->id)?$option->id:0).'"><i class="mdi mdi-close"></i></button></label>
  </div>';
  return $html;
}
function plus_alert($message, $type="info", $url=""){
  return '<div class="alert alert-'.$type.'"><h3>'.$message.'</h3>'.($url?'<a href="'.$url.' class="btn btn-primary">Continue</a>':'').'</div>';
}
function plus_prepare_question($question=null){
  $html='';
  $html .=  '
  <div class="form-group question '.((!isset($question->questiontype) || $question->questiontype != 'shortanswer')?'haveoptions':'').'">
	<div class="questiontext">'.($question->questiontext?:'').'</div>
	<div class="questiondescription">'.($question->questiondescription?:'').'</div>
	<div class="form-group">
    ';
    if($question->questiontype == 'shortanswer'){
		$html .= '<div class="form-group">
                    <textarea class="form-control form-control-lg"name="answer['.$question->id.']"  placeholder="Answer"></textarea>
                  </div>';
    } else if($question && !empty($question->options)){
		foreach ($question->options as $option) {
		  $html .= plus_prepare_question_option($question, $option);
		}
	}
    $html .=  '
    </div>
  </div>';
  return $html;
}
function plus_prepare_question_option($question, $option, $response=null){
  $html ='';
  $html .= '
	<div class="mb-4">
    <div class="form-check">
      <label class="form-check-label text-muted">
        <input type="'.($question->questiontype == 'multichoice'?'checkbox':'radio').'" name="answer['.$option->questionid.']'.($question->questiontype == 'multichoice'?'[]':'').'" value="'.$option->id.'" class="form-check-input"> '.$option->option.'</label>
    </div>
  </div>';
  return $html;
}
