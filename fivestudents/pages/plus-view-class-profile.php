<?php
function plus_classProfile(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->homeworkid = plus_get_request_parameter("homeworkid", 0);
  $formdata->schoolyear = plus_get_request_parameter("schoolyear", 0);
  $formdata->check_group = plus_get_request_parameter("check_group", 0);
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

 $html .= '
 			<div class="row table-scroll">
 				<table class="datareport">
 					<tr>
 						<th>Student</th>
 						<th colspan="4" class="text-center font-weight-bold" >'.($formdata->check_group?'Number':'Multiplication and Division of Numbers').'</th>
 						<th colspan="4" class="text-center font-weight-bold" >'.($formdata->check_group?'Operations':'Ratio').'</th>
 						<th colspan="4" class="text-center font-weight-bold" >'.($formdata->check_group?'Spatital Sense':'Percent').'</th>
 						<th colspan="4" class="text-center font-weight-bold" >'.($formdata->check_group?'Algebra':'Multipling and Dividing Decimals').'</th>
 						<th colspan="4" class="text-center font-weight-bold" >'.($formdata->check_group?'data':'Problem Solving with Decimals').'</th>
 					</tr>
 					<tr>
	 					<td></td>
	 					<td><p class="text-left font-weight-bold mb-0">CR</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">US</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">RA</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">COR</p></td>

	 					<td><p class="text-left font-weight-bold mb-0">CR</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">US</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">RA</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">COR</p></td>

	 					<td><p class="text-left font-weight-bold mb-0">CR</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">US</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">RA</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">COR</p></td>

	 					<td><p class="text-left font-weight-bold mb-0">CR</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">US</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">RA</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">COR</p></td>

	 					<td><p class="text-left font-weight-bold mb-0">CR</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">US</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">RA</p></td>
	 					<td><p class="text-left font-weight-bold mb-0">COR</p></td>
 					
 					</tr>
 					<tr>
	 					<td>Sanjana Dagar</td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
 					</tr>
 					<tr>
	 					<td>Deepak Ruhil</td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>

	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>

	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
 					</tr>
 					<tr>
	 					<td>Rohit Dabas</td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
 					</tr>
 					<tr>
	 					<td>Nitin Lakra</td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>

	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
 					</tr>
 					<tr>
	 					<td>Satish Taak</td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
 					</tr>

 					<tr>
	 					<td>Neeraj Rana</td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
 					</tr>

 					<tr>
	 					<td>Himanshi Singh</td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
 					</tr>

 					<tr>
	 					<td>Praveen Kumar</td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
 					</tr>

 					<tr>
	 					<td>Vikki Boora</td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
 					</tr>
 					<tr>
	 					<td>Bunty Birla</td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>

	 					<td class="text-center"><span class="smalldot blue"></span></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span></td>
	 					<td class="text-center"><span class="smalldot yellow"></span></td>
	 					<td class="text-center"><span class="smalldot red"></span></td>
 					</tr>
 					<tr>
	 					<td class="font-weight-bold mb-0">Average</td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>

	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot lightgreen"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>

	 					<td class="text-center"><span class="smalldot blue"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot yellow"></span><p class="font-weight-bold mb-0"></p></td>
	 					<td class="text-center"><span class="smalldot red"></span><p class="font-weight-bold mb-0"></p></td>
 					</tr>
 				</table>
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