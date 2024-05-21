<?php
require_once(__DIR__ . "/../config.php");
require_once($CFG->dirroot . '/api/moodlecall.php');

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("HTTP/1.0 200 Successfull operation");

$args =$_GET;
$events = array();
$current_user = wp_get_current_user();
$MOODLE = new MoodleManager($current_user);
$APIRES = $MOODLE->get("getCalendarEvents", null, $args);
if($APIRES->code == 200 && isset($APIRES->data->events)){
    if(is_array($APIRES->data->events)){
        foreach ($APIRES->data->events as $key => $event) {
            $eventstatus = "none";
            $canstart = false;
            $cancancel = false;
            $cancomplete = false;
            $canedit = false;
            $canadd = false;
            $canvisit = false;
            $candelete = false;
            $canclaim = false;
            $canstartsurvey = false;
            if($event->timestart < time() && $event->status == '4' &&
                (
                    ($event->myevents == 1 && current_user_can('plus_isconsultant')) ||
                    ($event->myevents == 0 && current_user_can('plus_istutor'))
                )
            ){
                $canclaim = true;
            }
            if(current_user_can('plus_editevents')){
                $canadd = true;
                if($event->status == 0){
                    $canedit = true;
                }
            }
            if(
                ($event->status == 0 || $event->status == 4) && 
                (
                    (current_user_can('plus_canstartevents') && $event->timestart <= time() && $event->timeend > time()) ||
                    ($event->myevents == 1 && $event->timestart <= time() && current_user_can('plus_canstarteventsanytime'))
                ) &&
                (
                    ($event->myevents == 1 && current_user_can('plus_isconsultant')) ||
                    ($event->myevents == 0 && current_user_can('plus_istutor'))
                )
            ){
                $canstart = true;
            }
            if(
                ($event->status != 2 && $event->status != 3) && current_user_can('plus_cancancelevents') &&
                (
                    ($event->myevents == 1 && current_user_can('plus_isconsultant')) ||
                    ($event->myevents == 0 && current_user_can('plus_istutor'))
                )
            ){
                $cancancel = true;
            }
            if(
                $event->status == 1 && current_user_can('plus_cancompleteevents') &&
                (
                    ($event->myevents == 1 && current_user_can('plus_isconsultant')) ||
                    ($event->myevents == 0 && current_user_can('plus_istutor'))
                )
            ){
                $cancomplete = true;
            }
            if($event->myevents != 1 && $event->timestart > time() && current_user_can('plus_canvisitevents')){
                $canvisit = true;
            }
            if($event->status == 0 && $event->timestart > time() && current_user_can('plus_candeleteevents') && ($event->myevents == 1 && current_user_can('plus_isconsultant'))){
                $candelete = true;
            }
            switch ($event->status) {
                case 0:
                    $eventstatusclass = "planned";
                    break;
                case 1:
                    $eventstatusclass = "started";
                    if(
                        current_user_can('plus_cansubmitsurvey') &&
                        (
                            ($event->myevents == 1 && current_user_can('plus_isconsultant')) ||
                            ($event->myevents == 0 && current_user_can('plus_istutor'))
                        )
                    ){
                        $canstartsurvey = true;
                    }
                    break;
                case 2:
                    $eventstatusclass = "cancelled";
                    break;
                case 3:
                    $eventstatusclass = "completed";
                    break;
                case 4:
                    $eventstatusclass = "planned";
                    break;
                case 5:
                    $eventstatusclass = "claim";
                    break;
                case 6:
                    $eventstatusclass = "inprogress";
                    break;
                default:
                    break;
            }
            
            $eventstatus = plus_get_string("status_{$eventstatusclass}", "calendar");
            $event->canclaim = $canclaim;
            $event->canstart = $canstart;
            $event->canstartsurvey = $canstartsurvey;
            $event->candelete = $candelete;
            $event->canvisit = $canvisit;
            $event->eventstatus = $eventstatus;
            $event->cancancel = $cancancel;
            $event->cancomplete = $cancomplete;
            $event->canadd = $canadd;
            $event->canedit = $canedit;
            $eventname = $event->id." - ".$event->name;
            
            if(!empty($event->coursename)){
                $eventname = $event->id." - ".$event->coursename;
            }
            
            if(!empty($event->groupname)){
                $eventname .= "({$event->groupname})";
            }

            array_push($events, array(
                "start"=>date("Y-m-d\TH:i:s", $event->timestart),
                "end"=>date("Y-m-d\TH:i:s", $event->timeend),
                "duration"=>$event->timeduration,
                "title"=>$eventname,
                "description"=>$event->description,
                "fulldata"=>$event,
                "className"=>array('status-'.$eventstatusclass),
            ));
        }
    }
}
echo json_encode($events);
