<?php
function plus_view_monthlyreport(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  $userid = plus_get_request_parameter("userid", 0);
  $groupid = plus_get_request_parameter("groupid", 0);
  $generatemonthlyreport = plus_get_request_parameter("generatemonthlyreport", 0);
  if(empty($groupid)){
    plus_redirect(home_url()."/groups/");
    exit;
  }
  if(empty($userid)){
    plus_redirect(home_url()."/group-details/?id=".$groupid);
    exit;
  }
  if(!empty($generatemonthlyreport) && !empty($groupid) && !empty($userid)){
    $MOODLE->get("generaterMonthlyReport", null, array("groupid"=>$groupid, "userid"=>$userid));
    plus_redirect(home_url()."/monthly-report/?groupid=".$groupid."&userid=".$userid);
    exit;
  }

  $APIRES = $MOODLE->get("getMyReport", null, array("userid"=>$userid));
  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }
  $searchreq->userid = $userid;
  $searchreq->groupid = $groupid;
  $APIRES = $MOODLE->get("getMyMonthlyReport", null, $searchreq);

  // echo "<pre>";
  // print_r($APIRES);
  // die;
  $html='';
  // $html.='<pre>';
  // $html.=print_r($APIRES);
  // $html.='</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("heading", "report").'</h4>
                  <div class="card-body-action">
                    <a class="btn btn-primary" href="/monthly-report/?generatemonthlyreport=1&groupid='.$groupid.'&userid='.$userid.'"> '.plus_get_string("generatemonthlyreport", "form").' </a>
                    <a class="btn btn-warning" href="/group-details/?id='.$groupid.'"><i class="mdi mdi-keyboard-backspace"></i></a>
                  </div>                  
                ';
  $html .=        '<div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("table_col1", "report").'</th>
                          <th>'.plus_get_string("table_col2", "report").'</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
                      $counter = 1;
              if(is_object($APIRES) && is_array($APIRES->data)){
                foreach ($APIRES->data as $key => $report) {
                  $html .=  '<tr>
                              <td class="py-1">'.($key+1).'</td>
                              <td class="py-1">'.$report->month.'</td>
                              <td class=""><button class="btn btn-primary" onclick="showmonthlyreport('.$key.')">'.plus_get_string("btnviewreport", "report").'</button></td>
                            </tr>';
                }
                $searchreq->total = $APIRES->data->total;
                $searchreq->start = $APIRES->data->start;
                $searchreq->limit = $APIRES->data->limit;
              } else {
    $html .=              '<tr><td colspan="6" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
              }        
            $html .=  '</tbody>
                    </table>
                  </div>';
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '</div>
            </div>
          </div>
          <div class="monthlyreportdetails">
            <div class="monthlyreportdetails_head">
            <button  class="btn btn-primary card-body-action" id ="generatemonthlyreportpdf" > '.plus_get_string("print", "form").'  PDF</button>
            <button  class="btn btn-primary card-body-action" onclick="imageexportData(\'print_monthlyreport\')"> '.plus_get_string("print", "form").'</button>
              <span class="close" >X</span>
            </div>
            <div>
              <div class="monthlyreportdetails_body" id="print_monthlyreport"></div>
            </div>
          </div>
          <style>
.monthlyreportdetails {
    position: fixed;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    display: none;
    z-index: 9999999999;
    background: #F5F7FF;
    padding: 30px;
    overflow-x: scroll;
}
.monthlyreportdetails.show {
    display: block;
}
.monthlyreportdetails .close {
  cursor:pointer;
}
          </style>
<script>
var alldata = '.(is_array($APIRES->data)?json_encode($APIRES->data):'[]').';
function showmonthlyreport(reportindex){
    var data = alldata[reportindex];
    if(data){
        console.log("data", data);
        var html = `<div style="padding:20px;">
              <div>
              <img src="https://plus.fivestudents.com/wp-content/plugins/el-dashboard/public/images/Five-Students-Logo_big-1.webp" width="150"/>
              </div>

        <table class="datareport table table-bordered">
            <tbody>
              <tr>
                <th class="text-center" colspan="7" style="font-size: 30px;" >'.plus_get_string("monthlyreport", "report").'</th>
              </tr>
              <tr>
                <th colspan="3" style="font-size: 20px;">${data?.finaldata?.firstname} ${data?.finaldata?.lastname}</th>
                <th colspan="4" rowspan="2" class="text-center" style="font-size: 20px;">'.plus_get_string("monthlyreport_subtitle", "report").'</th>
              </tr>
              <tr>
                <th colspan="3" style="font-size: 20px;">
                  '.plus_get_string("monthlyreport_grade", "report").': ${data?.finaldata?.gradename}<br/>
                  '.plus_get_string("monthlyreport_from", "report").': ${data?.fromdate}<br/>
                  '.plus_get_string("monthlyreport_to", "report").': ${data?.todate}<br/>
                </th>
              </tr>
              <tr class="text-center">
                <th>'.plus_get_string("monthlyreport_unit", "report").'</th>
                <th>'.plus_get_string("monthlyreport_lesson", "report").'</th>
                <th>'.plus_get_string("monthlyreport_totalexcercise", "report").'</th>
                <th>'.plus_get_string("statusnotmeeting", "report").'</th>
                <th>'.plus_get_string("statusbasic", "report").'</th>
                <th>'.plus_get_string("statusgood", "report").'</th>
                <th>'.plus_get_string("statusexcelent", "report").'</th>
              </tr>
              `;
              data?.finaldata?.reportdata.forEach(function(item,index){
                console.log("item- ",item);
                html += `<tr><td rowspan="${item.subtopic.length}">${item?.name}</td>`;
                item.subtopic.forEach(function(subtopic, subindex){
                  var colordataposition = 0;
                    var color=`red`;
                    if(subtopic.percent > 85){color = `blue`; colordataposition=3;}
                    else if(subtopic.percent > 70){color = `lightgreen`;colordataposition=2;} 
                    else if(subtopic.percent > 50){color = `yellow`;colordataposition=1;} 
                    else {color = `red`;colordataposition=0;} 
                    var colordata = `${subtopic.fraction}/${subtopic.maxfraction}<br/><span class="smalldot ${color}"></span>`;
                    if(subindex != 0){
                        html += `<tr>`;
                    }
                    html += `<td>`+subtopic.name+`</td>
                      <td class="text-center" >`+subtopic.total+`</td>
                      <td class="text-center" >`+((colordataposition == 0)?colordata:``)+`</td>
                      <td class="text-center" >`+((colordataposition == 1)?colordata:``)+`</td>
                      <td class="text-center" >`+((colordataposition == 2)?colordata:``)+`</td>
                      <td class="text-center" >`+((colordataposition == 3)?colordata:``)+`</td>
                    </tr>`;
                });
                html += ``;
              });

html += `               
           </tbody>
          </table>
          </div>
    `;
    var filename = `Bilan_${data?.finaldata?.firstname}_${data?.finaldata?.lastname}_${data?.todate}`;

        $("#generatemonthlyreportpdf").attr("data-filename", filename);
        $(".monthlyreportdetails_body").html(html);
        $(".monthlyreportdetails").modal(\'show\');
    } else {
        displayToast("Falied", "report", "error");
    }
}
$(document).ready(function(){
  $(".monthlyreportdetails .close").click(function(){
    $(".monthlyreportdetails").modal(\'hide\');
  });
  $("#generatemonthlyreportpdf").click(function(){
    var filename = $(this).data("filename");
    htmltopdfexport(\'print_monthlyreport\', filename);
  });
});
</script>';
  echo $html;
}