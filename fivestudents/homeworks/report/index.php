<?php
  require_once("../../config.php");
  require_login();
  $homeworkid = optional_param("homeworkid", 0);
  $blockaccess = optional_param("blockaccess", "");
  $questionid = optional_param("questionid", "");
  if(empty($homeworkid)){
    redirect("{$CFG->wwwroot}/homeworks/");
  }
  $homework = get_homeworkreport($homeworkid);
  if(empty($homework)){
    redirect("{$CFG->wwwroot}/homeworks/");
  }
  if(!empty($questionid) && $blockaccess!==""){
    updatehomeqordquestionstatus($homeworkid, $questionid, $blockaccess);
    redirect("{$CFG->wwwroot}/homeworks/report/?homeworkid={$homeworkid}");
  }
  $has_internet = false;
  $onlinequiz = offline_getQuizes($homework->quiz); 
  if(!$onlinequiz && has_internet()){
    $has_internet = true;
    $args = array(
      "quizid"=>$homework->quiz
    );
    // $onlinequiz = online_getQuizes($args); 
  }
  $homeqordquestionstatus = (object)gethomeqordquestionstatus($homeworkid);
  $OUTPUT->loadjquery();
  echo $OUTPUT->header();
  $html = '';
  // $html .= '<pre>'.print_r($onlinequiz, true).'</pre>';
  $html .= '<div class="row">
            <div class="col-12 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0"></p>
                  <div class="text-right">
                  </div>
                  <br/>';
  $html .='<div id="print_homeworkreport">    
    <div class="row">
      <div class="col-sm-9">
        <div class="forms-sample blueform row">
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.get_string("level", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$homework->grade.'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.get_string("group", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$homework->group_name.'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Type</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.($homework->type?get_string("assessment", "form"):get_string("homework", "site")).'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.get_string("semester", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$homework->topicname.'">
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.get_string("lesson", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$homework->subtopicname.'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.get_string("homework", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$homework->quizname.'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.get_string("attempt", "form").'</span>
                </div>
                <select name="attempt" class="form-control" id="attempt" required="required">
                  <option value="1" selected="">'.get_string("firstattempt", "form").'</option>
                  <option value="2">'.get_string("bestattempt", "form").'</option>
                </select>
                <input type="hidden" id="groupid" value="'.$homework->groupid.'">
                <input type="hidden" id="homeworkid" value="'.$homework->id.'">
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="col-sm-3" style="font-size:15px;">
      <span class="smalldot gray"></span>&nbsp; &nbsp;<span style="">'.get_string("statusincomplete", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #ffffff"></span>&nbsp; &nbsp;<span style="">'.get_string("statusunstarted", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span>&nbsp; &nbsp;<span style="">'.get_string("statusnotmeeting", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span>&nbsp; &nbsp;<span style="">'.get_string("statusbasic", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span>&nbsp; &nbsp;<span style="">'.get_string("statusgood", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span>&nbsp; &nbsp;<span style="">'.get_string("statusexcelent", "form").'</span><br><br><br> 
    </div>
  </div><br>';
  // echo '<pre>'.print_r($homework, true).'</pre>';
  // echo '<pre>'.print_r($onlinequiz, true).'</pre>';
  $html .= '<br><div class="table-responsive"><table class="reporttable" border="1" style="border-color: #e0ebeb;table-layout: fixed; width:100%;">
    <tbody>
      <tr>
        <td class="studentname" colspan="4" style="width:140px;"></td>
        <td colspan="4" style="width:140px;">'.get_string("completiondate", "form").'</td>';
        $i=0;
    foreach($homework->questions as $question) { 
      $popuphtml = "";
      $questionTitle = "";
      $i++;
      $qid=$question->id;
      $questionid = 'Q'.$i;
      $questionblocking = '<span class="" title="Stop"><a href="'.$CFG->wwwroot.'/homeworks/report/?homeworkid='.$homeworkid.'&questionid='.$question->id.'&blockaccess=1"><i class="mdi mdi-play-circle-outline"></i></a></span>';
      if(isset($homeqordquestionstatus->$qid) && $homeqordquestionstatus->$qid==1){
        $questionblocking = '<span class="" title="Start"><a href="'.$CFG->wwwroot.'/homeworks/report/?homeworkid='.$homeworkid.'&questionid='.$question->id.'&blockaccess=0"><i class="mdi mdi-pause-circle-outline"></i></a></span>';
      }
      $questionfound = false;
      if($onlinequiz){
        $question_key = array_search($question->id, array_column($onlinequiz->allquestions, 'id'));
        if($question_key !== false){
          $foundquestion = $onlinequiz->allquestions[$question_key];
          $foundquestion->questionHint = prepareOfflineContent($foundquestion->questionHint);
          $foundquestion->questionText = prepareOfflineContent($foundquestion->questionText);
          $foundquestion->generalFeedback = prepareOfflineContent($foundquestion->generalFeedback);
          $questionfound = true;
          $popuphtml ='
          <div class="questionlist '.$homework->lang.'">
            <div class="questionhead"></div>';
            if($foundquestion->type=="multianswer"){                           
              foreach($foundquestion->subQuestion as $qkey => $subQuestion){
                $stext = ""; 
                $questionText = prepareOfflineContent($subQuestion->questionText);
                if($subQuestion->type == 'multichoiceh'){
                  $stext.='<br>';
                  foreach($subQuestion->options as $optionval){         
                    $optionval->answer = prepareOfflineContent($optionval->answer);        
                    $stext.='<label>              
                    <input type="radio"> '.$optionval->answer.'
                    </label>&nbsp;&nbsp';               
                  }
                  
                }else if($subQuestion->type == 'multichoicev'){
                  $stext.='<br>';
                  foreach($subQuestion->options as $optionval){                 
                    $optionval->answer = prepareOfflineContent($optionval->answer);        
                    $stext.='<label>              
                    <input type="radio"> '.$optionval->answer.'
                    </label>
                    </br>';               
                  }
                  
                
                }else if($subQuestion->type == 'shortanswer'){                
                  $stext.='<label>              
                  <input type="text" value="" style="display: inline-block;width: 60px;" > 
                  </label>            
                  <br>';
                } else {                
                  $stext.='<select style="display: inline-block;width: fit-content;">';
                  foreach($subQuestion->options as $optionval){
                    $optionval->answer = prepareOfflineContent($optionval->answer);        
                    $stext.='<option>'.$optionval->answer.'</option>';  
                  }
                  $stext.='</select>';    
                }
                $foundquestion->questionText = str_replace('{#'.($qkey+1).'}',$stext, $foundquestion->questionText);
              }             
            }
            else if($foundquestion->type=="ddwtos" || $foundquestion->type=="gapselect"){
              $foundquestion->questionText = preg_replace('/[[[1-9]*]]/m', '<input type="text" val="" style="display: inline-block;width: 60px;">', $foundquestion->questionText);;
            } 
            $questionTitle = $foundquestion->questionTitle;     
            $popuphtml.=' <div class="questionbody homeworkquiz">'.$foundquestion->questionText.'</div>';
            $popuphtml.='<div class="questionanssection">';
            if($foundquestion->type=='multichoice' || $foundquestion->type=='multiselect'){
              if($foundquestion->isRadioButton){
                $fieldtype='radio';
              }else{
                $fieldtype='checkbox';
              }                         
              foreach($foundquestion->options as $optionval ){               
                $optionval->answer = prepareOfflineContent($optionval->answer);        
                $popuphtml.='<label><input type="'.$fieldtype.'"> '.$optionval->answer.'</label><br>';  
              }                           
            } else if($foundquestion->type=="truefalse"){
              foreach($foundquestion->options as $optionval ){               
                $optionval->answer = prepareOfflineContent($optionval->answer);        
                $popuphtml.='<label><input type="radio"> '.$optionval->answer.'</label><br>';  
              }
            } else if($foundquestion->type=="shortanswer" || $foundquestion->type=="numerical" || $foundquestion->type=="calculated"){
                $popuphtml.='<label><input type="text" value="" disabled></label><br>';  
            } else if($foundquestion->type=="ddwtos"){
              foreach($foundquestion->options as $optionval ){               
                $optionval->answer = prepareOfflineContent($optionval->answer);        
                $popuphtml.='<span class="ddwtos" style="padding: 10px;border: 1px solid;">'.$optionval->answer.'</span>'; 
              }
            }else if($foundquestion->type=="gapselect"){
              $popuphtml.='<select>';
              foreach($foundquestion->options as $optionval){
                $optionval->answer = prepareOfflineContent($optionval->answer);        
                $popuphtml.='<option>'.$optionval->answer.'</option>';    
              }
              $popuphtml.='</select>';           
            }
             $popuphtml .='

            

            </div>
             <div id="accordion'.$foundquestion->id.'">
                <button data-toggle="collapse" class="hovernormal" data-target="#questionHint'.$foundquestion->id.'" id="hi'.$foundquestion->id.'">'.get_string("hints", "form").'</button>
                <button data-toggle="collapse" id="expla'.$foundquestion->id.'" class="hovernormal" data-target="#explanationFeedback'.$foundquestion->id.'">'.get_string("explanation", "form").'</button>

                  <div id="explanationFeedback'.$foundquestion->id.'" class="collapse" data-parent="#accordion'.$foundquestion->id.'" aria-labelledby="expla'.$foundquestion->id.'">
                    <p>
                      '.$foundquestion->generalFeedback.'
                    </p>
                  </div>
                  <div id="questionHint'.$foundquestion->id.'" class="collapse" data-parent="#accordion'.$foundquestion->id.'" aria-labelledby="hi'.$foundquestion->id.'">
                    <p>
                      '.$foundquestion->questionhint.'
                    </p>
                  </div>
             </div>
            </div>
            ';
            $allcompetencies  = array();
            if(!empty($foundquestion->competencies)){
              foreach ($foundquestion->competencies as $key => $competencie) {
                if(!empty($competencie->$strndsfield)){
                  $competencies = '<span title="'.$competencie->$strndsfieldname.'">'.$competencie->$strndsfield.'</span>';
                  array_push($allcompetencies, $competencies);
                  $allstrands_compitencies[$competencie->$strndsfield] = $competencie->$strndsfieldname;

                }
              }
            }
            $strands = '';
            if(!empty($foundquestion->strands->$strndsfield)){
              $strands = '<span title="'.$foundquestion->strands->$strndsfieldname.'">'.$foundquestion->strands->$strndsfield.'</span>';
              // $allstrands[$foundquestion->strands->$strndsfield] = $foundquestion->strands->$strndsfieldname;
            }
          $popuphtml = !empty($popuphtml)?base64_encode($popuphtml):$popuphtml;    
        }
      }






































      $html .='
        <th class="text-center " style="font-size: 12px; width:100px;">
          <div class="strands_competencies">
            <div class="text-left '.($questionfound?' viewquestion ':' ').'"  data-counter="'.$i.'" data-id="'.$question->id.'" data-lang="'.$homework->lang.'" data-popupcontent="'.$popuphtml.'" data-popupcontenttitle="'.base64_encode($questionTitle).'">
              <p style="font-size:12px; margin:0;">'.$questionid.'</p>
              <p style="font-size:12px; margin:0;">('.number_format($question->defaultmark, 2).')</p>
            </div>
            <div class="text-right">'.$questionblocking.'</div>
          </div>
        </th>';
      //if(!isset($overallqt[$i])){$overallqt[$i] = array("qt"=>0, "qmt"=>0, "qmtm"=>0);}
    }
  $html .='
        <th class="text-center" style="font-size: 12px; width:70px;">
            <div class="text-center">
              <p style="font-size:12px; margin:0;">'.get_string('score','form').'</p>
          </div></th>
      </tr>';
  $roundon =10;    
      
    $attempthtml='';
    $finalscore = array('score'=>0, 'count'=>0);
    foreach ($homework->users as $key => $user) {
    if($user->tutor){continue;}
      $attemptdata = $user->attemptdata;
      $userid = $user->id;
      $attempthtml .='
      <tr>
        <td style="font-size:11px;padding:5px;" colspan="4">'.$user->firstname.' '.$user->lastname.'</td>
        <td style="font-size:11px;padding:5px;width:140px;" colspan="4">'.((isset($attemptdata->submissiontime) && !empty($attemptdata->submissiontime))?plus_dateToFrench($attemptdata->submissiontime):'').'</td>';
    
      if($attemptdata && $attemptdata->isAttempted){
        $qt = 0;
        $qmt = 0;
        foreach($homework->questions as $questiondata) { 
          $i = $questiondata->id;
          $found_key = array_search($questiondata->id, array_column($attemptdata->questions, 'id'));
          if($found_key !== false){
            $question = $attemptdata->questions[$found_key];
          } else {
            $question =  null;
          }
          if(!isset($overallqt[$i])){$overallqt[$i] = array("qt"=>0, "qmt"=>0, "qmtm"=>0, "count"=>0);}
          if($question){
            if($question->type == 'description'){ continue; }
            //if($question->isAttempted){
              // if($attemptdata->isfinished){
                $overallqt[$i]['qt']+=$question->marks;
                $overallqt[$i]['qmt']++;
                $overallqt[$i]['qmtm']+=$question->maxMarks;
                $overallqt[$i]['count']++;
                $qt+=$question->marks;
                $qmt+=$question->maxMarks;
              // }
              $dotcolor = "";
              if(empty(floatval($question->marks)) || empty(floatval($question->maxMarks)) ){ $dotcolor="red";} 
              else if($question->marks/$question->maxMarks>=0.85){ $dotcolor="blue"; }
              else if($question->marks/$question->maxMarks>=0.70){ $dotcolor="lightgreen";}
              else if($question->marks/$question->maxMarks>=0.50){ $dotcolor="yellow";}
              else { $dotcolor="red";}
              $percentscore = 1;
              $attempthtml .='<td class="text-center" style="font-size:11px;padding:5px;">'.number_format($question->marks,2).'<br><span class="smalldot '.$dotcolor.'"></span></td>';
            //} else {
            //  $attempthtml .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
            //}
          } else {
            $attempthtml .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
          }
        }
        $dotcolor = "";
        if(!$attemptdata->isfinished){
          $attempthtml .='<td class="text-center" style="font-size:11px;padding:5px;">N/C<br><span class="smalldot gray"></span></td>';
        } else {
          if(empty($qt) || empty($qmt)){ $dotcolor="red";} 
          else if($qt/$qmt>=0.85){ $dotcolor="blue"; }
          else if($qt/$qmt>=0.70){ $dotcolor="lightgreen";}
          else if($qt/$qmt>=0.50){ $dotcolor="yellow";}
          else { $dotcolor="red";}
          if(empty($qmt)){
            $finalqtn = 0;
          } else {
            $finalqtn = ($qt/$qmt*$roundon);
          }
          $finalqt = number_format($finalqtn, 2);
          $finalscore['score']+=$finalqt;
          $finalscore['count']++;
          $attempthtml .='<td class="text-center" style="font-size:11px;padding:5px;">'.$finalqt.'<br><span class="smalldot '.$dotcolor.'"></span></td>';
        }
      } else {
        // foreach ($APIRES->data->reportdata->allquestions as $key => $question) {
        for ($i=0; $i < $homework->totalquestion; $i++) { 
            $attempthtml .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
        }
        $attempthtml .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
      }
    }
$htmlhead = "";
$htmlhead .='<tr>';
$htmlhead .='<td colspan="4">'.get_string("groupaverage", "form").'</td>';
$htmlhead .='<th colspan="4"></th>';
foreach($homework->questions as $questiondata) { 
  $i = $questiondata->id;
  if(isset($overallqt[$i]) && $overallqt[$i]['count'] && $overallqt[$i]['count'] > 0){
    $ovrall = $overallqt[$i];
    $dotcolor = "";
    if(empty($ovrall['qt']) || empty($ovrall['qmtm'])){ $dotcolor="red";} 
    else if($ovrall['qt']/$ovrall['qmtm']>=0.85){ $dotcolor="blue"; }
    else if($ovrall['qt']/$ovrall['qmtm']>=0.70){ $dotcolor="lightgreen";}
    else if($ovrall['qt']/$ovrall['qmtm']>=0.50){ $dotcolor="yellow";}
    else { $dotcolor="red";}
    if(empty($ovrall['qt']) || empty($ovrall['qmt'])){ 
      $finalqt = number_format(0, 2);
    } else {
      $finalqt = number_format(($ovrall['qt']/$ovrall['qmt']), 2);
    }
    $htmlhead .='<td class="text-center" style="font-size:12px;padding:5px;">'.$finalqt.'<br><span class="smalldot '.$dotcolor.'"></span></td>';
  } else {
    $htmlhead .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
  }
}

// if($finalscore['count'] > 0){
//   foreach ($overallqt as $key => $ovrall) {
//     // if($ovrall['type'] == 'description'){ continue; }
//   }
// } else {
//   for ($i=0; $i < $homework->totalquestion; $i++) { 
//     $htmlhead .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
//   }
// }
  $dotcolor = "";
  // print_r($finalscore);
  // die;
  if(empty($finalscore['score']) || empty($finalscore['count'])){ $dotcolor="red";} 
  else if($finalscore['score']/$finalscore['count']/$roundon>=0.85){ $dotcolor="blue"; }
  else if($finalscore['score']/$finalscore['count']/$roundon>=0.70){ $dotcolor="lightgreen";}
  else if($finalscore['score']/$finalscore['count']/$roundon>=0.50){ $dotcolor="yellow";}
  else { $dotcolor="red";}
  if(empty($finalscore['score']) || empty($finalscore['count'])){ 
    $finalqt = number_format(0, 2);
  } else {
    $finalqt = number_format(($finalscore['score']/$finalscore['count']), 2);
  }
$htmlhead .='<td class="text-center" style="font-size:12px;padding:5px;">'.$finalqt.'<br><span class="smalldot '.$dotcolor.'"></span></td>';
$htmlhead .='</tr>';
     
    $html .=$htmlhead.$attempthtml;
    
  $html .='
      </tbody></table></div>
</div>';
  $html .='
                </div>
              </div>
            </div>
          </div>';

$html.='      <div class="modal fade" id="questionviewer" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
          </div>
        </div>      
      </div>
    </div>'; 
$html .='<script>
  $(document).ready(function(){
    $(".viewquestion").click(function(){
      var questioncounter = $(this).data("counter");
      var lang = $(this).data("lang");
      var questiontitle = decodeURIComponent(escape(window.atob($(this).data("popupcontenttitle"))));
      var questiontext = decodeURIComponent(escape(window.atob($(this).data("popupcontent"))));
      console.log("questioncounter- ", questioncounter);
      console.log("questiontitle- ", questiontitle);
      console.log("questiontext- ", questiontext);
      if(lang && !$("#questionviewer").hasClass(lang)){
        $("#questionviewer").addClass(lang);
      }
      $("#questionviewer .modal-title").html("Q"+questioncounter+": "+questiontitle);
      $("#questionviewer .modal-body").html(questiontext);
      $("#questionviewer").modal();
    });
  });
</script>';

  echo $html;
  echo $OUTPUT->footer();
