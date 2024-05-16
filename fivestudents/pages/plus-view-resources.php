<?php
function plus_view_resources()
{
  global $CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  $searchreq->t = plus_get_request_parameter("t", "");
  $APIRES = $MOODLE->get("BrowseResources", null, $searchreq);
  $apidata = print_r($APIRES, true);
  $html = '
  <style>
    .resource-preview {
      background: rgba(0,0,0,0.1);
      padding: 10px;
      margin-top: 30px;
    }
    .resource-preview .card-img-top, .resource-preview .card-body{
      text-align:center;
      line-height: 1em;
    }
   .resource-preview .card-img-top a, .resource-preview .card-img-top i {
      line-height: 0.8em;
      font-size: 100px;
      display: inline-block;
    }
  </style>
  ';
  // $html .='<pre>'.$apidata.'</pre>';
  $topicshtml = '';
  $moduleshtml = '';
  $found = false;
  $breadcrumbs = "";
//   $breadcrumbs = '                            <li class="breadcrumb-item"><a href="#">UI Elements</a></li>
//                             <li class="breadcrumb-item active" aria-current="page">Typography</li>
// ';

  if ($APIRES->code == 200 && !empty($APIRES->data)) {
    $course = $APIRES->data;
    // $reshtml.= "<pre>".print_r($course, true)."</pre>";
    if (isset($course->alltopics) && !empty($course->alltopics)) {
      foreach ($course->alltopics as $key => $topics) {
        if (!empty($topics->name)) {
          $topicshtml .= '<div class="col-sm-3">
                        <a class="btn resource-preview  btn-secondary" href="'.$CFG->wwwroot.'/resources?t=' . $topics->id . '"><i class="mdi mdi-folder btn-icon-prepend"></i> ' . $topics->name . ' </a>
                      </div>';
          $found = true;
        }
      }
    }

    if (isset($course->allmodules) && !empty($course->allmodules)) {
      foreach ($course->allmodules as $key => $module) {
        if (!empty($module->name)) {
          $moduleshtml .= '<div class="col-sm-3">
                        <div class="card resource-preview">
                          <div class="card-img-top"><a class="font-weight-bold" href="'.$CFG->wwwroot.'/resource-details?id=' . $module->id . '"><i class="mdi mdi-folder"></i></a></div>
                          <div class="card-body">
                            <p><a class="font-weight-bold" href="/resource-details?id=' . $module->id . '">' . $module->name . '</a>
                            </p>
                          </div>
                        </div>
                      </div>';
          $found = true;
        }
      }
    }
    if(isset($course->breadcrumbs) && !empty($course->breadcrumbs)){
      foreach ($course->breadcrumbs as $key => $belement) {
       $breadcrumbs .= '<li class="breadcrumb-item"><a href="'.$CFG->wwwroot.'/resources?t=' . $belement->id . '">' . $belement->name . '</a></li>';
      }
    }
  }
  if (!$found) {
    $topicshtml = '<div class="col-12"><div class="alert alert-info">Empty result</div></div>';
  }
  $html .= ' <div class="row flex-column">
              <div class="col-md-12 grid-margin transparent">
                <div class="row">
                  <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="card-title">' . (!empty($course->parent)?$course->parent->name:'') . '</h4>
                        <nav aria-label="breadcrumb">' . (!empty($breadcrumbs)?'<ol class="breadcrumb">'.$breadcrumbs.'</ol>':'') . '</nav>
                        <div class="row">' . $topicshtml . '</div>
                        '.(!empty($moduleshtml)?'<div class="row">' . $moduleshtml . '</div>':'').'
                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
<script>
$(document).ready(function(){
  $(".copyLink").click(function(){
    var groupid = $(this).data("id");
    var reqargs = {
        "groupid": groupid
    };
    var shortlinksetting = getAPIRequest("getGroupLinkID",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      if(response.data && response.data.shortLink){
        navigator.clipboard.writeText(response.data.shortLink);
        displayToast("' . plus_get_string("success", "form") . '","' . plus_get_string("copylinksuccess", "form") . '", "info");
      } else {
        displayToast("' . plus_get_string("failed", "form") . '","' . plus_get_string("copylinkfailed", "form") . '", "error");
      }
    });
  });
  $(".copyCode").click(function(){
    var groupid = $(this).data("id");
    var reqargs = {
        "groupid": groupid
    };
    navigator.clipboard.writeText(groupid);
    var shortlinksetting = getAPIRequest("getGroupCode",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      if(response.data && response.data.grouplinkid){
        navigator.clipboard.writeText(response.data.grouplinkid);
        displayToast("' . plus_get_string("success", "form") . '","' . plus_get_string("copycodesuccess", "form") . '", "info");
      } else {
        displayToast("' . plus_get_string("failed", "form") . '","' . plus_get_string("copycodefailed", "form") . '", "error");
      }
    });
  });
  $(".copyOneTimeCode").click(function(){
    var groupid = $(this).data("id");
    var reqargs = {
        "groupid": groupid
    };
    navigator.clipboard.writeText(groupid);
    var shortlinksetting = getAPIRequest("getGroupOnetimeCode",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      if(response.data && response.data.grouplinkid){
        navigator.clipboard.writeText(response.data.grouplinkid);
        displayToast("' . plus_get_string("success", "form") . '","' . plus_get_string("copycodesuccess", "form") . '", "info");
      } else {
        displayToast("' . plus_get_string("failed", "form") . '","' . plus_get_string("copycodefailed", "form") . '", "error");
      }
    });
  });
  $(".copyExamCode").click(function(){
    var groupid = $(this).data("id");
    var reqargs = {
        "groupid": groupid
    };
    navigator.clipboard.writeText(groupid);
    var shortlinksetting = getAPIRequest("getGroupExamCode",reqargs);
    $.ajax(shortlinksetting).done(function (response) {
      if(response.data && response.data.grouplinkid){
        navigator.clipboard.writeText(response.data.grouplinkid);
        displayToast("' . plus_get_string("success", "form") . '","' . plus_get_string("copycodesuccess", "form") . '", "info");
      } else {
        displayToast("' . plus_get_string("failed", "form") . '","' . plus_get_string("copycodefailed", "form") . '", "error");
      }
    });
  });
});
</script>';

return $html;
}
