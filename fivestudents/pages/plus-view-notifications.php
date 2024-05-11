<?php
function plus_view_notifications(){
  global $wp, $MOODLESESSION;
  $current_user = $MOODLESESSION->INSTITUTION->member->userid;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->teachid = $current_user;
  $formdata->id = plus_get_request_parameter("id", 0);
  $APIRESnotify = $MOODLE->get("getInstituteNotification", null, $formdata);

  $html ='';
  // $html .='<pre>'.print_r($APIRESnotify, true).'</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("title", "notification").'</h4>
                  <div class="table-responsive">
                    <table class="table table-striped plus_local_datatable" id="notifications">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("slno", "notification").'</th>
                          '.(current_user_can('plus_notification_viewallcolumns')?'
                            <th>'.plus_get_string("schoolname", "notification").'</th>
                            <th>'.plus_get_string("teachername", "notification").'</th>
                            ':'').'
                          <th>'.plus_get_string("eventdate", "notification").'</th>
                          <th>'.plus_get_string("status", "notification").'</th>
                          <th>'.plus_get_string("comment", "notification").'</th>
                          <th>'.plus_get_string("lastupdated", "notification").'</th>
                          <th>'.plus_get_string("action", "notification").'</th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRESnotify) && is_array($APIRESnotify->data->notifications)){
                $counter = 0;
                foreach ($APIRESnotify->data->notifications as $key => $notification) {
                  $counter++;
                  $html .=  '<tr '.($formdata->id == $notification->id?'class="selected"':'').'>
                              <td class="py-1">'.$counter.'</td>
                              '.(current_user_can('plus_notification_viewallcolumns')?'
                                <td class="py-1">'.$notification->schoolname.'</td>
                                <td class="py-1">'.$notification->teachername.'</td>
                                ':'').'
                              <td class="py-1">'.plus_dateToFrench($notification->timestart).'</td>
                              <td class="py-1">'.plus_get_string("status_{$notification->status}", "calendar").'</td>
                              <td class="py-1">'.$notification->comment.'</td>
                              <td class="py-1">'.plus_dateToFrench($notification->lastupdated).'</td>
                              <td class="">'.(current_user_can('plus_notification_eventview')?'<a href="/event-management/?eventid='.$notification->eventid.'&active='.$notification->action.'events"> '.plus_get_string("view", "notification").'</a>':'').'</td>
                              </tr>';
                }
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
  echo $html;
}