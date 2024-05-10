<?php
require_once("../config.php");
global $DB, $PAGE, $CFG, $USER, $OUTPUT, $INSTITUTION;
require_login();
$disablecalendar = $INSTITUTION->disablecalendar;
if($disablecalendar == 1){
    redirect("{$CFG->wwwroot}/dashboard/", get_string("nopermission", 'site'), "info");
}
$OUTPUT->loadjquery();
$canadd =false;
$html ='';
$html .='<input type="hidden" id="institutionid" value="0"/>';
$html .='<input type="hidden" id="teacherid" value="0"/>';
$html .="
<div id= 'calendar'></div>
<script>
    $(document).ready(function() {

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title', 
                right: 'month,agendaWeek,agendaDay".($canadd?",addEventButton":"")."'
            },
            events: {
                url: '".$CFG->wwwroot."/app_rest_api/offline/calendarevents.php',
                type: 'GET',
                data: {
                    institutionid: $('#institutionid').val(),
                    teacherid: $('#teacherid').val()
                }
            },
            defaultDate: moment().format('YYYY-MM-DD'),
            defaultView: 'agendaWeek',
            locale: '".strtolower($CURRENTLANG)."',
            editable: false,
            eventLimit: true, 
            eventClick: function(calEvent, jsEvent, view) {
                var prevent = calEvent['fulldata'];
                console.log('prevent- ', prevent);
                var sttime = calEvent.start._i;
                var edtime = calEvent.end._i;
                var dialog = \$('<div class= row event_list >').append(
                    \$('<h5>').addClass('col-sm-3').text('".get_string("school", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.institution),
                    \$('<h5>').addClass('col-sm-3').text('".get_string("user", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.teacher),
                    \$('<h5>').addClass('col-sm-3').text('".get_string("group", 'form').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.groupname),
                    \$('<h5>').addClass('col-sm-3').text('".get_string("subject", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.coursename),
                    \$('<h5>').addClass('col-sm-3').text('".get_string("starttime", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'datetime-local').prop('disabled', true).attr('value',sttime),
                    \$('<h5>').addClass('col-sm-3').text('".get_string("endtime", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'datetime-local').prop('disabled', true).attr('value',edtime),
                    \$('<h5>').addClass('col-sm-3').text('".get_string("status", 'calendar').": '),
                    \$('<input>').addClass('col-sm-9').attr('type', 'text').prop('disabled', true).attr('value',prevent.eventstatus),
                );
                var dialogbuttons = {};
                    if(prevent.canstart){
                        dialogbuttons['".get_string("start", 'calendar')."']=function() {
                            var reqargs = {
                                'eventid': prevent.id,
                                'oldstatustime': prevent.statustime,
                                'status': 1,
                                'message':''
                            };
                            var that = this;
                            \$.ajax({
                                'url': '".$CFG->wwwroot."/app_rest_api/offline/index.php',
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
                    if(prevent.canstartsyrvey){
                        dialogbuttons['".get_string("btn_survey", 'calendar')."']=function() {
                            var reqargs = {
                                'eventid': prevent.id,
                                'message':''
                            };
                            var that = this;
                            \$.ajax({
                                'url': '".$CFG->wwwroot."/app_rest_api/offline/index.php',
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
                                var surveylisthtml = `<div class='surveyattempt'> <table><tr><th>".get_string("title", 'survey')."</th><th>".get_string("action", 'survey')."</th></tr>`;
                                var havesurvey = true;
                                if(Array.isArray(response.surveys) && response.surveys.length >0){
                                    $.each( response.surveys, function( key, survey ) {
                                        if(Array.isArray(survey.questions) && survey.questions.length >0){
                                            havesurvey = true;
                                            surveylisthtml += `<tr><td>\${survey.name}</td><td><button data-id='\${survey.id}' data-eventid='\${prevent.id}' data-container='.surveyattempt' startsurvey>".get_string("start", 'survey')."</button></td></tr>`;
                                        }
                                    });
                                } else {
                                    surveylisthtml += `<tr><td colspan='2'><div class='alert alert-warning'>".get_string("norecordfound", 'form')."</div></td></tr>`;
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
                                            '".get_string("close", 'calendar')."': function() {
                                                $(this).dialog('close');
                                            }
                                        }
                                    });
                                } else {
                                    alert('".get_string("norecordfound", 'form')."');
                                }
                                console.log('response- ', response);
                                consolelog('consolelog response- ', response);
                                console.log('response- ', response);
                            });
                        };
                    }
                    if(prevent.cancomplete){
                        dialogbuttons['".get_string("complete", 'calendar')."']=function() {
                            var reqargs = {
                                'eventid': prevent.id,
                                'oldstatustime': prevent.statustime,
                                'status': 3,
                                'message':''
                            };
                            var that = this;
                            \$.ajax({
                                'url': '".$CFG->wwwroot."/app_rest_api/offline/index.php',
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
                    if(prevent.timestart < ".time()." && prevent.status == '4'){
                        
                        dialogbuttons['".get_string("claim", 'calendar')."']=function() {
                            console.log('prevent---------- ', prevent);
                            $(this).dialog('close');

                            var label = $('<label>').text(`".get_string("claimreson", 'calendar').":`);
                            var input = $('<textarea>').attr('cols', '10').attr('rows', '10').attr('id', `confirm_claim\${prevent.id}`);
                            var dialog = $('<div>').addClass('dialogbody').append(
                                label,
                                input
                            ).dialog({
                                modal: true,
                                width: 400,
                                buttons: {
                                    '".get_string("confirmclaim", 'calendar')."': function() {
                                        var confirm_claim = $(`#confirm_claim\${prevent.id}`).val();
                                        // console.log('confirm_claim-------- ', confirm_claim);
                                        var reqargs = {
                                            'eventid': prevent.id,
                                            'oldstatustime': prevent.statustime,
                                            'status': 5,
                                            'message':confirm_claim
                                        };
                                        // console.log('reqargs---- ', reqargs);
                                        var that = this;
                                        if(confirm_claim == ''){
                                            alert('".get_string("addclaimreson", 'calendar')."');
                                            return;
                                        }
                                        \$.ajax({
                                            'url': '".$CFG->wwwroot."/app_rest_api/offline/index.php',
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
                                    '".get_string("close", 'calendar')."': function() {
                                        $(this).dialog('close');
                                    }
                                }
                            });

                        }
                    
                    }
 
                    if(prevent.cancancel){
                        dialogbuttons['".get_string("cancel", 'calendar')."']=function() {
                            $(this).dialog('close');
                            var label = $('<label>').text(`".get_string("cancelreson", 'calendar').":`);
                            var input = $('<textarea>').attr('cols', '10').attr('rows', '10').attr('id', `cancelmessage\${prevent.id}`);
                            var dialog = $('<div>').addClass('dialogbody').append(
                                label,
                                input
                            ).dialog({
                                modal: true,
                                width: 400,
                                buttons: {
                                    '".get_string("confirmcancel", 'calendar')."': function() {
                                        var cancelmessage = $(`#cancelmessage\${prevent.id}`).val();
                                        var reqargs = {
                                            'eventid': prevent.id,
                                            'oldstatustime': prevent.statustime,
                                            'status': 2,
                                            'message':cancelmessage
                                        };
                                        var that = this;
                                        if(cancelmessage == ''){
                                            alert('".get_string("addconfirmcancel", 'calendar')."');
                                            return;
                                        }
                                        \$.ajax({
                                            'url': '".$CFG->wwwroot."/app_rest_api/offline/index.php',
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
                                    '".get_string("close", 'calendar')."': function() {
                                        $(this).dialog('close');
                                    }
                                }
                            });
                        };
                    }

                    dialogbuttons['".get_string("close", 'calendar')."']=function() {
                        $(this).dialog('close');
                    }

                dialogbuttons['".get_string("close", 'calendar')."']=function() {
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
                console.log(`dayClick date: `, date)
                console.log(`dayClick jsEvent: `, jsEvent)
                console.log(`dayClick view: `, view)
                
            }
        });    
    });
</script>";

echo $OUTPUT->header();
echo $html;
echo $OUTPUT->footer();