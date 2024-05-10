<?php
function plus_studentProfile(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->homeworkid = plus_get_request_parameter("homeworkid", 0);
  $formdata->schoolyear = plus_get_request_parameter("schoolyear", 0);
  $formdata->start = plus_get_request_parameter("start", 0);
  $formdata->limit = plus_get_request_parameter("limit", 1);
  $formdata->total = plus_get_request_parameter("total", 15);
  // if(empty($formdata->groupid) || empty($formdata->homeworkid)){
  //   plus_redirect("/homework/");
  // }
  $formdata->attempt = plus_get_request_parameter("attempt", 1);
  $APIRES = $MOODLE->get("GetHomeworkReport", null, $formdata);
  $html='';
  // $html .=   '<div class="table-responsive">'.(gettype($APIRES) ).'</div>';
  // $html .=   '<div class="table-responsive">'.(is_object($APIRES)?json_encode($APIRES):$APIRES).'</div>';
  if(!is_object($APIRES)){ 
    $html .=  '<div class="alert alert-danger">There is some error</div>';     
    return $html;
  }
/*    echo "<pre>";
   print_r($APIRES->data->reportdata);
   echo "</pre>";
   die;  */
   
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
                  <span class="input-group-text">Grade '.plus_get_string("level", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="6">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("group", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="601">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">Mode</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="Mission">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("semester", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="UNIT 2: OPERATIONS">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("lesson", "form").'</span>
                </div>
                <input type="text" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="All">
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
                  <option value="1" '.((@$formdata->attempt==1)?'selected':'').' >'.plus_get_string("firstattempt", "form").'</option>
                  <option value="2" '.((@$formdata->attempt==2)?'selected':'').' >'.plus_get_string("bestattempt", "form").'</option>
                </select>
                <input type="hidden" id="groupid" value="'.@$formdata->groupid.'"/>
                <input type="hidden" id="homeworkid" value="'.@$formdata->homeworkid.'"/>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("from", "form").'</span>
                </div>
                <input type="date" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="2022-10-01">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">'.plus_get_string("to", "form").'</span>
                </div>
                <input type="date" readonly="readonly" disabled="disabled" class="form-control hovernormal" value="2022-10-30">
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
      <span class="dot" style="width:10px; height:10px; border-radius: 50%; background-color: #0100f3"></span>&nbsp; &nbsp;<span style="">'.plus_get_string("statusexcelent", "form").'</span><br>
    </div>
  </div><br/><br/>
</div>';
$html .=  ' 
          </div>
        </div>
      </div>
    </div>
<style type="text/css">
  .datareport .smalldot {
    width: 15px;
    height: 15px;
  }
  table.datareport *{
    vertical-align: middle;
}
</style>

    ';
 $html.='
 		<div class="mt-5">
 			<table class="datareport">
 				<tbody>
 					<tr>
 						<th class="text-center" colspan="7" style="font-size: 30px;" >Student\'s Profile Report</th>
 					</tr>
          <tr>
            <th colspan="3" style="font-size: 20px;">Alicia Brown</th>
            <th colspan="4" rowspan="2" class="text-center" style="font-size: 20px;">Student\'s level per lesson and competency</th>
          </tr>
 					<tr>
 						<th colspan="3" style="font-size: 20px;">
              Grade: 6<br/>
              From: 10/01/2022<br/>
              To: 10/31/2022<br/>
            </th>
 					</tr>
 					<tr>
 						<th>Unit</th>
 						<th>Lesson</th>
 						<th>Competency</th>
 						<th>'.plus_get_string("statusnotmeeting", "form").'</th>
            <th>'.plus_get_string("statusbasic", "form").'</th>
            <th>'.plus_get_string("statusgood", "form").'</th>
            <th>'.plus_get_string("statusexcelent", "form").'</th>
 					</tr>
 					<tr>
 						<td rowspan="15">UNIT 2: OPERATIONS</td>
 						<td rowspan="3">Multiplication and Division of Numbers</td>
 						<td >Communicating and Representing</td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
            <td class="text-center sBlue" ></td>
 					</tr>
          <tr>
            <td >Understanding and Solving</td>
            <td class="text-center" ></td>
            <td class="text-center lYellow" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
          </tr>
          <tr>
            <td >Reasoning and Analyzing</td>
            <td class="text-center dRed" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
          </tr>
          <tr>
            <td rowspan="2">Ratio</td>
            <td >Understanding and Solving</td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
            <td class="text-center lGreen" ></td>
            <td class="text-center" ></td>
          </tr>
          <tr>
            <td >Reasoning and Analyzing</td>
            <td class="text-center" ></td>
            <td class="text-center lYellow" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
          </tr>
          <tr>
            <td rowspan="2">Percent</td>
            <td >Understanding and Solving</td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
            <td class="text-center lGreen" ></td>
            <td class="text-center" ></td>
          </tr>
          <tr>
            <td >Reasoning and Analyzing</td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
            <td class="text-center lGreen" ></td>
            <td class="text-center" ></td>
          </tr>
          <tr>
            <td rowspan="2">Multipling and Dividing Decimals</td>
            <td >Understanding and Solving</td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
            <td class="text-center lGreen" ></td>
            <td class="text-center" ></td>
          </tr>
          <tr>
            <td >Reasoning and Analyzing</td>
            <td class="text-center" ></td>
            <td class="text-center lYellow" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
          </tr>
          <tr>
            <td rowspan="2">Problem Solving with Decimals</td>
            <td >Reasoning and Analyzing</td>
            <td class="text-center" ></td>
            <td class="text-center lYellow" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
          </tr>
 					<tr>
            <td >Connecting and Reflecting</td>
            <td class="text-center dRed" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
            <td class="text-center" ></td>
          </tr>
 				</tbody>
 			</table>
 		</div>
 ';

  $html .=      plus_pagination($formdata->start, $formdata->limit, $formdata->total, "group", false);



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
      var questiontitle = decodeURIComponent(escape(window.atob($(this).data("popupcontenttitle"))));
      var questiontext = decodeURIComponent(escape(window.atob($(this).data("popupcontent"))));
      console.log("questioncounter- ", questioncounter);
      console.log("questiontitle- ", questiontitle);
      console.log("questiontext- ", questiontext);
      $("#questionviewer .modal-title").html("Q"+questioncounter+": "+questiontitle);
      $("#questionviewer .modal-body").html(questiontext);
      $("#questionviewer").modal();
    });
  });
</script>';
  
    return $html;
  }