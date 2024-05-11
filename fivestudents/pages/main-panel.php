<?php
function main_panel(){
  global $CFG, $MOODLESESSION;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $formdata = new stdClass();
  $formdata->categoryid = plus_get_request_parameter("categoryid", 0);
  $formdata->groupid = plus_get_request_parameter("groupid", 0);
  $formdata->homeworkid = plus_get_request_parameter("homeworkid", 0);
  $formdata->filtertype = plus_get_request_parameter("filtertype", 1);
  $formdata->fromdate = plus_get_request_parameter("fromdate", date("Y-m-d"));
  $formdata->todate = plus_get_request_parameter("todate", date("Y-m-d"));
  $formdata->attempt = plus_get_request_parameter("attempt", 1);

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $APIRES = $MOODLE->get("Dashboard", null, $formdata);
  // echo "<pre>";
  // print_r($APIRES);
  // die;
  $html =  '<div class="row">'.(is_string($APIRES)?$APIRES:json_encode($APIRES)).'</div>';
  $html =  '<div class="row">';
  $html .=  '<div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">'.plus_get_string("welcome", "dashboard").' '.ucwords($current_user->data->display_name).'</h3>
                </div>';
                if(current_user_can('plus_viewdashboardkpi')){
  $html .=  '   <div class="col-12 col-xl-4">
                  <div class="justify-content-end d-flex">
                    <div class="btn-group btn-group-responsive" role="group" aria-label="Basic example">
                      <button type="button" data-filtertype="1" class="btn btn-outline-secondary filtertype  '.($formdata->filtertype == 1?'active':'').'">'.plus_get_string("today", "form").'</button>
                      <button type="button" data-filtertype="2" class="btn btn-outline-secondary filtertype '.($formdata->filtertype == 2?'active':'').'">'.plus_get_string("yesterday", "form").'</button>
                      <button type="button" data-filtertype="3" class="btn btn-outline-secondary filtertype '.($formdata->filtertype == 3?'active':'').'">'.plus_get_string("thismonth", "form").'</button>
                      <button type="button" data-toggle="collapse" data-target="#customdatefiler" aria-expanded="'.($formdata->filtertype != 4?'false':'true').'" data-filtertype="4" class="btn btn-outline-secondary filtertype '.($formdata->filtertype == 4?'active':'').'">'.plus_get_string("tochoose", "form").'</button>
                    </div>                 
                  </div>
                  <div id="customdatefiler" class="'.($formdata->filtertype != 4?'collapse':'').' mt-4" aria-expanded="'.($formdata->filtertype != 4?'false':'true').'" '.($formdata->filtertype != 4?'style="height: 0px";':'').'>
                    <div class="form-group">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">'.plus_get_string("from", "form").'</span>
                        </div>
                        <input type="date" class="form-control datefilter" value="'.$formdata->fromdate.'" data-filterdate="fromdate"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">'.plus_get_string("to", "form").'</span>
                        </div>
                        <input type="date" class="form-control datefilter" value="'.  $formdata->todate.'" data-filterdate="todate"/>
                      </div>
                    </div>
                  </div>
                </div>
';
                }

  $html .=  '
              </div>
            </div>
          </div>';
  // $html .= (is_object($APIRES)?json_encode($APIRES):$APIRES);
  // if(is_object($MOODLESESSION)){

    if(current_user_can('plus_viewsubscriptionkpi')){
      $html .=  '<div class="row">
                <div class="col-md-12 grid-margin transparent">
                  <div class="row">';
      $html .=  '<div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-blue">
                        <div class="card-body">
                          <p class="mb-2 fs-30">'.$APIRES->data->subscriptionskpi->active.'</p>
                          <p class="fs-30 mb-4">'.plus_get_string("active", "form").'</p>
                        </div>
                      </div>
                    </div>';
      $html .=  '<div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-red">
                        <div class="card-body">
                          <p class="mb-2 fs-30">'.$APIRES->data->subscriptionskpi->expired.'</p>
                          <p class="fs-30 mb-4">'.plus_get_string("expired", "form").'</p>
                        </div>
                      </div>
                    </div>';
      $html .=  '<div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-light-danger">
                        <div class="card-body">
                          <p class="mb-2 fs-30">'.$APIRES->data->subscriptionskpi->expiringin3days.'</p>
                          <p class="fs-30 mb-4">'.plus_get_string("expiringin3days", "form").'</p>
                        </div>
                      </div>
                    </div>';
      $html .=  '<div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-lightyellow">
                        <div class="card-body">
                          <p class="mb-2 fs-30">'.$APIRES->data->subscriptionskpi->expiringin7days.'</p>
                          <p class="fs-30 mb-4">'.plus_get_string("expiringin7days", "form").'</p>
                        </div>
                      </div>
                    </div>';
      $html .=  '</div>
              </div>
            </div>';
    } 
    if(current_user_can('plus_viewdashboardkpi')) {

  $catcourses = '<option value="0">'.plus_get_string("all", "site").'</option>';
  $groups = '<option value="0">'.plus_get_string("all", "site").'</option>';
  $selectedgrade = null;
  $selectedgroup = null;
  $allgrades = array();
  $allgroup = array();
  if(isset($APIRES->data->grades) && sizeof($APIRES->data->grades)>0){
    $allgrades = $APIRES->data->grades;
    foreach ($APIRES->data->grades as $key => $grade) {
      // if($key == 0 ) {$selectedgrade = $grade;}
        $sel = '';
        if($grade->categoryid == $formdata->categoryid){
          $sel = "selected";
          $selectedgrade = $grade;
        }
        $catcourses .= '<option '.$sel.' value="'.$grade->categoryid.'">'.$grade->name.'</option>';
    }
  }
  if($selectedgrade && is_array($selectedgrade->allgroup) && !empty($selectedgrade->allgroup)){
      $sel = '';
      $allgroup = $selectedgrade->allgroup;
      foreach ($selectedgrade->allgroup as $key => $group) {
        $sel = "";
        if($group->groupid == $formdata->groupid){
          $sel = "selected";
          $selectedgroup = $group;
        }
        $groups .= '<option '.$sel.' value="'.$group->groupid.'">'.$group->name.'</option>';
      }
  }
  $homeworks = '<option value="0">'.plus_get_string("all", "site").'</option>';
  if($selectedgroup && is_array($selectedgroup->homeworks) && !empty($selectedgroup->homeworks)){
      $sel = '';
      foreach ($selectedgroup->homeworks as $key => $homework) {
        $sel = "";
        if($homework->id == $formdata->homeworkid){
          $sel = "selected";
        }
        $homeworks .= '<option '.$sel.' value="'.$homework->id.'">'.$homework->name.'</option>';
      }
  }
      $reportpageurl = home_url( "/dashboardstatus-report");
      $formdata->status = 0;
      $report_incompleted = plus_getpageurl($reportpageurl, $formdata);
      $formdata->status = 1;
      $report_unstarted = plus_getpageurl($reportpageurl, $formdata);
      $formdata->status = 2;
      $report_completed = plus_getpageurl($reportpageurl, $formdata);
      $formdata->status = 5;
      $report_latecompleted = plus_getpageurl($reportpageurl, $formdata);

      $formdata->status = 3;
      
      $report_passed = plus_getpageurl($reportpageurl, $formdata);
      $formdata->status = 4;
      $report_failed = plus_getpageurl($reportpageurl, $formdata);

      $html .= '
            <div class="row homeworkstatus">
              <div class="col-md-6 grid-margin stretch-card">
                <div class="card tale-bg filterform">
                  <div class="card-people mt-auto">
                    <!--<img src="https://plus.fivestudents.com/wp-content/plugins/el-dashboard/public/images/dashboard/people.svg" alt="people">-->
                    <form id="dashboardfilter"  class="forms-sample blueform" autocomplete="off">
                      <div class="form-group">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">'.plus_get_string("level", "form").'</span>
                          </div>
                          <select name="categoryid" class="form-control" id="categoryid" required="required">'.$catcourses.'</select>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">'.plus_get_string("group", "form").'</span>
                          </div>
                          <select name="groupid" class="form-control" id="groupid" required="required">'.$groups.'</select>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">'.plus_get_string("homework", "form").'</span>
                          </div>
                          <select name="homeworkid" class="form-control" id="homeworkid" required="required">'.$homeworks.'</select>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">'.plus_get_string("attempt", "form").'</span>
                          </div>
                          <select name="attempt" class="form-control" id="attempt" required="required">
                            <option value="1" '.(($formdata->attempt==1)?'selected':'').' >'.plus_get_string("firstattempt", "form").'</option>
                            <option value="2" '.(($formdata->attempt==2)?'selected':'').' >'.plus_get_string("bestattempt", "form").'</option>
                          </select>
                        </div>
                      </div>
                      <input type="hidden" name="filtertype" value="'.$formdata->filtertype.'"/>
                      <input type="hidden" name="fromdate" value="'.$formdata->fromdate.'"/>
                      <input type="hidden" name="todate" value="'.$formdata->todate.'"/>
                      <div class="input-group mb-4">
                          <button type="submit" class="form-control btn btn-blue">'.plus_get_string("search", "form").'</button>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
              <div class="col-md-6 grid-margin text-center transparent homeworkdata">
                <div class="row">
                  <div class="col-md-6 mb-4 mt-4 stretch-card1 transparent">
                    <div>'.plus_get_string("strnotcompleted", "dashboard").'</div>
                    <a href="'.$report_incompleted.'">
                      <div class="card incomplete">
                        <div class="card-body">
                          <p class="fs-40 mb-2 count">'.$APIRES->data->homeworkkpi->notcompleted->count.'</p>
                          <p class="fs-40 mb-2 percent">'.$APIRES->data->homeworkkpi->notcompleted->percent.'%</p>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class="col-md-6 mb-4 mt-4 stretch-card1 transparent">
                    <div>'.plus_get_string("strnotstarted", "dashboard").'</div>
                    <a href="'.$report_unstarted.'">
                      <div class="card notstarted">
                        <div class="card-body">
                          <p class="fs-40 mb-2 count">'.$APIRES->data->homeworkkpi->notstarted->count.'</p>
                          <p class="fs-40 mb-2 percent">'.$APIRES->data->homeworkkpi->notstarted->percent.'%</p>
                        </div>
                      </a>
                    </div>
                  </div>
                  <div class="col-md-6 mb-4 stretch-card1 transparent">
                    <div>'.plus_get_string("strcompleted", "dashboard").'</div>
                    <a href="'.$report_completed.'">
                      <div class="card completed">
                        <div class="card-body">
                          <p class="fs-40 mb-2 count">'.$APIRES->data->homeworkkpi->completed->count.'</p>
                          <p class="fs-40 mb-2 percent">'.$APIRES->data->homeworkkpi->completed->percent.'%</p>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class="col-md-6 mb-4 stretch-card1 transparent">
                    <div>'.plus_get_string("strlatecompleted", "dashboard").'</div>
                    <a href="'.$report_latecompleted.'">
                      <div class="card latecompleted">
                        <div class="card-body">
                          <p class="fs-40 mb-2 count">'.$APIRES->data->homeworkkpi->latecompleted->count.'</p>
                          <p class="fs-40 mb-2 percent">'.$APIRES->data->homeworkkpi->latecompleted->percent.'%</p>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class="col-md-6 mb-4 stretch-card1 transparent">
                    <div>'.plus_get_string("strpassed", "dashboard").'</div>
                    <a href="'.$report_passed.'">
                      <div class="card passed">
                        <div class="card-body">
                          <p class="fs-40 mb-2 count">'.$APIRES->data->homeworkkpi->passed->count.'</p>
                          <p class="fs-40 mb-2 percent">'.$APIRES->data->homeworkkpi->passed->percent.'%</p>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class="col-md-6 col-md-6 mb-4 stretch-card1 transparent">
                    <div>'.plus_get_string("strnotpassed", "dashboard").'</div>
                    <a href="'.$report_failed.'">
                      <div class="card notpassed">
                        <div class="card-body">
                          <p class="fs-40 mb-2 count">'.$APIRES->data->homeworkkpi->notpassed->count.'</p>
                          <p class="fs-40 mb-2 percent">'.$APIRES->data->homeworkkpi->notpassed->percent.'%</p>
                        </div>
                      </div>
                    </a>
                  </div>
                  <div class="col-md-3 mb-4 stretch-card1 transparent"></div>
                  <div class="col-md-6 col-md-6 mb-4 stretch-card1 transparent">
                    <div>'.plus_get_string("totaluserhomeworkcompleted", "form").'</div>
                      <div class="card lightblue">
                        <div class="card-body">
                          <br><p class="fs-40 mb-2 percent">'.$APIRES->data->homeworkkpi->totalquizcompleted.'</p><br>
                        </div>
                      </div>
                  </div>
                  <div class="col-md-3 mb-4 stretch-card1 transparent"></div>
                  
                  
                </div>
              </div>
            </div>
      ';
      $html .='<script>
  $(document).ready(function(){
    var selectedgrade = null;    
    var allgrades = '.json_encode($allgrades).';
    var allgroups = '.json_encode($allgroup).';
    $("#categoryid").change(function(){
      var newoptions  ="<option value=\"0\">'.plus_get_string("all", "site").'</option>";
      var gradeid = $(this).val();
      var selectedgrade = allgrades.find(x => x.categoryid === gradeid);
      console.log("selectedgrade-", selectedgrade);
      if(selectedgrade && Array.isArray(selectedgrade.allgroup)){
        allgroups = selectedgrade.allgroup;
        $.each( selectedgrade.allgroup, function( key, group ) {
          console.log("group- ", group);
          newoptions += \'<option value="\'+group.groupid+\'">\'+group.name+\'</option>\'
        });
      }
      console.log(newoptions);
      $("#groupid").html(newoptions);
    });
    $("#groupid").change(function(){
      var newoptions  ="<option value=\"0\">'.plus_get_string("all", "site").'</option>";
      var groupid = $(this).val();
      var selectedgroup = allgroups.find(x => x.groupid === groupid);
      console.log("selectedgroup-", selectedgroup);
      if(selectedgroup && Array.isArray(selectedgroup.homeworks)){
        $.each( selectedgroup.homeworks, function( key, homework ) {
          console.log("group- ", homework);
          newoptions += \'<option value="\'+homework.id+\'">\'+homework.name+\'</option>\'
        });
      }
      $("#homeworkid").html(newoptions);
    });
    $(".filtertype").click(function(){
      $(".filtertype").removeClass("active");
      $(this).addClass("active");
      var filtertype = $(this).data("filtertype");
      console.log("filtertype- ", filtertype);
      $("input[name=\'filtertype\']").val(filtertype);
      if(filtertype != 4){
        $("#dashboardfilter").submit();
      }
    });
    $("#customdatefiler .datefilter").change(function(){
      var datetype = $(this).data("filterdate");
      var dateval = $(this).val();
      console.log("datetype- ", datetype);
      console.log("dateval- ", dateval);
      $("input[name=\'"+datetype+"\']").val(dateval);
    });
  })
  </script>';
    }
  // }


          
  echo $html;

  
}