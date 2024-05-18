<?php
function plus_view_verificationcode()
{
  global $CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  $searchreq->institutionid = plus_get_request_parameter("id", 0);
  $html = '';
  $APIRES = $MOODLE->get("getDeviceKeys", null, $searchreq);
  $totalkeys = 0;
  $devices = array();
  if($APIRES->code == 200){
    $totalkeys = $APIRES->data->allowedkeys;
    $devices = $APIRES->data->deviceslist;
  // $html .= "<pre>".print_r($APIRES, true)."</pre>";
  }

  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">'.plus_get_string("devices", "site").' ('.$totalkeys.')</h4>';
  $html .=        '<div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("devicekey", "form").'</th>
                          <th>'.plus_get_string("status", "form").'</th>
                          <th>'.plus_get_string("devicetoken", "form").'</th>
                          <th>'.plus_get_string("devicename", "form").'</th>
                        </tr>
                      </thead>
                      <tbody>';
                      foreach ($devices as $key => $device) {
                         $html .=  '<tr>
                              <td class="py-1">'.$device->devicekey.'</td>
                              <td class="py-1">'.($device->devicetoken?"activated":"Not Activated").'</td>
                              <td class="py-1">'.$device->devicetoken.'</td>
                              <td class="py-1">'.$device->devicename.'</td>
                              </tr>';
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
          </div>';
  return $html;
}

