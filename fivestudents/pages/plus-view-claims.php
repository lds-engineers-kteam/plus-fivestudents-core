<?php
function plus_view_claims(){
  global $wp,$API ;
  return plus_view_noaccess();
  if ( !is_user_logged_in() || !current_user_can('view_plusclaims')) {
    return plus_view_noaccess();
  }
  $current_user = wp_get_current_user();
  $searchreq = new stdClass();
  $searchreq->id = plus_get_request_parameter("id", "");
  $args = new stdClass();
  $eventstatus = array("none", "started", "cancelled", "completed", "past", "claim", "inprogress");
  $args->status = 5; 
  $MOODLE = new MoodleManager($current_user);

  $CLAIMEDAPIRES = $MOODLE->get("getCalendarEvents", null, $args);
  $args->status = 6; 
  $INPROCESSAPIRES = $MOODLE->get("getCalendarEvents", null, $args);
  $html= '';
  // $html .= '<pre>'.print_r($INPROCESSAPIRES, true).'</pre>';
  $html .= '
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  ';
  $html .=  '
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div>
<ul class="nav nav-tabs">
  <li><a class="nav-link active" data-toggle="tab" href="#claimedevents">'.plus_get_string("claimedevents", "form").'</a></li>
  <li><a class="nav-link " data-toggle="tab" href="#inprocessevents">'.plus_get_string("inprocessevents", "form").'</a></li>
</ul>

<div class="tab-content">
  <div id="claimedevents" class="tab-pane fade active show">
    <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>'.plus_get_string("user", "form").'</th>
                  <th>'.plus_get_string("group", "form").'</th>
                  <th>'.plus_get_string("matter", "form").'</th>
                  <th>'.plus_get_string("startdate", "form").'</th>
                  <th>'.plus_get_string("enddate", "form").'</th>
                  <th>'.plus_get_string("completedquiz", "form").'</th>
                  <th>'.plus_get_string("passedquiz", "form").'</th>
                  <th>'.plus_get_string("status", "form").'</th>
                  <th>'.plus_get_string("message", "form").'</th>
                  <th></th>
                  <th></th>
                </tr>';
    if(is_array($CLAIMEDAPIRES->data->events) && sizeof($CLAIMEDAPIRES->data->events)> 0){
      foreach ($CLAIMEDAPIRES->data->events as $event ) {
        $html .= '<tr>
                    <td>'.$event->teacher.'</td>
                    <td>'.$event->groupname.'</td>
                    <td>'.$event->coursename.'</td>
                    <td>'.plus_dateToFrench($event->timestart).'</td>
                    <td>'.plus_dateToFrench($event->timeend).'</td>
                    <td>'.$event->totalcompleted.'</td>
                    <td>'.$event->totalpassed.'</td>
                    <td>'. ($eventstatus[$event->status]?:$event->status) .'</td>
                    <td>'.$event->statusmessage.'</td>
                    <td> <button type="button" eventlogs data-id="'.$event->id.'" data-logs="'.base64_encode($event->logs).'" >'.plus_get_string("eventlogs", "form").'</button></td>
                    <td> <button type="button" eventupdate data-id="'.$event->id.'" >'.plus_get_string("eventupdate", "form").'</button></td>
                  </tr>';
      }
    } else {
      $html .=    '<tr><td colspan="11" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
    }
      $html .= '</table>
              </div>
  </div>
  <div id="inprocessevents" class="tab-pane fade">
    <div class="table-responsive">
              <table class="table">
                <tr>
                  <th>'.plus_get_string("user", "form").'</th>
                  <th>'.plus_get_string("group", "form").'</th>
                  <th>'.plus_get_string("matter", "form").'</th>
                  <th>'.plus_get_string("startdate", "form").'</th>
                  <th>'.plus_get_string("enddate", "form").'</th>
                  <th>'.plus_get_string("completedquiz", "form").'</th>
                  <th>'.plus_get_string("passedquiz", "form").'</th>
                  <th>'.plus_get_string("status", "form").'</th>
                  <th>'.plus_get_string("message", "form").'</th>
                  <th></th>
                  <th></th>
                </tr>';
    if(is_array($INPROCESSAPIRES->data->events) && sizeof($INPROCESSAPIRES->data->events)> 0){
      foreach ($INPROCESSAPIRES->data->events as $event ) {
        $html .= '<tr>
                    <td>'.$event->teacher.'</td>
                    <td>'.$event->groupname.'</td>
                    <td>'.$event->coursename.'</td>
                    <td>'.plus_dateToFrench($event->timestart).'</td>
                    <td>'.plus_dateToFrench($event->timeend).'</td>
                    <td>'.$event->totalcompleted.'</td>
                    <td>'.$event->totalpassed.'</td>
                    <td>'. ($eventstatus[$event->status]?:$event->status) .'</td>
                    <td>'.$event->statusmessage.'</td>
                    <td>'.$event->id.' <button type="button" eventlogs data-id="'.$event->id.'" data-logs="'.base64_encode(json_encode($event->logs)).'" >'.plus_get_string("eventlogs", "form").'</button></td>
                    <td> <button type="button" eventupdate data-id="'.$event->id.'" >'.plus_get_string("eventupdate", "form").'</button></td>
                  </tr>';
      }
    } else {
      $html .=    '<tr><td colspan="11" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
    }
      $html .= '</table>
              </div>
  </div>
  <div id="menu2" class="tab-pane fade">
    <h3>Menu 2</h3>
    <p>Some content in menu 2.</p>
  </div>
</div>

            </div>

            ';
  $html.= '
          </div>
        </div>
      </div>
    </div>
  ';
  $html .= '<script>
  $(document).on("click", "[eventupdate]", function(){
    var eventid = $(this).data("id");
    console.log("Update", eventid);
    var eventcomment_label = $("<label>").text("'.plus_get_string("eventcomment", "form").':");
    var eventcomment_input = $("<textarea>").attr("cols", "10").attr("rows", "10").attr("id", "message");
    var dialog = $("<div>").append(
        eventcomment_label,
        eventcomment_input,
    ).dialog({
        modal: true,
        width: 400,
        buttons: {
            "Save": function() {
                var that = this;
                var reqargs = {
                    "eventid": eventid,
                    "status": 6,
                    "message":$("#message").val()
                };
                if(reqargs.message == ""){
                    alert("Please add missing info");
                    return;
                }
                $.ajax({
                    "url": "/api/index.php",
                    "method": "POST",
                    "timeout": 0,
                    "headers": {
                    "Content-Type": "application/json",
                    },
                    "data": JSON.stringify({
                    "wsfunction": "updateEventStatus",
                    "wsargs": reqargs
                    }),
                }).done(function (response) {
                    $(that).dialog("close");
                    location.reload();
                });
            },
            "Close": function() {
                $(this).dialog("close");
            }
        }
    });
  });
  $(document).on("click", "[eventlogs]", function(){
    var eventid = $(this).data("id");
    var logs = window.atob($(this).data("logs"));
    if(logs){
      logs = JSON.parse(logs);
    } else {
      logs = [];
    }
    var logdata = "";
    if(Array.isArray(logs) && logs.length > 0){
      var logdatatable = `<div class="table-responsive"><table class="table table-stripped">`;
      logdatatable += `<tr><th>'.plus_get_string("user", "form").'</th><th>'.plus_get_string("message", "form").'</td></tr>`;
      logs.forEach((log) => {
        logdatatable += `<tr><td>${log.firstname} ${log.lastname}</td><td>${log.message}</td></tr>`;
      });
      logdatatable += `</table></div>`;
      logdata = $(logdatatable);
    } else {
      logdata = $("<label>").text("'.plus_get_string("norecordfound", "form").':");
    }
    console.log("logs:- ", logs);
    console.log("logdata:- ", logdata);
    var dialog = $("<div>").append(
        logdata
    ).dialog({
        modal: true,
        width: 400,
        buttons: {
            "Close": function() {
                $(this).dialog("close");
            }
        }
    });
  });
  </script>';
  echo $html;
}

