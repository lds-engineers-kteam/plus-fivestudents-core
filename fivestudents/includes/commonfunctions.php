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
      <input type="text" required data-name="questiontext" value="'.($question->questiontext?:'').'"  class="form-control forminputelement"  placeholder="Question Text"/>
      <input type="hidden"  class="form-control forminputelement" data-name="id" value="'.(($question && $question->id)?$question->id:0).'" />
    </div>
    <div class="col-sm-12 formelement">
      <textarea class="form-control forminputelement turneditor" data-name="questiondescription" name="questiondescription" placeholder="Question Description">'.($question->questiondescription?:'').'</textarea>
    </div>
    <div class="col-sm-12 formelement">
      <select data-name="lang"  class="form-control forminputelement">
        <option '.(($question->lang && $question->lang=='ar')?'selected':'').' value="ar">'.plus_get_string("arabic", "form").'</option>
        <option '.(($question->lang && $question->lang=='fr')?'selected':'').' value="fr">'.plus_get_string("french", "form").'</option>
        <option '.(($question->lang && $question->lang=='en')?'selected':'').' value="en">'.plus_get_string("english", "form").'</option>
      </select>
    </div>
    <div class="col-sm-12 formelement">
      <label> <input type="checkbox" value="1" data-name="required" class="forminputelement" '.(($question->required == 1)?'checked':'').' > &nbsp; Required</label><br>
    </div>

    <div class="col-sm-12 formelement">
      <select questiontype data-name="questiontype" class="form-control forminputelement">
        <option '.(($question->questiontype && $question->questiontype=='shortanswer')?'selected':'').' value="shortanswer">'.plus_get_string("shortanswer", "form").'</option>
        <option '.(($question->questiontype && $question->questiontype=='truefalse')?'selected':'').' value="truefalse">'.plus_get_string("true", "form").'/'.plus_get_string("false", "form").'</option>
        <option '.(($question->questiontype && $question->questiontype=='singlechoice')?'selected':'').' value="singlechoice">'.plus_get_string("singlechoice", "form").'</option>
        <option '.(($question->questiontype && $question->questiontype=='multichoice')?'selected':'').' value="multichoice">'.plus_get_string("multichoice", "form").'</option>
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
function classprofilereport_tr($topics, $addtitle = true){
  $html = '';
  $nextrowhtml = '';
  $allsubtopics = array();
  foreach ($topics as $key => $topic) {
    $html .= '<td colspan="'.$topic->childcount.'"><p class="text-center font-weight-bold mb-0" '.($topic->lang == 'ar'?'style="direction:rtl;"':'').' >'.($topic->name?$topic->name:"SubTopic ".$topic->section).'</p></td>';
    foreach ($topic->subtopic as $subtopic) {
      array_push($allsubtopics, $subtopic);
    }
  }
  if(sizeof($allsubtopics) > 0){
    $nextrowhtml .= classprofilereport_tr($allsubtopics, false);
  }
  if($addtitle){
    $html = '<tr><th>'.plus_get_string("students", "form").'</th>'.$html.'</tr>';
  } else {
    $html = '<tr><th></th>'.$html.'</tr>';
  }
  $html .= $nextrowhtml;
  return $html;
}
function classprofilereport_td($topics, $scoredata, $xpsetting){
  global $CLASSPROFILEAVGDATA;
  $html = '';
  $allsubtopics = array();
  foreach ($topics as $key => $topic) {
    if(sizeof($topic->subtopic) > 0){
      foreach ($topic->subtopic as $subtopic) {
        array_push($allsubtopics, $subtopic);
      }
    } else {
      $colordataposition = 0;
      $color='grey';
      $colordata = '';
      $topicid = $topic->id;
      // $colordata .= '<pre>'.$topicid.print_r($scoredata, true).'</pre>';
      if(isset($scoredata->$topicid)){

        // $colordata .= '<pre>'.print_r($scoredata->$topicid).'</pre>';
        $ques_att = $scoredata->$topicid;
        $ques_att->totalmarks1 = number_format($ques_att->totalmarks, 2);
        $ques_att->totalmaxmarks1 = number_format($ques_att->totalmaxmarks, 2);
        $ques_att->totalmaxfraction1 = number_format($ques_att->totalmaxfraction, 2);
        $ques_att->maxfraction = number_format($xpsetting->roundon, 2);
        $ques_att->fraction = (($ques_att->totalattempt > 0)?($ques_att->totalmarks1/$ques_att->totalmaxmarks1)*$ques_att->totalmaxfraction1:0);
        $ques_att->fraction = number_format($ques_att->fraction, 2);
        $ques_att->maxmark = number_format($ques_att->totalmaxmarks, 2);
        $ques_att->percent = ($ques_att->totalmarks1/$ques_att->totalmaxmarks1)*100;
        if(!isset($CLASSPROFILEAVGDATA[$topicid])){
          $sbtdata = new stdClass();
          $sbtdata->gotscore = array();
          $sbtdata->finalpercent = array();
          $sbtdata->percentscore = array();
          $sbtdata->totalscore = array();
          $CLASSPROFILEAVGDATA[$topicid] = $sbtdata;
        }

        if($ques_att->totalattempt > 0){
          array_push($CLASSPROFILEAVGDATA[$topicid]->finalpercent, $ques_att->finalpercent);
          array_push($CLASSPROFILEAVGDATA[$topicid]->gotscore, $ques_att->fraction);
          array_push($CLASSPROFILEAVGDATA[$topicid]->percentscore, $ques_att->percent);
          array_push($CLASSPROFILEAVGDATA[$topicid]->totalscore, $ques_att->maxfraction);
          if($ques_att->finalpercent > 8.5){$color = 'blue'; $colordataposition=3;}
          else if($ques_att->finalpercent > 7.0){$color = 'lightgreen';$colordataposition=2;} 
          else if($ques_att->finalpercent > 5.0){$color = 'yellow';$colordataposition=1;} 
          else {$color = 'red';$colordataposition=0;} 
        }
        $colordata .= (number_format($ques_att->finalpercent, 2)).'/'.$ques_att->maxfraction.'<br/>';
      }
      $colordata .= '<span class="smalldot '.$color.'"></span>';
      $html .=  '<td class="text-center 0" >'.(($colordataposition == 0)?$colordata:'').'</td>
                <td class="text-center 1" >'.(($colordataposition == 1)?$colordata:'').'</td>
                <td class="text-center 2" >'.(($colordataposition == 2)?$colordata:'').'</td>
                <td class="text-center 3" >'.(($colordataposition == 3)?$colordata:'').'</td>';

    }
  }
  if(sizeof($allsubtopics) > 0){
    $nextrowhtml .= classprofilereport_td($allsubtopics, $scoredata, $xpsetting);
  }
  $html .= $nextrowhtml;
  return $html;
}
function classprofilereport_avg($topics, $xpsetting){
  global $CLASSPROFILEAVGDATA;
  $html = '';
  $allsubtopics = array();
  foreach ($topics as $key => $topic) {
    if(sizeof($topic->subtopic) > 0){
      foreach ($topic->subtopic as $subtopic) {
        array_push($allsubtopics, $subtopic);
      }
    } else {
      $colordataposition = 0;
      $color='grey';
      $colordata = '';
      $topicid = $topic->id;
      if(isset($CLASSPROFILEAVGDATA[$topicid])){
        $topicdata = $CLASSPROFILEAVGDATA[$topicid];
        $percentscore = $topicdata->percentscore;
        $gotscore = $topicdata->gotscore;
        $finalpercent = $topicdata->finalpercent;
        $totalscore = $topicdata->totalscore;
        $colordataposition = 0;
        $color='grey';
        $gotscoreaverage = 0;
        $percentaverage = 0;
        $totalscoreaverage = 0;
        if(sizeof($percentscore) > 0){
          $percentaverage = array_sum($finalpercent)/count($finalpercent);
          $gotscoreaverage = array_sum($gotscore)/count($gotscore);
          $totalscoreaverage = array_sum($totalscore)/count($totalscore);
          if($percentaverage > 8.5){$color = 'blue'; $colordataposition=3;}
          else if($percentaverage > 7.0){$color = 'lightgreen';$colordataposition=2;} 
          else if($percentaverage > 5.0){$color = 'yellow';$colordataposition=1;} 
          else {$color = 'red';$colordataposition=0;} 
        }
        // $colordata .= '<pre>'.print_r($topicdata, true).'</pre>';
        $colordata .= number_format($percentaverage,2).'/'.number_format($totalscoreaverage,2).'<br/>';
      }
      $colordata .= '<span class="smalldot '.$color.'"></span>';
      $html .=  '<td class="text-center 0" >'.(($colordataposition == 0)?$colordata:'').'</td>
                <td class="text-center 1" >'.(($colordataposition == 1)?$colordata:'').'</td>
                <td class="text-center 2" >'.(($colordataposition == 2)?$colordata:'').'</td>
                <td class="text-center 3" >'.(($colordataposition == 3)?$colordata:'').'</td>';

    }
  }
  if(sizeof($allsubtopics) > 0){
    $nextrowhtml .= classprofilereport_avg($allsubtopics, $xpsetting);
  }
  $html .= $nextrowhtml;
  return $html;
}
function plus_translatelogs($logs)
{
  if(is_array($logs)){
    foreach ($logs as $key => $log) {
      $log->updatedtime_str = plus_dateToFrench($log->updatedtime);
      $log->olddata = json_decode($log->olddata);
      if(is_object($log->olddata)){
        $log->olddata->timestart_str = plus_dateToFrench($log->olddata->timestart);
        $log->olddata->timeend_str = plus_dateToFrench($log->olddata->timeend);
      }
      $log->olddata = json_encode($log->olddata);
      $logs[$key] = $log;
    }
  }
  return $logs;
}