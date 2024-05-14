<?php
function plus_view_groups(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  $generatemonthlyreport = plus_get_request_parameter("generatemonthlyreport", 0);
  $groupid = plus_get_request_parameter("groupid", 0);
  if(current_user_can('plus_generatemonthlyreport') && !empty($generatemonthlyreport) && !empty($groupid)){
    $APIRES = $MOODLE->get("generaterMonthlyReport", null, array("groupid"=>$groupid));
    plus_redirect(home_url()."/groups/");
    exit;
  }

  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }
  $searchreq->name = plus_get_request_parameter("groupname", "");
  $searchreq->teacher = plus_get_request_parameter("teacher", "");
  $searchreq->createddatefrom = plus_get_request_parameter("createddatefrom", "");
  $searchreq->createddateto = plus_get_request_parameter("createddateto", "");
  $searchreq->start = plus_get_request_parameter("start", 0);
  $searchreq->limit = plus_get_request_parameter("limit", 10);
  $searchreq->currentschoolyear = plus_get_request_parameter("currentschoolyear", 0);
  $searchreq->total = 0;
  $APIRES = $MOODLE->get("BrowseGroups", null, $searchreq);
  $html='';
  // $html.='<pre>';
  // $html.=print_r($APIRES, true);
  // $html.='</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("groups", "site").'</h4>
                  '.(current_user_can('plus_addgroups')?'<a class="btn btn-primary card-body-action" href="'.$CFG->wwwroot.'/add-group"><i class="mdi mdi-plus"></i></a>':'').'
                  
                  <form class="forms-sample">
                    <div class="form-group row">
                      <label for="name" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="groupname" class="form-control" id="name" placeholder="'.plus_get_string("name", "form").'" value="'.$searchreq->name.'">
                      </div>
                    </div>
                    <input type="hidden" name="start" value="0"/>
                    <input type="hidden" name="limit" value="10"/>
                    <button type="submit" name="filter" class="btn btn-primary mr-2">'.plus_get_string("search", "form").'</button>
                    <button type="submit" name="cancel" class="btn btn-light">'.plus_get_string("cancel", "form").'</button>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"></h4>';
  $html .=        '<div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("action", "table").'</th>
                          <th>'.plus_get_string("name", "form").'</th>
                          <th>'.plus_get_string("level", "form").'</th>
                          <th>'.plus_get_string("matter", "form").'</th>
                          <th>'.plus_get_string("teacher", "form").'</th>
                          <th>'.plus_get_string("noofstudent", "form").'</th>
                          '.(current_user_can('plus_viewtobeapproved')?'<th>'.plus_get_string("tobeapproved", "form").'</th>':'').'
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_array($APIRES->data->groups)){
                foreach ($APIRES->data->groups as $key => $group) {
                  $html .=  '<tr>
                              <td class="p-1 text-center">'.(current_user_can('plus_viewgroupdetails')?'<a href="'.$CFG->wwwroot.'/group-details?id='.$group->id.'" style="font-size:32px;"><i class="mdi mdi-magnify"></i></a>':'').'</td>
                              <td class="py-1">'.$group->name.'</td>
                              <td class="py-1">'.$group->grade.'</td>
                              <td class="py-1">'.$group->coursename.'</td>
                              <td class="">'.$group->teachers.'</td>
                              <td class="">'.$group->totalusers.'</td>
                            '.(current_user_can('plus_viewtobeapproved')?'<td class="">'.$group->pendingapproval.'</td>':'').'
                              <td class="">
                                '.(current_user_can('plus_generategrouplink')?'<span data-id="'.$group->id.'" class="btn copyLink"><i class="mdi mdi-content-copy"></i>'.plus_get_string("copylink", "form").'</span> &nbsp; &nbsp;':'').'
                                '.(current_user_can('plus_generategroupcode')?'<span data-id="'.$group->id.'" class="btn copyCode"><i class="mdi mdi-content-copy"></i>'.plus_get_string("copycode", "form").'</span> &nbsp; &nbsp;<span data-id="'.$group->id.'" class="btn copyOneTimeCode"><i class="mdi mdi-content-copy"></i>'.plus_get_string("copyOneTimeCode", "form").'</span> &nbsp; &nbsp; <span data-id="'.$group->id.'" class="btn copyExamCode"><i class="mdi mdi-content-copy"></i>'.plus_get_string("copyexamcode", "form").'</span> &nbsp; &nbsp;':'').'
                                '.(current_user_can('plus_addgroups')?'<a href="'.$CFG->wwwroot.'/add-group?id='.$group->id.'"><i class="mdi mdi-lead-pencil"></i> '.plus_get_string("edit", "form").'</a>&nbsp; &nbsp;':'').'
                                '.(current_user_can('plus_viewgroupdetails')?'<a href="'.$CFG->wwwroot.'/group-details?id='.$group->id.'"> '.plus_get_string("details", "form").' </a> &nbsp; &nbsp;':'').'
                                '.(current_user_can('plus_generatemonthlyreport')?'<a href="/groups/?generatemonthlyreport=1&groupid='.$group->id.'"> '.plus_get_string("generatemonthlyreport", "form").' </a>':'').'
                                </td>
                              </tr>';
                }
                $searchreq->total = $APIRES->data->total;
                $searchreq->start = $APIRES->data->start;
                $searchreq->limit = $APIRES->data->limit;
              } else {
    $html .=              '<tr><td colspan="6" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
              }        
              // $html .=  '<tr>
              //             <td class="py-1"></td>
              //             <td>Herman Beck</td>
              //             <td><div class="progress">
              //                 <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
              //               </div></td>
              //             <td>$ 77.99</td>
              //             <td>May 15, 2015</td>
              //             <td>May 15, 2015</td>
              //             <td>May 15, 2015</td>
              //             <td>May 15, 2015</td>
              //           </tr>';
            $html .=  '</tbody>
                    </table>
                  </div>';
  $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "group");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '</div>
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
        displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("copylinksuccess", "form").'", "info");
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("copylinkfailed", "form").'", "error");
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
        displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("copycodesuccess", "form").'", "info");
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("copycodefailed", "form").'", "error");
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
        displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("copycodesuccess", "form").'", "info");
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("copycodefailed", "form").'", "error");
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
        displayToast("'.plus_get_string("success", "form").'","'.plus_get_string("copycodesuccess", "form").'", "info");
      } else {
        displayToast("'.plus_get_string("failed", "form").'","'.plus_get_string("copycodefailed", "form").'", "error");
      }
    });
  });
});
</script>';
  return $html;
}