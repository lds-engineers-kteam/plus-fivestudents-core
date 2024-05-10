<?php
function plus_view_calendar(){
  global $wp, $MOODLESESSION;
  if ( !is_user_logged_in() || !current_user_can('plus_viewcalendar') || ( $MOODLESESSION->INSTITUTION && $MOODLESESSION->INSTITUTION->disablecalendar == 1)) {
    return plus_view_noaccess();
  }
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $formdata = new stdClass();
  $formdata->institutionid = plus_get_request_parameter("institutionid", 0);
  $formdata->teacherid = plus_get_request_parameter("teacherid", 0);
  $formdata->eventfor = plus_get_request_parameter("eventfor", 0);

  $current_user = wp_get_current_user();
  $current_user = wp_get_current_user();
  $searchreq = new stdClass();
  $searchreq->id = plus_get_request_parameter("id", "");
  $html ="";
  $html .='<pre>'.date("d F Y h:i:s A").'</pre>';
  $canadd = false;
  if(current_user_can('plus_editevents')){
      $canadd = true;
  }
  $institutions = array();
  $selectedinstitution = null;
  $selectedteacher = null;
  if(current_user_can('plus_viewteachersevent')){
    $formdata->eventfor = plus_get_request_parameter("eventfor", 1);
      $institutionData = $MOODLE->get("institutionWithTeacher", null,array());
      if($institutionData && !empty($institutionData->data)){
        if(is_array($institutionData->data)){
          $institutions = $institutionData->data;
        } else {
          $institutions = array($institutionData->data);
        }
      }
      $institutionoptions = '<option value="0" >'.plus_get_string("all", "site").'</option>';
      if(is_array($institutions)){
        $singleinstitution = sizeof($institutions)==1;
        foreach ($institutions as $key => $institution) {          
          if($singleinstitution){
            $formdata->institutionid = $institution->id;
          }
          $sel = '';
          if($formdata->institutionid == $institution->id){
            $selectedinstitution = $institution;
            $sel='selected';
          }
          $institutionoptions .= '<option '.$sel.' value="'.$institution->id.'" '.($formdata->institutionid == $institution->id?'selected':'').' >'.$institution->institution.' </option>';
        }
      }
      $teachersoption = '<option value="0" >'.plus_get_string("all", "site").' '.plus_get_string("events", "site").'</option>';
      if($selectedinstitution && !empty($selectedinstitution->teachers)){
        foreach ($selectedinstitution->teachers as $teacher) {
          $sel = '';
          if($formdata->teacherid == $teacher->id){
            $selectedteacher = $teacher;
            $sel='selected';
          }
          $teachersoption .= '<option value="'.$teacher->id.'" '.$sel.'>'.$teacher->firstname.' '.$teacher->lastname.' </option>';
        }
      }
      if(!current_user_can('plus_calendarmyevent')){
        $html .='<input type="hidden" id="teacherid" value="0"/>';
        $formdata->eventfor = 0;
      } else {
        $html .='<div class="form-group row filterarea">
                  <label class="col-sm-2 col-form-label">'.plus_get_string("filterarea", "form").'</label>
                  <div class="col-sm-10">
                  '.(current_user_can('plus_calendarmyevent')?'<label for="eventformyself" class="col-form-label text-right"><input  type="radio" value="1" '.($formdata->eventfor == 1?' checked="checked" ':'').' id="eventformyself" name="eventfor" /> '.plus_get_string("myevent", "calendar").'</label>&nbsp;&nbsp;&nbsp;':'').'
                    <label for="eventforteachers" class="col-form-label text-right"><input type="radio" value="0" '.($formdata->eventfor == 0?' checked="checked" ':'').' id="eventforteachers" name="eventfor" /> '.plus_get_string("teachers", "calendar").'</label>&nbsp;&nbsp;&nbsp;
                  </div>
                </div>';
      }
    $html .='
            <div class="form-group row">
              <label for="institutionid" class="col-sm-2 col-form-label">'.plus_get_string("school", "site").' *</label>
              <div class="col-sm-10">
                <select name="institutionid" required id="institutionid" class="form-control">
                    '.$institutionoptions.'
                </select>
              </div>
            </div>';
    if($formdata->eventfor){
    } else {
        $html .='<input type="hidden" id="teacherid" value="0"/>';
    }
    $html .='
            <div class="form-group row">
              <label for="teacherid" class="col-sm-2 col-form-label">'.plus_get_string("teacher", "form").' *</label>
              <div class="col-sm-10">
                <select name="teacherid" id="teacherid" required class="form-control">
                '.$teachersoption.'
                </select>
              </div>
            </div>';

    } else {
        $html .='<input type="hidden" id="institutionid" value="0"/>';
        $html .='<input type="hidden" id="teacherid" value="0"/>';
        $html .='<input type="hidden" name="eventfor" value="0"/>';
    }
    $currentlang = plus_getuserlang();
  $html .="
  <div id='calendar'></div>
  <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' />
  <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
  <script src='/wp-content/plugins/el-dashboard/public/vendors/calender_script/jquery-ui.js'></script>
  ".($currentlang == "EN"?"<script src='/wp-content/plugins/el-dashboard/public/vendors/calender_script/moment.js'></script>":"<script src='/wp-content/plugins/el-dashboard/public/vendors/calender_script/momentfr.js'></script>")."
  <script src='/wp-content/plugins/el-dashboard/public/vendors/calender_script/fullcalendar.min.js'></script>
  <script src='/wp-content/plugins/el-dashboard/public/vendors/calender_script/locale/fr.js'></script>
  <script>
  var institutions = ".json_encode($institutions).";
  var selectedinstitution = ".json_encode($selectedinstitution).";
    $(document).on('change', '#institutionid', function(){
      var institutionid = $(this).val();
      var url = window.location.origin+'/calendar?institutionid='+institutionid;
        window.location.href = url ;
      /*selectedinstitution = institutions.find(x => x.id === institutionid);
      var newoptions = '<option value=\"0\" >My Events</option>';
      if(selectedinstitution && Array.isArray(selectedinstitution.teachers)){
        var allteacher = selectedinstitution.teachers;
        $.each( allteacher, function( key, teacher ) {
          newoptions += `<option value='\${teacher.id}'>\${teacher.firstname} \${teacher.lastname}</option>`
        });
      }
      $('#teacherid').html(newoptions);
      $('#teacherid').trigger('change');
      */
    });
    $(document).on('change', '#teacherid', function(){
      var institutionid = $('#institutionid').val();
      var teacherid = $(this).val();
      var url = window.location.origin+'/calendar?institutionid='+institutionid+'&teacherid='+teacherid+'&eventfor=0';
        window.location.href = url ;
    });
    $(document).on('change', '[name=\"eventfor\"]', function(){
      var eventfor = $(this).val();
      var institutionid = $('#institutionid').val();
      var teacherid = $('#teacherid').val();
      var url = window.location.origin+'/calendar?institutionid='+institutionid+'&teacherid='+teacherid+'&eventfor='+eventfor;
        window.location.href = url ;
    });
    $(document).on('change', '#teacherid', function(){
        $('#calendar').fullCalendar('refetchEvents');
    });

  function myfunction(dtime){
     const d = new Date(dtime);
        var year = d.getFullYear();
        var monthint = String(d.getMonth()).padStart(2, '0');
        var mon = parseInt(monthint) + 1;
        var dat = String(d.getDate()).padStart(2, '0');
        var hr = String(d.getHours()).padStart(2, '0');
        var min = String(d.getMinutes()).padStart(2, '0');
        var eventdate = year+'-'+mon+'-'+dat+'T'+hr+':'+min;
        return eventdate;
    }
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay".($canadd?",addEventButton":"")."'
            },
            events: {
                url: '/api/calendarevents.php',
                type: 'GET',
                data: {
                    institutionid: $('#institutionid').val(),
                    teacherid: '$formdata->teacherid',
                    myevents: '$formdata->eventfor',
                }
            },
            defaultDate: moment().format('YYYY-MM-DD'),
            defaultView: 'agendaWeek',
            locale: '".strtolower($currentlang)."',
            slotMinTime: '08:00:00',
            slotMaxTime: '19:00:00',
            editable: false,
            eventLimit: true,
            customButtons: {
                ".($canadd?"addEventButton: {
                    text: '".plus_get_string("addevent", "calendar")."',
                    click: function() {
                        console.log('clicked add event');
                        var url = window.location.origin+'/add-event?returnto=calendar';
                        window.location.href = url ;
                    }
                },":"")."
            },
            eventClick: function(calEvent, jsEvent, view) {
                var prevent = calEvent['fulldata'];
                console.log('prevent- ', prevent);
                console.log('calEvent- ', calEvent);
                var sttime = prevent.timestart_sstr;
                var edtime = prevent.timeend_sstr;
                var dialog = \$('<div class= row event_list >').append(
                    \$('<h5>').addClass('col-sm-3').text('".plus_get_string("school", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.institution),
                    \$('<h5>').addClass('col-sm-3').text('".plus_get_string("user", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.teacher),
                    \$('<h5>').addClass('col-sm-3').text('".plus_get_string("group", 'form').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.groupname),
                    \$('<h5>').addClass('col-sm-3').text('".plus_get_string("subject", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.coursename),
                    \$('<h5>').addClass('col-sm-3').text('".plus_get_string("starttime", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'datetime-local').prop('disabled', true).attr('value',sttime),
                    \$('<h5>').addClass('col-sm-3').text('".plus_get_string("endtime", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'datetime-local').prop('disabled', true).attr('value',edtime),
                    \$('<h5>').addClass('col-sm-3').text('".plus_get_string("status", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.eventstatus),
                );
                var dialogbuttons = {};
                if(prevent.canedit){
                    dialogbuttons['".plus_get_string("edit", 'form')."']=function() {
                        var eventid = prevent.id;
                        console.log('Edit event',eventid);
                        $(this).dialog('close');
                        var label = $('<label>').addClass('col-sm-12').html(`<h5>".plus_get_string("editeventdate", 'calendar').":</h5>`);
                        var dialog = $('<div class= row > ').append(
                            label,
                            \$('<h6>').addClass('col-sm-4').text('".plus_get_string("starttime", 'calendar').": '),
                            \$('<input>').addClass('col-sm-8').attr('type', 'datetime-local').attr('id', `start_time${eventid}`).attr('value',sttime),
                            \$('<h6>').addClass('col-sm-4').text('".plus_get_string("endtime", 'calendar').": '),
                            \$('<input>').addClass('col-sm-8').attr('type', 'datetime-local').attr('id', `end_time${eventid}`).attr('value',edtime),
                        ).dialog({
                            modal: true,
                            width: 600,
                            buttons: {
                                '".plus_get_string("save", 'form')."': function() {
                                    var stime = $(`#start_time${eventid}`).val();
                                    var etime = $(`#end_time${eventid}`).val();
                                    var reqargs = {
                                        'eventid': prevent.id,
                                        'srttime': stime,
                                        'endtime': etime,
                                        'status': 0,
                                        'message':'update event',
                                    };
                                    var that = this;
                                    console.log('tetttt',reqargs);
                                    \$.ajax({
                                        'url': '/api/index.php',
                                        'method': 'POST',
                                        'timeout': 0,
                                        'headers': {'Content-Type': 'application/json',},
                                        'data': JSON.stringify({
                                        'wsfunction': 'editEventTime',
                                        'wsargs': reqargs
                                        }),
                                    }).done(function (response) {
                                        $(that).dialog('close');
                                        console.log('response :-- ',response);
                                        $('#calendar').fullCalendar('refetchEvents');
                                    });
                                },
                                '".plus_get_string("close", 'calendar')."': function() {
                                    $(this).dialog('close');
                                }
                            }
                        });
                        // var url = window.location.origin+'/add-event?returnto=calendar&id='+eventid;
                        // window.location.href = url ;
                    }
                }
                if(prevent.canstart){
                    dialogbuttons['".plus_get_string("start", 'calendar')."']=function() {
                        var reqargs = {
                            'eventid': prevent.id,
                            'status': 1,
                            'message':''
                        };
                        var that = this;
                        \$.ajax({
                            'url': '/api/index.php',
                            'method': 'POST',
                            'timeout': 0,
                            'headers': {
                            'Content-Type': 'application/json',
                            },
                            'data': JSON.stringify({
                            'wsfunction': 'updateEventStatus',
                            'wsargs': reqargs
                            }),
                        }).done(function (response) {
                            $('#calendar').fullCalendar('refetchEvents');
                            $(that).dialog('close');
                        });
                    };
                }
                if(prevent.canvisit){
                    dialogbuttons['".plus_get_string("visit", 'calendar')."']=function() {
                        var reqargs = {
                            'eventid': prevent.id,
                            'status': 1,
                            'message':''
                        };
                        var that = this;
                        \$.ajax({
                            'url': '/api/index.php',
                            'method': 'POST',
                            'timeout': 0,
                            'headers': {
                            'Content-Type': 'application/json',
                            },
                            'data': JSON.stringify({
                            'wsfunction': 'visitEventStatus',
                            'wsargs': reqargs
                            }),
                        }).done(function (response) {
                            $('#calendar').fullCalendar('refetchEvents');
                            $(that).dialog('close');
                        });
                    };
                }
                if(prevent.candelete){
                    dialogbuttons['".plus_get_string("delete", 'calendar')."']=function() {
                        $(this).dialog('close');
                        var eventid = prevent.id;
                        console.log('Edit event',eventid);
                        var label = $('<label>').addClass('col-sm-12').html('<h5>".plus_get_string("confirmdeletion", 'calendar').":</h5>');
                        var dialogbuttons1 = {};
                        dialogbuttons1[`".plus_get_string("deleteevent", 'calendar')."`] = function() {
                            var reqargs = {
                                'eventid': prevent.id,
                                'deleteseries': 0
                            };
                            var that = this;
                            \$.ajax({
                                'url': '/api/index.php',
                                'method': 'POST',
                                'timeout': 0,
                                'headers': {'Content-Type': 'application/json',},
                                'data': JSON.stringify({
                                'wsfunction': 'deleteEvents',
                                'wsargs': reqargs
                                }),
                            }).done(function (response) {
                                $(that).dialog('close');
                                console.log('response :-- ',response);
                                $('#calendar').fullCalendar('refetchEvents');
                            });
                        }
                        if(prevent.repeatevent == 1){
                            dialogbuttons1['".plus_get_string("deleteseries", 'calendar')."'] = function() {
                                var reqargs = {
                                    'eventid': prevent.id,
                                    'deleteseries': 1
                                };
                                var that = this;
                                \$.ajax({
                                    'url': '/api/index.php',
                                    'method': 'POST',
                                    'timeout': 0,
                                    'headers': {'Content-Type': 'application/json',},
                                    'data': JSON.stringify({
                                    'wsfunction': 'deleteEvents',
                                    'wsargs': reqargs
                                    }),
                                }).done(function (response) {
                                    $(that).dialog('close');
                                    console.log('response :-- ',response);
                                    $('#calendar').fullCalendar('refetchEvents');
                                });
                            }

                        }
                        dialogbuttons1['".plus_get_string("close", 'calendar')."'] = function() {
                            $(this).dialog('close');
                        }
                        var dialog = $('<div class= row > ').append(
                            label,
                        ).dialog({
                            modal: true,
                            width: 600,
                            buttons: dialogbuttons1
                        });
                    }
                }
                if(prevent.canstartsurvey){
                    dialogbuttons['".plus_get_string("btn_survey", 'calendar')."']=function() {
                        var reqargs = {
                            'eventid': prevent.id,
                            'message':''
                        };
                        var that = this;
                        \$.ajax({
                            'url': '/api/index.php',
                            'method': 'POST',
                            'timeout': 0,
                            'headers': {
                            'Content-Type': 'application/json',
                            },
                            'data': JSON.stringify({
                            'wsfunction': 'getSurveyList',
                            'wsargs': reqargs
                            }),
                        }).done(function (apiresresponse) {
                            var response = apiresresponse.data;
                            var surveylisthtml = `<div class='surveyattempt'> <table><tr><th>".plus_get_string("title", 'survey')."</th><th>".plus_get_string("action", 'survey')."</th></tr>`;
                                var havesurvey = true;
                                if(Array.isArray(response.surveys) && response.surveys.length >0){
                                    $.each( response.surveys, function( key, survey ) {
                                        if(Array.isArray(survey.questions) && survey.questions.length >0){
                                            havesurvey = true;
                                            surveylisthtml += `<tr><td>\${survey.name}</td><td><button data-id='\${survey.id}' data-eventid='\${prevent.id}' data-container='.surveyattempt' startsurvey>".plus_get_string("start", 'survey')."</button></td></tr>`;
                                        }
                                    });
                                } else {
                                    surveylisthtml += `<tr><td colspan='2'><div class='alert alert-warning'>".plus_get_string("norecordfound", 'form')."</div></td></tr>`;
                                }
                                surveylisthtml += `</table>`;

                            if(havesurvey){
                                $(that).dialog('close');
                                var dialog = $('<div>').addClass('dialogbody').append(
                                    surveylisthtml,
                                ).dialog({
                                    modal: true,
                                    width: '90%',
                                    buttons: {
                                        '".plus_get_string("close", 'calendar')."': function() {
                                            $(this).dialog('close');
                                        }
                                    }
                                });
                            } else {
                                alert('".plus_get_string("norecordfound", 'form')."');
                            }
                            console.log('response- ', response);
                            console.log('consolelog response- ', response);
                            console.log('response- ', response);
                        });
                    };
                }
                if(prevent.cancomplete){
                    dialogbuttons['".plus_get_string("complete", 'calendar')."']=function() {
                        var reqargs = {
                            'eventid': prevent.id,
                            'status': 3,
                            'message':''
                        };
                        var that = this;
                        \$.ajax({
                            'url': '/api/index.php',
                            'method': 'POST',
                            'timeout': 0,
                            'headers': {
                            'Content-Type': 'application/json',
                            },
                            'data': JSON.stringify({
                            'wsfunction': 'updateEventStatus',
                            'wsargs': reqargs
                            }),
                        }).done(function (response) {
                            $(that).dialog('close');
                            $('#calendar').fullCalendar('refetchEvents');
                        });
                    };
                }
                if(prevent.canclaim){
                    dialogbuttons['".plus_get_string("claim", 'calendar')."']=function() {
                        $(this).dialog('close');
                        var label = $('<label>').text(`".plus_get_string("claimreson", 'calendar').":`);
                        var input = $('<textarea>').attr('cols', '10').attr('rows', '10').attr('id', `confirm_claim${prevent.id}`);
                        var dialog = $('<div>').addClass('dialogbody').append(
                            label,
                            input
                        ).dialog({
                            modal: true,
                            width: 400,
                            buttons: {
                                '".plus_get_string("confirmclaim", 'calendar')."': function() {
                                    var confirm_claim = $(`#confirm_claim${prevent.id}`).val();
                                    var reqargs = {
                                        'eventid': prevent.id,
                                        'status': 5,
                                        'message':confirm_claim
                                    };
                                    // console.log('reqargs---- ', reqargs);
                                    var that = this;
                                    if(confirm_claim == ''){
                                        alert('".plus_get_string("addclaimreson", 'calendar')."');
                                        return;
                                    }
                                    \$.ajax({
                                        'url': '/api/index.php',
                                        'method': 'POST',
                                        'timeout': 0,
                                        'headers': {
                                        'Content-Type': 'application/json',
                                        },
                                        'data': JSON.stringify({
                                        'wsfunction': 'updateEventStatus',
                                        'wsargs': reqargs
                                        }),
                                    }).done(function (response) {
                                        $(that).dialog('close');
                                        $('#calendar').fullCalendar('refetchEvents');
                                    });
                                },
                                '".plus_get_string("close", 'calendar')."': function() {
                                    $(this).dialog('close');
                                }
                            }
                        });

                    }
                
                }
                if(prevent.cancancel){
                    dialogbuttons['".plus_get_string("cancel", 'calendar')."']=function() {
                        $(this).dialog('close');
                        var label = $('<label>').text(`".plus_get_string("cancelreson", 'calendar').":`);
                        var input = $('<textarea>').attr('cols', '10').attr('rows', '10').attr('id', `cancelmessage${prevent.id}`);
                        var dialog = $('<div>').append(
                            label,
                            input
                        ).dialog({
                            modal: true,
                            width: 400,
                            buttons: {
                                '".plus_get_string("confirmcancel", 'calendar')."': function() {
                                    var cancelmessage = $(`#cancelmessage${prevent.id}`).val();
                                    var reqargs = {
                                        'eventid': prevent.id,
                                        'status': 2,
                                        'message':cancelmessage
                                    };
                                    if(cancelmessage == ''){
                                        alert('".plus_get_string("addconfirmcancel", 'calendar')."');
                                        return;
                                    }
                                    var that = this;
                                    \$.ajax({
                                        'url': '/api/index.php',
                                        'method': 'POST',
                                        'timeout': 0,
                                        'headers': {
                                        'Content-Type': 'application/json',
                                        },
                                        'data': JSON.stringify({
                                        'wsfunction': 'updateEventStatus',
                                        'wsargs': reqargs
                                        }),
                                    }).done(function (response) {
                                        $(that).dialog('close');
                                        $('#calendar').fullCalendar('refetchEvents');
                                    });
                                },
                                '".plus_get_string("close", 'calendar')."': function() {
                                    $(this).dialog('close');
                                }
                            }
                        });
                    };
                }
                dialogbuttons['".plus_get_string("close", 'calendar')."']=function() {
                    $(this).dialog('close');
                }
                dialog.dialog({
                    modal: true,
                    width: 800,
                    maxWidth: 900,
                    buttons: dialogbuttons
                });
                console.log(`eventClick calEvent: `, calEvent)
                console.log(`eventClick jsEvent: `, jsEvent)
                console.log(`eventClick view: `, view)
            },
            dayClick: function(date, jsEvent, view) {
                ".($canadd?"console.log('clicked add event');
                var eventdat = myfunction(date['_d']);
                var endevt22 = (parseInt(date['_i']/1000)+3600);
                var endevt33 = (endevt22*1000);
                var endevt = myfunction(endevt33);
                var titlelabel =  \$('<h6>').addClass('col-sm-4').text(' Start date: ');
                var title =  \$('<input>').addClass('col-sm-8').attr('type', 'datetime-local');
                title.attr('id', `event_date${prevent.id}`);
                title.attr('value', eventdat);
                var selectedOption;
                var dialog = \$('<div class=row >').append(
                    \$('<h6>').addClass('col-sm-4').text(' End date: '),
                    \$('<input>').addClass('col-sm-8').attr('type', 'datetime-local').attr('id', `event_enddate${prevent.id}`).attr('value', endevt),
                    \$('<br><br>')
                ).dialog({
                    modal: true,
                    width: 400,
                    buttons: {
                        '".plus_get_string("addevent", 'calendar')."': function() {
                            selectedOption2 = $(`#event_date${prevent.id}`).val();
                            selectedOption3 = $(`#event_enddate${prevent.id}`).val();
                            var url = window.location.origin+'/add-event?startdate='+selectedOption2+'&enddate='+selectedOption3+'&returnto=calendar';
                            window.location.href = url ;
                        },
                        '".plus_get_string("close", 'calendar')."': function() {
                            \$(this).dialog('close');
                        }
                    },
                    '".plus_get_string("close", 'calendar')."': function() {
                        if (selectedOption) {
                            \$('#calendar').fullCalendar('renderEvent', {
                                title: title.val() + ' - ' + selectedOption,
                                start: moment(),
                                allDay: true
                            });
                        }
                    }
                });
                dialog.prepend(title);
                dialog.prepend(titlelabel);
                console.log(`dayClick date: `, date)
                console.log(`dayClick jsEvent: `, jsEvent)
                console.log(`dayClick view: `, view)":'')."
                
            }
        });
    });
    </script>";

  return $html;
}