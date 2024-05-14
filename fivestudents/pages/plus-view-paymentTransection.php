<?php
function paymenttrans(){
  global $wp, $DB,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $fromform = new stdclass();
  $temp = "";
    $APIRES22 = $MOODLE->get("getGroupByInstituteId", null);

    $fromform->stname = plus_get_request_parameter("stname","");
    $fromform->group = plus_get_request_parameter("group",0);
    $fromform->fromdate = plus_get_request_parameter("fromdate",date("Y-m-01"));
    $fromform->todate = plus_get_request_parameter("todate",date("Y-m-t"));
    $apidatas = $MOODLE->get("getTransectionData",null,$fromform);
    if(isset($apidatas->data) && is_array($apidatas->data)){
      foreach ($apidatas->data as $key) {
      $temp .= "<tr>
              <td>".$key->studentname."</td>
              <td>".$key->gradename."</td>
              <td>".$key->groupname."</td>
              <td>".$key->tnsid."</td>
              <td>".$key->id."</td>
              <td>".$key->totalamount."</td>
              <td>".$key->paidamount."</td>
              <td>".$key->amount."</td>
              <td>".$key->paidby."</td>
              <td>".plus_dateToFrench($key->paiddate, "d F Y H:i A")."</td>
      
              </tr>";
      }
    }
// echo "<pre>";
// print_r($apidatas);
// echo "</pre>";
  $html='<link rel="stylesheet" href="'.__FILE__ .'/public/../../../vendors/select2/select2.min.css">
  <link rel="stylesheet" href="'. __FILE__ .'/public/../../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
';
$html .=  '<div class="row">
<div class="col-md-12 grid-margin transparent">
  <div class="row">';
$html .=  '<div class="col-md-12 grid-margin stretch-card">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">'.plus_get_string("title", "paymenttransection").'</h4>
      <form class="forms-sample"  method="get">
            <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").'</label>
                <div class="col-sm-10">
                    <input type="text" name="stname" class="form-control" id="stname" placeholder="'.plus_get_string("name", "form").'" value="'.$fromform->stname.'">
                </div>
            </div>
            <div class="form-group row">
                <label for="group" class="col-sm-2 col-form-label">'.plus_get_string("group", "site").'</label>
                <div class="col-sm-10">
                    <select class="form-control" id="group" name="group">
                        <option value="">'.plus_get_string("tochoose", "form").' '.plus_get_string("group", "site").'</option>';
                        foreach($APIRES22->data as $group_row){
                            $selected='';
                            if($group_row->id == $fromform->group){
                            $selected='selected';
                            }
                            $html.='<option value="'.$group_row->id.'" '.$selected.'>'.$group_row->name.'</option>';
                         }

            $html.= '</select>
                </div>
            </div>
            <div class="form-group row">
                <label for="fromdate" class="col-sm-2 col-form-label">'.plus_get_string("from", "form").'</label>
                <div class="col-sm-10">
                    <input type="date" name="fromdate" class="form-control" id="fromdate" value="'.$fromform->fromdate.'">
                </div>
            </div>
            <div class="form-group row">
                <label for="todate" class="col-sm-2 col-form-label">'.plus_get_string("to", "form").'</label>
                <div class="col-sm-10">
                    <input type="date" name="todate" class="form-control" id="todate" value="'.$fromform->todate.'">
                </div>
            </div>
               
                <button type="submit" name="filter" class="btn btn-primary mr-2">Search</button>
                <button type="submit" name="cancel" class="btn btn-light">Cancel</button>
      </form>
    </div>
  </div>    
</div>';

$html .=  '<div class="col-lg-12 grid-margin stretch-card">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title"></h4>';
$html .=        '<div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>'.plus_get_string("studentname", "paymenttransection").'</th>
              <th>'.plus_get_string("grade", "paymenttransection").'</th>
              <th>'.plus_get_string("group", "paymenttransection").'</th>
              <th>'.plus_get_string("transectionid", "paymenttransection").'</th>
              <th>'.plus_get_string("subscriptionid", "paymenttransection").'</th>
              <th>'.plus_get_string("totalamount", "paymenttransection").'</th>
              <th>'.plus_get_string("paidamount", "paymenttransection").'</th>
              <th>'.plus_get_string("installment", "paymenttransection").'</th>
              <th>'.plus_get_string("paidby", "paymenttransection").'</th>
              <th>'.plus_get_string("paiddate", "paymenttransection").'</th>
            </tr>
          </thead>
          <tbody>
         '. $temp.'
        </tbody>
        </table>
      </div>';
// $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "homework");
$html .=      '</div>
  </div>
</div>
</div>
</div>
</div>
';
// $html .=  '<script src="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2/select2.min.js"></script><script src="'.plugin_dir_url( __FILE__ ).'/public/../../../js/select2.js"></script>';
echo $html;
}