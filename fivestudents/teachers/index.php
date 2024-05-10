<?php
  require_once("../config.php");
  $syncnow = optional_param("syncnow", "");
  if($syncnow){
    syncAlluserdata($syncnow);
    redirect("{$CFG->wwwroot}/teachers/");
  }
  require_login();
  $OUTPUT->loadjquery();
  echo $OUTPUT->header();
  $alllocaldatafiles = get_alllocaldata();

$html = '';
//$html .= '<pre>'.print_r($alllocaldatafiles, true).'</pre>';
$html .= '<div class="row">
            <div class="col-12 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                <p class="text-center py-4">Teachers Home Page</p>
               </div>
              </div>
            </div>
          </div>';
  $html .='';
  echo $html;
  echo $OUTPUT->footer();