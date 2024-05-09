<?php
plus_startMoodleSession();
function plus_checkerror(){
  $html="";
  global $MOODLESESSION;
  if(is_string($MOODLESESSION)){
    return $MOODLESESSION;
  }
  if(!in_array("internaladmin", $MOODLESESSION->allmyroles)){
    if($MOODLESESSION->code == 401){
      $html = '<div class="alert alert-danger" role="alert">'.$MOODLESESSION->error->message.'</div>';
    }
  }
  return $html;
}