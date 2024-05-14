<?php

function plus_checkerror(){
  $html="";
  $current_user = wp_get_current_user();
  $MOODLESESSION = wp_get_moodle_session();
  $plus_allUserRoles = plus_allUserRoles($current_user->token);
  $MOODLESESSION->allmyroles = $plus_allUserRoles;

  // echo "<pre>";
  // print_r($plus_allUserRoles);
  // echo "</pre>";
  // die;

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