<?php
function plus_view_subscript(){
  global $wp, $DB, $CFG;
  
  require_once($CFG->dirroot . '/api/moodlecall.php');
  $tem='';
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);


  $formdata = new stdClass();
  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url()."/users/");
    exit;
  }


  $formdata->grade = plus_get_request_parameter("grade", 0);
  $formdata->institutionid = plus_get_request_parameter("id", 0);
  $formdata->schoolsubscriptamt = plus_get_request_parameter("schoolsubscriptamt", 0);
  $formdata->parentsubscriptamt = plus_get_request_parameter("parentsubscriptamt", 0);
  /*Save starts*/
  if(isset($_POST['savesubscription'])){
    $res1 = $MOODLE->get("SaveSubscription",'', $formdata);
    plus_redirect(home_url()."/subscription-setting/?id=".$formdata->institutionid);
    exit;
   
  }
  
  /*Save ends*/
  /*browse starts*/
$res1 = $MOODLE->get("getInstituteSubscription",'', $formdata);
if(isset($res1->data)){
  foreach($res1->data as $row ){
    $tem .= '<tr>
    <td>'.(empty($row->name)?"all":$row->name).'</td>
    <td>'.$row->schoolsubscriptamt.'</td>
    <td>'.$row->parentsubscriptamt.'</td>
    <td class="">'.(empty($row->name)?"":'<a href="/subscription-setting/?id='.$row->institutionid.'&grade='.$row->grade.'">Edit</a>').'</td>
    </tr>';
  }
}
/*browse ends*/

  if(!empty($formdata->institutionid) && !empty($formdata->grade)){
    $APIRES = $MOODLE->get("GetSubscriptionById", null, $formdata);
    if(!empty($APIRES->data)){
      $formdata->schoolsubscriptamt = $APIRES->data->schoolsubscriptamt;
      $formdata->parentsubscriptamt = $APIRES->data->parentsubscriptamt;
    } 
  }



  $gradesdata = $MOODLE->get("getSimpleGrades");
  $allgrades = '';
  if($gradesdata->data && $gradesdata->data && is_array($gradesdata->data->allgrade)){
    foreach ($gradesdata->data->allgrade as $key => $value) {
      $allgrades .= '<option value="'.$value->id.'" '.($formdata->grade == $value->id?'selected':'').'>'.$value->name.'</option>';
    }
  }


  $html  =  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("title", "subscription").'</h4>
                  <form class="forms-sample" method="post">
                  <div class="form-group row">
                      <label for="accounttype" class="col-sm-2 col-form-label">'.plus_get_string("selectgrade", "subscription").'</label>
                      <div class="col-sm-10">
                        <select name="grade" id="grade" class="form-control">
                          '.$allgrades.'
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="search" class="col-sm-2 col-form-label">'.plus_get_string("schoolamount", "subscription").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="schoolsubscriptamt" class="form-control" id="schoolsubscriptamt" placeholder="'.plus_get_string("schoolamount", "subscription").'" value="'.$formdata->schoolsubscriptamt.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="search" class="col-sm-2 col-form-label">'.plus_get_string("parentamount", "subscription").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="parentsubscriptamt" class="form-control" id="parentsubscriptamt" placeholder="'.plus_get_string("parentamount", "subscription").'" value="'.$formdata->parentsubscriptamt.'">
                      </div>
                    </div>
                    <input type="hidden" name="id" value="'.$formdata->institutionid.'">
                    <button type="submit" name="savesubscription" class="btn btn-primary mr-2">'.plus_get_string("submit", "subscription").'</button>
                    <button type="submit" name="cancel" class="btn btn-light">'.plus_get_string("cancel", "subscription").'</button>
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
                          <th>'.plus_get_string("grade", "subscription").'</th>
                          <th>'.plus_get_string("schoolsubsamount", "subscription").'</th>
                          <th>'.plus_get_string("parentsubsamount", "subscription").'</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
              
                  $html .=  $tem;
            $html .=  '</tbody>
                    </table>
                  </div>';
  // $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "user");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '</div>
            </div>
          </div>';
  return $html;
}