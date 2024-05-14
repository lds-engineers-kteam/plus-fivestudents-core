<?php
function plus_view_eventmanagement(){
  global $wp,$API,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $MOODLESESSION = wp_get_moodle_session();
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  $searchreq->id = plus_get_request_parameter("id", "");
  $searchreq->eventid = plus_get_request_parameter("eventid", 0);
  $args = new stdClass();
  $eventstatus = array(
    plus_get_string("status_planned", "calendar"), 
    plus_get_string("status_started", "calendar"), 
    plus_get_string("status_cancelled", "calendar"), 
    plus_get_string("status_completed", "calendar"), 
    plus_get_string("status_planned", "calendar"), 
    plus_get_string("status_claim", "calendar"), 
    plus_get_string("status_inprogress", "calendar")
  );
  $active = plus_get_request_parameter("active", '');
  
  if(current_user_can('view_plusclaimedevent')){
    $args->status = 5; 
    $CLAIMEDAPIRES = $MOODLE->get("getCalendarEvents", null, $args);
    if(empty($active)){ $active = 'claimedevents'; }
  }
  if(current_user_can('view_pluscencllededevent')){
    $args->status = 2; 
    $CANCELLEDAPIRES = $MOODLE->get("getCalendarEvents", null, $args);
    if(empty($active)){ $active = 'cancelledevents'; }
  }
  if(current_user_can('view_plusinprogressedevent')){
    $args->status = 6; 
    $INPROCESSAPIRES = $MOODLE->get("getCalendarEvents", null, $args);
    if(empty($active)){ $active = 'inprogressevents'; }
  }
  $html= '';
  // $html .= '<pre>'.print_r($active, true).'</pre>';
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
                '.(current_user_can('view_pluscencllededevent')?'<li><a class="nav-link '.($active=='cancelledevents'?'active':'').'" data-toggle="tab" href="#cencllededevent">'.plus_get_string("cencllededevent", "form").'</a></li>':'').'
                '.(current_user_can('view_plusclaimedevent')?'<li><a class="nav-link '.($active=='claimedevents'?'active':'').'" data-toggle="tab" href="#claimedevents">'.plus_get_string("claimedevents", "form").'</a></li>':'').'
                '.(current_user_can('view_plusinprogressedevent')?'<li><a class="nav-link '.($active=='inprogressevents'?'active':'').'" data-toggle="tab" href="#inprocessevents">'.(current_user_can('view_plusclaimedevent')?plus_get_string("inprocessevents", "form"):plus_get_string("claimedevents", "form")).'</a></li>':'').'
              </ul>
              <div class="tab-content">';
  if(current_user_can('view_pluscencllededevent')){
    $html .=  '
    <div id="cencllededevent" class="tab-pane fade '.($active=='cancelledevents'?'active show':'').'">
      <div class="table-responsive">
                <table class="table">
                  <tr>
                    <th>'.plus_get_string("user", "form").'</th>
                    <th>'.plus_get_string("group", "form").'</th>
                    <th>'.plus_get_string("matter", "form").'</th>
                    <th>'.plus_get_string("startdate", "form").'</th>
                    <th>'.plus_get_string("enddate", "form").'</th>
                    '.(current_user_can('plus_eventotherviewcompletions')?'
                      <th>'.plus_get_string("completedquiz", "form").'</th>
                      <th>'.plus_get_string("passedquiz", "form").'</th>
                      <th>'.plus_get_string("completedquizday", "form").'</th>
                      <th>'.plus_get_string("passedquizday", "form").'</th>
                      ':'').'
                    <th></th>
                    '.(current_user_can('plus_eventothereditdate')?'<th></th>':'').'
                    
                  </tr>';
      if(is_array($CANCELLEDAPIRES->data->events) && sizeof($CANCELLEDAPIRES->data->events)> 0){
        foreach ($CANCELLEDAPIRES->data->events as $event ) {
          $event->logs = plus_translatelogs($event->logs);
          $html .= '<tr class="'.($searchreq->eventid == $event->id?'selected':'').'">
                      <td>'.$event->teacher.'</td>
                      <td>'.$event->groupname.'</td>
                      <td>'.$event->coursename.'</td>
                      <td>'.plus_dateToFrench($event->timestart).'</td>
                      <td>'.plus_dateToFrench($event->timeend).'</td>
                      '.(current_user_can('plus_eventotherviewcompletions')?'
                        <td>'.$event->totalcompleted.'</td>
                        <td>'.$event->totalpassed.'</td>
                        <td>'.$event->totalcompletedday.'</td>
                        <td>'.$event->totalpassedday.'</td>
                      ':'').'
                      <td><button type="button" eventlogs data-id="'.$event->id.'" data-status="'.$event->status.'" data-logs="'.base64_encode(json_encode($event->logs)).'" data-events="'.base64_encode(json_encode($event)).'" >'.plus_get_string("eventlogs", "form").'</button></td>
                    '.(current_user_can('plus_eventothereditdate')?'<td><button type="button" eventreschedule data-timestart="'.date("Y-m-d\TH:i:s", $event->timestart).'" data-timeend="'.date("Y-m-d\TH:i:s", $event->timeend).'" data-id="'.$event->id.'" data-status="'.$event->status.'" data-logs="'.base64_encode(json_encode($event->logs)).'" data-events="'.base64_encode(json_encode($event)).'" >'.plus_get_string("eventreschedule", "form").'</button></td>':'').'
                    </tr>';
        }
      } else {
        $html .=    '<tr><td colspan="10" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
      }
        $html .= '</table>
                </div>
    </div>';
  }
  if(current_user_can('view_plusclaimedevent')){
    $html.= '
    <div id="claimedevents" class="tab-pane fade '.($active=='claimedevents'?'active show':'').'">
      <div class="table-responsive">
                <table class="table">
                  <tr>
                    <th>'.plus_get_string("user", "form").'</th>
                    <th>'.plus_get_string("group", "form").'</th>
                    <th>'.plus_get_string("matter", "form").'</th>
                    <th>'.plus_get_string("startdate", "form").'</th>
                    <th>'.plus_get_string("enddate", "form").'</th>
                    '.(current_user_can('plus_eventotherviewcompletions')?'
                      <th>'.plus_get_string("completedquiz", "form").'</th>
                      <th>'.plus_get_string("passedquiz", "form").'</th>
                      <th>'.plus_get_string("completedquizday", "form").'</th>
                      <th>'.plus_get_string("passedquizday", "form").'</th>
                      ':'').'
                    <th></th>
                    <th></th>
                  </tr>';
      if(is_array($CLAIMEDAPIRES->data->events) && sizeof($CLAIMEDAPIRES->data->events)> 0){
        foreach ($CLAIMEDAPIRES->data->events as $event ) {
          $event->logs = plus_translatelogs($event->logs);
          $html .= '<tr class="'.($searchreq->eventid == $event->id?'selected':'').'">
                      <td>'.$event->teacher.'</td>
                      <td>'.$event->groupname.'</td>
                      <td>'.$event->coursename.'</td>
                      <td>'.plus_dateToFrench($event->timestart).'</td>
                      <td>'.plus_dateToFrench($event->timeend).'</td>
                      '.(current_user_can('plus_eventotherviewcompletions')?'
                        <td>'.$event->totalcompleted.'</td>
                        <td>'.$event->totalpassed.'</td>
                        <td>'.$event->totalcompletedday.'</td>
                        <td>'.$event->totalpassedday.'</td>
                      ':'').'
                      <td><button type="button" eventlogs data-id="'.$event->id.'" data-status="'.$event->status.'" data-logs="'.base64_encode(json_encode($event->logs)).'" data-events="'.base64_encode(json_encode($event)).'" >'.plus_get_string("eventlogs", "form").'</button></td>
                      <td><button type="button" eventupdate data-id="'.$event->id.'" data-status="'.$event->status.'" data-logs="'.base64_encode(json_encode($event->logs)).'" data-events="'.base64_encode(json_encode($event)).'" >'.plus_get_string("eventupdate", "form").'</button></td>
                    </tr>';
        }
      } else {
        $html .=    '<tr><td colspan="10" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
      }
        $html .= '</table>
                </div>
    </div>';
  }
  if(current_user_can('view_plusinprogressedevent')){
    $html .=  '
    <div id="inprocessevents" class="tab-pane fade '.($active=='inprogressevents'?'active show':'').'">
      <div class="table-responsive">
                <table class="table">
                  <tr>
                    <th>'.plus_get_string("user", "form").'</th>
                    <th>'.plus_get_string("group", "form").'</th>
                    <th>'.plus_get_string("matter", "form").'</th>
                    <th>'.plus_get_string("startdate", "form").'</th>
                    <th>'.plus_get_string("enddate", "form").'</th>
                    '.(current_user_can('plus_eventotherviewcompletions')?'
                      <th>'.plus_get_string("completedquiz", "form").'</th>
                      <th>'.plus_get_string("passedquiz", "form").'</th>
                      <th>'.plus_get_string("completedquizday", "form").'</th>
                      <th>'.plus_get_string("passedquizday", "form").'</th>
                      ':'').'
                    <th></th>
                    <th></th>
                  </tr>';
      if(is_array($INPROCESSAPIRES->data->events) && sizeof($INPROCESSAPIRES->data->events)> 0){
        foreach ($INPROCESSAPIRES->data->events as $event ) {
          $event->logs = plus_translatelogs($event->logs);
          $html .= '<tr class="'.($searchreq->eventid == $event->id?'selected':'').'">
                      <td>'.$event->teacher.'</td>
                      <td>'.$event->groupname.'</td>
                      <td>'.$event->coursename.'</td>
                      <td>'.plus_dateToFrench($event->timestart).'</td>
                      <td>'.plus_dateToFrench($event->timeend).'</td>
                      '.(current_user_can('plus_eventotherviewcompletions')?'
                        <td>'.$event->totalcompleted.'</td>
                        <td>'.$event->totalpassed.'</td>
                        <td>'.$event->totalcompletedday.'</td>
                        <td>'.$event->totalpassedday.'</td>
                      ':'').'
                      <td><button type="button" eventlogs data-id="'.$event->id.'" data-timestart="'.date("Y-m-d\TH:i",$event->timestart).'" data-timeend="'.date("Y-m-d\TH:i",$event->timeend).'" data-status="'.$event->status.'" data-logs="'.base64_encode(json_encode($event->logs)).'" data-events="'.base64_encode(json_encode($event)).'" >'.plus_get_string("eventlogs", "form").'</button></td>
                      <td><button type="button" eventupdate data-id="'.$event->id.'" data-status="'.$event->status.'" data-logs="'.base64_encode(json_encode($event->logs)).'" data-events="'.base64_encode(json_encode($event)).'" data-prefill="'.(current_user_can('view_plusclaimedevent')?1:0).'" >'.plus_get_string("eventupdate", "form").'</button></td>
                    </tr>';
        }
      } else {
        $html .=    '<tr><td colspan="10" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
      }
        $html .= '</table>
                </div>
    </div>';
  }
  $html .=  '
</div>

            </div>

            ';
  $html.= '
          </div>
        </div>
      </div>
    </div>';
  $statuspermission = 0;
  if(current_user_can('plus_eventothercancel')){$statuspermission++;}
  if(current_user_can('plus_eventotherinprogress')){$statuspermission++;}
  if(current_user_can('plus_eventothercomplete')){$statuspermission++;}
  $html .= '<script>
  var eventstatus = '.json_encode($eventstatus).';
  $(document).on("click", "[eventupdate]", function(){
    var eventid = $(this).data("id");
    var eventstatus = $(this).data("status");
    var prefill = $(this).data("prefill");
    var logs = $(this).data("logs");
    var events = $(this).data("events");
    if(logs){
      logs = window.atob(logs);
    }
    if(events){
      events = window.atob(events);
      events = JSON.parse(events);
    }
    if(logs){
      logs = JSON.parse(logs);
    } else {
      logs = [];
    }

    console.log("Update", eventid);
    console.log("prefill", prefill);
    console.log("events", events);
    var eventcomment_label = $("<label>").text("'.plus_get_string("eventcomment", "form").':");
    var eventcomment_input = $("<textarea>").attr("cols", "10").attr("rows", "10").attr("id", "message"+eventid);
    if(prefill == 1){
      console.log("events.statusmessage", events.statusmessage);
      eventcomment_input = eventcomment_input.val(events.statusmessage);
    }
    '.(($statuspermission >1)?'
      var eventstatus_select = $(`<select id="eventstatus${eventid}">'.(current_user_can('plus_eventothercancel')?'<option value="2">'.plus_get_string("cancel", "form").'</option>':'').(current_user_can('plus_eventotherinprogress')?'<option value="6">'.plus_get_string("inprogress", "form").'</option>':'').(current_user_can('plus_eventothercomplete')?'<option value="3">'.plus_get_string("complete", "form").'</option>':'').'</select>`);
      var eventstatus_label = $("<label>").text("'.plus_get_string("status", "form").':");
      ':'
      var eventstatus_label = "";
      var eventstatus_select = $(`<input id="eventstatus${eventid}" '.(current_user_can('plus_eventotherinprogress')?' type="hidden" value="6" ':'').' />`);
      ').'
    
    var dialogbuttons = {};
    dialogbuttons["'.plus_get_string("save", "form").'"]=function() {
      var that = this;
      var reqargs = {
        "type": "event",
        "viewed": 0,
        "title": "",
        "current_usid":555,
        "teacherid": events.teacherid,
        "schoolyear": events.schoolyear,
        "institutionid":events.institutionid,
        "eventid": eventid,
        "status":$(`#eventstatus${eventid}`).val(),
        "message":$("#message"+eventid).val()
      };
      console.log("reqargs- ", reqargs);
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
    };
    dialogbuttons["'.plus_get_string("close", "form").'"]=function() {
      $(this).dialog("close");
    };

    var dialog = $("<div>").append(
        eventcomment_label,
        eventcomment_input,
        eventstatus_label,
        eventstatus_select,
    ).dialog({
        modal: true,
        width: 400,
        buttons: dialogbuttons
    });
  });
  $(document).on("click", "[eventreschedule]", function(){
    var eventid = $(this).data("id");
    var eventstatus = $(this).data("status");
    var timestart = $(this).data("timestart");
    var timeend = $(this).data("timeend");
    console.log("Update", eventid);
    '.(current_user_can('plus_eventothereditdate')?'
      var eventcomment_label = $("<label>").text("'.plus_get_string("eventcomment", "form").':");
      var eventcomment_input = $("<textarea>").attr("cols", "10").attr("rows", "10").attr("id", "message"+eventid);
      var label = $("<label>").addClass("col-sm-12").html("<h5>Edit event date:</h5>");
      var dialog = $("<div class= row > ").append(
        eventcomment_label,
        eventcomment_input,
        $("<h6>").addClass("col-sm-4").text("'.plus_get_string("starttime", "calendar").': "),
        $("<input>").addClass("col-sm-8").attr("type", "datetime-local").attr("id", `start_time${eventid}`).attr("value",timestart),
        $("<h6>").addClass("col-sm-4").text("'.plus_get_string("endtime", "calendar").': "),
        $("<input>").addClass("col-sm-8").attr("type", "datetime-local").attr("id", `end_time${eventid}`).attr("value",timeend),
        ).dialog({
            modal: true,
            width: 600,
            buttons: {
                "'.plus_get_string("save", "form").'": function() {
                    var stime = $(`#start_time${eventid}`).val();
                    var etime = $(`#end_time${eventid}`).val();
                    var reqargs = {
                        "eventid": eventid,
                        "srttime": stime,
                        "endtime": etime,
                        "status": 0,
                        "message":$("#message"+eventid).val(),
                    };
                    var that = this;
                    console.log("tetttt",reqargs);
                    $.ajax({
                        "url": "/api/index.php",
                        "method": "POST",
                        "timeout": 0,
                        "headers": {"Content-Type": "application/json",},
                        "data": JSON.stringify({
                        "wsfunction": "updateEventStatus",
                        "wsargs": reqargs
                        }),
                    }).done(function (response) {
                        $(that).dialog("close");
                        location.reload();
                    });
                },
                "'.plus_get_string("close", "form").'": function() {
                    $(this).dialog("close");
                }
            }
        });

    ':'').'
  });
  $(document).on("click", "[eventlogs]", function(){
    var eventid = $(this).data("id");
    var logs = $(this).data("logs");
    var events = $(this).data("events");
    if(logs){
      logs = window.atob(logs);
    }
    if(events){
      events = window.atob(events);
      events = JSON.parse(events);
    }
    if(logs){
      logs = JSON.parse(logs);
    } else {
      logs = [];
    }
    console.log("eventstatus", eventstatus);
    console.log("events", events);
    var logdata = "";
    if(Array.isArray(logs) && logs.length > 0){
      var logdatatable = `<div class="table-responsive"><table class="table table-stripped">`;
      logdatatable += `<tr><th>'.plus_get_string("startdate", "form").'</th><th>'.plus_get_string("enddate", "form").'</th><th>'.plus_get_string("status", "form").'</th><th>'.plus_get_string("message", "form").'</th><th>'.plus_get_string("modifiedby", "form").'</th><th>'.plus_get_string("modifieddate", "form").'</th></tr>`;
      logs.forEach((log) => {
        console.log("log", log);
        var statusm = eventstatus[log.status]?eventstatus[log.status]:log.status;
        var olddata = JSON.parse(log.olddata);
        console.log("olddata", olddata);
        const timestart = olddata.timestart_str;
        const timeend = olddata.timeend_str;
        const modifieddate = log.updatedtime_str;
        logdatatable += `<tr> <td>${timestart}</td> <td>${timeend}</td> <td>${statusm}</td> <td>${log.message}</td> <td>${log.firstname} ${log.lastname}</td> <td>${modifieddate}</td></tr>`;
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
        width: "90%",
        buttons: {
            "'.plus_get_string("close", "form").'": function() {
                $(this).dialog("close");
            }
        }
    });
  });
  </script>';
  return $html;
}