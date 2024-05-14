<?php
function plus_view_cancelledevents(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $current_user = wp_get_current_user();
  $searchreq = new stdClass();
  $searchreq->id = plus_get_request_parameter("id", "");
  $html='cancelled events';
  return $html;
}



     

