<?php
function plus_view_homework_report(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->homeworkid = plus_get_request_parameter("homeworkid", 0);
  $formdata->schoolyear = plus_get_request_parameter("schoolyear", 0);
  $formdata->questionid = plus_get_request_parameter("questionid", 0);
  $formdata->blockaccess = plus_get_request_parameter("blockaccess", 0);
  if(empty($formdata->groupid) || empty($formdata->homeworkid)){
    plus_redirect("/homework/");
  }
  if(isset($_GET['blockaccess']) && !empty($formdata->questionid)){
    $DELETEAPIRES = $MOODLE->get("UpdateQuestionBlocking", null, $formdata);
    plus_redirect('/homework-report/?groupid='.$formdata->groupid.'&homeworkid='.$formdata->homeworkid);
    exit;
  }
  $lang = plus_getuserlang();
  // echo $lang;
  $strndsfield = strtolower($lang.'code');
  $strndsfieldname = strtolower($lang.'name');
  $formdata->attempt = plus_get_request_parameter("attempt", 1);
  $APIRES = $MOODLE->get("GetHomeworkReport", null, $formdata);
  $html='';
  // $html .=   '<div class="table-responsive">'.(gettype($APIRES) ).'</div>';
  // $html .=   '<div class="table-responsive">'.(is_object($APIRES)?json_encode($APIRES):$APIRES).'</div>';
  if(!is_object($APIRES)){ 
    $html .=  '<div class="alert alert-danger">There is some error</div>';     
    return $html;
  }
  $haveimediatefeedback = false;
$allstrands = array();
$allstrands_compitencies = array();

$headoptionshtml = "";
        $qcountr = 0;
        $blockedquestions = $APIRES->data->reportdata->blockedquestions;
        $haveimediatefeedback = $APIRES->data->reportdata->immediatefeedback;
        foreach ($APIRES->data->reportdata->allquestions as $key => $question) {
          $imediatefeedbackbtn = '';
          if($haveimediatefeedback){
            $imediatefeedbackbtn = '<span class="" title="Stop"><a href="/homework-report/?groupid='.$formdata->groupid.'&homeworkid='.$formdata->homeworkid.'&questionid='.$question->qid.'&blockaccess=1"><i style="font-size: 20px;" class="mdi mdi-play-circle-outline"></i></a></span>';
            if(in_array($question->qid, $blockedquestions)){
              $imediatefeedbackbtn = '<span class="" title="Start"><a href="/homework-report/?groupid='.$formdata->groupid.'&homeworkid='.$formdata->homeworkid.'&questionid='.$question->qid.'&blockaccess=0"><i style="font-size: 20px;" class="mdi mdi-pause-circle-outline"></i></a></span>';
            }
          }
          if($question->type == 'description'){ continue; } $qcountr++;
          if(!isset($overallqt[$key])){$overallqt[$key] = array("qt"=>0, "qmt"=>0, "qmtm"=>0, "type"=>$question->type);}
    $popuphtml ='
        <div class="questionlist '.$APIRES->data->reportdata->lang.'">
          <div class="questionhead"></div>';
          if($question->type=="multianswer"){                           
            foreach($question->subQuestion as $qkey => $subQuestion){
              $stext = ""; 
              $questionText = $subQuestion->questionText;
              if($subQuestion->type == 'multichoiceh'){
                $stext.='<br>';
                foreach($subQuestion->options as $optionval){                 
                  $stext.='<label>              
                  <input type="radio"> '.$optionval->answer.'
                  </label>&nbsp;&nbsp';               
                }
                
              }else if($subQuestion->type == 'multichoicev'){
                $stext.='<br>';
                foreach($subQuestion->options as $optionval){                 
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
                  $stext.='<option>'.$optionval->answer.'</option>';  
                }
                $stext.='</select>';    
              }
              $question->questionText = str_replace('{#'.($qkey+1).'}',$stext, $question->questionText);
            }             
          }
          else if($question->type=="ddwtos" || $question->type=="gapselect"){
            $question->questionText = preg_replace('/[[[1-9]*]]/m', '<input type="text" val="" style="display: inline-block;width: 60px;">', $question->questionText);;
          }         
          $popuphtml.=' <div class="questionbody homeworkquiz">'.$question->questionText.'</div>';
           $popuphtml.='<div class="questionanssection">';
          if($question->type=='multichoice' || $question->type=='multiselect'){
            if($question->isRadioButton){
              $fieldtype='radio';
            }else{
              $fieldtype='checkbox';
            }                         
            foreach($question->options as $optionval ){               
            $popuphtml.='<label>             
            <input type="'.$fieldtype.'"> '.$optionval->answer.'
            </label>
            
            <br>';  
            }                           
          }else if($question->type=="truefalse"){
            foreach($question->options as $optionval ){               
            $popuphtml.='<label>             
            <input type="radio"> '.$optionval->answer.'
            </label>
            
            <br>';  
            }
          }else if($question->type=="shortanswer" || $question->type=="numerical" || $question->type=="calculated"){                            
              $popuphtml.='<label>             
              <input type="text" value="" disabled> 
              </label>            
              <br>';  
          }else if($question->type=="ddwtos"){
            foreach($question->options as $optionval ){               
            $popuphtml.='            
            <span class="ddwtos" style="padding: 10px;border: 1px solid;">'.$optionval->answer.'</span>'; 
            }
          }else if($question->type=="gapselect"){
            $popuphtml.='<select>';
            foreach($question->options as $optionval){
            $popuphtml.='<option>'.$optionval->answer.'</option>';    
            }
            $popuphtml.='</select>';           
          }
           $popuphtml .='

          

          </div>
           <div id="accordion'.$question->id.'">
              <button data-toggle="collapse" class="hovernormal" data-target="#questionHint'.$question->id.'" id="hi'.$question->id.'">'.plus_get_string("hints", "form").'</button>
              <button data-toggle="collapse" id="expla'.$question->id.'" class="hovernormal" data-target="#explanationFeedback'.$question->id.'">'.plus_get_string("explanation", "form").'</button>

                <div id="explanationFeedback'.$question->id.'" class="collapse" data-parent="#accordion'.$question->id.'" aria-labelledby="expla'.$question->id.'">
                  <p>
                    '.$question->generalFeedback.'
                  </p>
                </div>
                <div id="questionHint'.$question->id.'" class="collapse" data-parent="#accordion'.$question->id.'" aria-labelledby="hi'.$question->id.'">
                  <p>
                    '.$question->questionhint.'
                  </p>
                </div>
           </div>
          </div>
          ';
          $allcompetencies  = array();
          if(!empty($question->competencies)){
            foreach ($question->competencies as $key => $competencie) {
              if(!empty($competencie->$strndsfield)){
                $competencies = '<span title="'.$competencie->$strndsfieldname.'">'.$competencie->$strndsfield.'</span>';
                array_push($allcompetencies, $competencies);
                $allstrands_compitencies[$competencie->$strndsfield] = $competencie->$strndsfieldname;

              }
            }
          }
          $strands = '';
          if(!empty($question->strands->$strndsfield)){
            $strands = '<span title="'.$question->strands->$strndsfieldname.'">'.$question->strands->$strndsfield.'</span>';
            // $allstrands[$question->strands->$strndsfield] = $question->strands->$strndsfieldname;
          }

        $popuphtml = !empty($popuphtml)?base64_encode($popuphtml):$popuphtml;      
          $headoptionshtml .='<th class="text-center " style="font-size: 12px; width:100px;"  data-counter="'.$qcountr.'" data-lang="'.$APIRES->data->reportdata->lang.'" data-popupcontent="'.$popuphtml.'" data-popupcontenttitle="'.base64_encode($question->questionTitle).'">
            <div class="strands_competencies">
              <div class="text-center viewquestion" data-counter="'.$qcountr.'" data-lang="'.$APIRES->data->reportdata->lang.'" data-popupcontent="'.$popuphtml.'" data-popupcontenttitle="'.base64_encode($question->questionTitle).'" style="width: -webkit-fill-available;">
                <p style="font-size:12px; margin:0;">Q'.$qcountr.'</p>
                <p style="font-size:12px; margin:0;">('.$question->maxMarks.')</p>
              </div>
              <div class="text-right">
              '.(!empty($strands)?'<p style="font-size:12px; margin:0; cursor: pointer;">('.$strands.')</p>':'').'
              '.(!empty($allcompetencies)?'<p style="font-size:12px; margin:0; cursor: pointer;">('.implode("|", $allcompetencies).')</p>':'').$imediatefeedbackbtn.'
              </div>
            </div>
          </th>';

        }










   
  $html .=  '     
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="homeworkreport-card">
          <div class="card-body haveaction">
            <h4 class="card-title"></h4>
            <button  class="btn btn-primary card-body-action hide" onclick="imageexportData(\'print_homeworkreport\')"> '.plus_get_string("print", "form").'</button>
            ';
  $html .=  ' 
  <div id="print_homeworkreport">    
    <div class="row">
      <div class="col-sm-9">
        <div class="forms-sample blueform row">
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("level", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$APIRES->data->reportdata->gradename.'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("group", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$APIRES->data->reportdata->groupname.'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Type</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.($APIRES->data->reportdata->type?plus_get_string("assessment", "form"):plus_get_string("homework", "site")).'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("semester", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$APIRES->data->reportdata->semester.'">
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("lesson", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$APIRES->data->reportdata->lesson.'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("quiz", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="'.$APIRES->data->reportdata->quizname.'">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("attempt", "form").'</span>
                </div>
                <select name="attempt" class="form-control" id="attempt" required="required">
                  <option value="1" '.(($formdata->attempt==1)?'selected':'').' >'.plus_get_string("firstattempt", "form").'</option>
                  <option value="2" '.(($formdata->attempt==2)?'selected':'').' >'.plus_get_string("bestattempt", "form").'</option>
                </select>
                <input type="hidden" id="groupid" value="'.$formdata->groupid.'"/>
                <input type="hidden" id="homeworkid" value="'.$formdata->homeworkid.'"/>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="col-sm-3"  style="font-size:15px;">
      <span class="smalldot gray"></span>&nbsp; &nbsp;<span style="">'.plus_get_string("statusincomplete", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #ffffff"></span>&nbsp; &nbsp;<span style="">'.plus_get_string("statusunstarted", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #FF0000"></span>&nbsp; &nbsp;<span style="">'.plus_get_string("statusnotmeeting", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #fff53b"></span>&nbsp; &nbsp;<span style="">'.plus_get_string("statusbasic", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #00FF33"></span>&nbsp; &nbsp;<span style="">'.plus_get_string("statusgood", "form").'</span><br>
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span>&nbsp; &nbsp;<span style="">'.plus_get_string("statusexcelent", "form").'</span><br><br><br>';
      if(!empty($allstrands) || !empty($allstrands_compitencies)){
        // foreach ($allstrands as $key => $value) {
        //   $html .= '<p><strong>'.$key.'</strong>: '.$value.'</p>';          
        // }
        // foreach ($allstrands_compitencies as $key => $value) {
        //   $html .= '<p><strong>'.$key.'</strong>: '.$value.'</p>';          
        // }
      }
  $html .=  ' 
    </div>
  </div><br/><br/>';
    $overallqt = array();
    $html .='<div class="table-responsive"><table class="reporttable" border="1" style="border-color: #e0ebeb;table-layout: fixed; width:100%;">
    <tr>
        <td class="studentname text-center" colspan="4" style="width:140px;">'.plus_get_string("teacher", "form").' : '.$APIRES->data->reportdata->teacher.'</td>
        <td class="text-center" colspan="4" style="width:140px;">'.plus_get_string("completiondate", "form").' </td>
        ';
    $html .= $headoptionshtml;
    $html .='<th class="text-center" style="font-size: 12px; width:45px;">'.plus_get_string("score", "form").'</th>';

    $html .='</tr>';
    // $html .='<table class="reporttable" border="1" style="border-color: #e0ebeb;table-layout: fixed; width:100%;">';
    $htmldata = "";
    $finalscore = array("score"=>0, "count"=>0);
    foreach ($APIRES->data->reportdata->allstudents as $key => $student) {
      $htmldata .='<tr>
        <td class="text-left" style="font-size:11px;padding:5px;" colspan="4">'.$student->lastname.' '.$student->firstname.'</td>
        <td class="text-center" style="font-size:11px;padding:5px;width:140px;" colspan="4">'.(!empty($student->attempted->timefinish)?plus_dateToFrench($student->attempted->timefinish):'').'</td>';
      if(is_object($student->attempted)){
        $qt = 0;
        $qmt = 0;
        foreach ($student->attemptdata as $key => $question) {
          if(!isset($overallqt[$key])){$overallqt[$key] = array("qt"=>0, "qmt"=>0, "qmtm"=>0, "type"=>$question->type);}
          if($question->type == 'description'){ continue; }
          if($question->isAttempted){
            // if($student->attempted->state == 'finished'){
              $overallqt[$key]['qt']+=$question->marks;
              $overallqt[$key]['qmt']++;
              $overallqt[$key]['qmtm']+=$question->maxMarks;
              // $overallqt[$key]['qmt']+=$question->maxMarks;
              $qt+=$question->marks;
              $qmt+=$question->maxMarks;
            // }

            $dotcolor = "";
            if(empty($question->marks) || empty($question->maxMarks) ){ $dotcolor="red";} 
            else if($question->marks/$question->maxMarks>=0.85){ $dotcolor="blue"; }
            else if($question->marks/$question->maxMarks>=0.70){ $dotcolor="lightgreen";}
            else if($question->marks/$question->maxMarks>=0.50){ $dotcolor="yellow";}
            else { $dotcolor="red";}
            $percentscore = 1;
            $htmldata .='<td class="text-center" style="font-size:11px;padding:5px;">'.number_format($question->marks, 2).'<br><span class="smalldot '.$dotcolor.'"></span></td>';
          } else {
            $htmldata .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
          }
        }
        $dotcolor = "";
        if($student->attempted->state != 'finished'){
          $htmldata .='<td class="text-center" style="font-size:11px;padding:5px;">N/C<br><span class="smalldot gray"></span></td>';
        } else {
          if(empty($qt) || empty($qmt)){ $dotcolor="red";} 
          else if($qt/$qmt>=0.85){ $dotcolor="blue"; }
          else if($qt/$qmt>=0.70){ $dotcolor="lightgreen";}
          else if($qt/$qmt>=0.50){ $dotcolor="yellow";}
          else { $dotcolor="red";}
          if(empty($qmt)){
            $finalqtn = 0;
          } else {
            $finalqtn = ($qt/$qmt*$APIRES->data->reportdata->roundon);
          }
          $finalqt = number_format($finalqtn, 2);
          $finalscore['score']+=$finalqt;
          $finalscore['count']++;
          $htmldata .='<td class="text-center" style="font-size:11px;padding:5px;">'.$finalqt.'<br><span class="smalldot '.$dotcolor.'"></span></td>';
        }
      } else {
        foreach ($APIRES->data->reportdata->allquestions as $key => $question) {
          if($question->type == 'description'){ continue; }
            $htmldata .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
        }
        $htmldata .='<td class="text-center" style="font-size:11px;padding:5px;"><span class="smalldot gray"></span></td>';
      }
    $htmldata .='</tr>';
    }
$htmlhead = "";
$htmlhead .='<tr>';
$htmlhead .='<td class="text-center" colspan="4">'.plus_get_string("groupaverage", "form").'</td>';
$htmlhead .='<th class="text-center" colspan="4"></th>';
foreach ($overallqt as $key => $ovrall) {
  if($ovrall['type'] == 'description'){ continue; }
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
}
  $dotcolor = "";
  // print_r($finalscore);
  // die;
  if(empty($finalscore['score']) || empty($finalscore['count'])){ $dotcolor="red";} 
  else if($finalscore['score']/$finalscore['count']/$APIRES->data->reportdata->roundon>=0.85){ $dotcolor="blue"; }
  else if($finalscore['score']/$finalscore['count']/$APIRES->data->reportdata->roundon>=0.70){ $dotcolor="lightgreen";}
  else if($finalscore['score']/$finalscore['count']/$APIRES->data->reportdata->roundon>=0.50){ $dotcolor="yellow";}
  else { $dotcolor="red";}
  if(empty($finalscore['score']) || empty($finalscore['count'])){ 
    $finalqt = number_format(0, 2);
  } else {
    $finalqt = number_format(($finalscore['score']/$finalscore['count']), 2);
  }
$htmlhead .='<td class="text-center" style="font-size:12px;padding:5px;">'.$finalqt.'<br><span class="smalldot '.$dotcolor.'"></span></td>';
$htmlhead .='</tr>';
$html .= $htmlhead.$htmldata;
$html .='</table></div>
</div>';
$html .=  ' 
          </div>
        </div>
      </div>
    </div>
    ';

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
    $("#attempt").change(function(){
      var attempt = $(this).val();
      var groupid = $("#groupid").val();
      var homeworkid = $("#homeworkid").val();
      location.href = "'.home_url( $wp->request ).'?groupid="+groupid+"&homeworkid="+homeworkid+"&attempt="+attempt;
    });
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
  return $html;
}
