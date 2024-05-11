<?php
function main_panel(){
  global $CFG;
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
  // // print_r($MOODLE);
  // echo print_r($APIRES, true);
  // echo "</pre>";
  // die;

  $html =  '<div class="row"></div>';
  $html =  '<div class="row">';
  $html .=  '<div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Dashboard</h3>
                </div>';
  $html .=  '   <div class="col-12 col-xl-4">
                  <div class="justify-content-end d-flex">
                    <div class="btn-group btn-group-responsive" role="group" aria-label="Basic example">
                      <button type="button" data-filtertype="1" class="btn btn-outline-secondary filtertype">today</button>
                      <button type="button" data-filtertype="2" class="btn btn-outline-secondary filtertype">yesterday</button>
                      <button type="button" data-filtertype="3" class="btn btn-outline-secondary filtertype">thismonth</button>
                      <button type="button" data-toggle="collapse" data-target="#customdatefiler" aria-expanded="" data-filtertype="4" class="btn btn-outline-secondary filtertype">tochoose</button>
                    </div>                 
                  </div>
                  <div id="customdatefiler" class="'.($formdata->filtertype != 4?'collapse':'').' mt-4" aria-expanded="'.($formdata->filtertype != 4?'false':'true').'" '.($formdata->filtertype != 4?'style="height: 0px";':'').'>
                    <div class="form-group">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">from</span>
                        </div>
                        <input type="date" class="form-control datefilter" value="'.$formdata->fromdate.'" data-filterdate="fromdate"/>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">to</span>
                        </div>
                        <input type="date" class="form-control datefilter" value="'.  $formdata->todate.'" data-filterdate="todate"/>
                      </div>
                    </div>
                  </div>
                </div>';

      $html .=  '
              </div>
            </div>
          </div>';


          
  echo $html;

  
}