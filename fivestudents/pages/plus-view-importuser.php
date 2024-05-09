<?php
function plus_view_importuser(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager();
  $searchreq = new stdClass();
  $generatemonthlyreport = plus_get_request_parameter("generatemonthlyreport", 0);
  $searchreq->groupid = plus_get_request_parameter("groupid", 0);
  $searchreq->usersdata = array();
  // if(!empty($generatemonthlyreport) && !empty($groupid)){
  //   $APIRES = $MOODLE->get("generaterMonthlyReport", null, array("groupid"=>$groupid));
  //   plus_redirect(home_url()."/groups/");
  //   exit;
  // }
  $html='';
  $fileerr = "";

  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }
  if ( isset($_POST["uploaduser"]) ){
    if ($_FILES["file"]["error"] > 0) {
      $fileerr.= "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
    if($_FILES['file']['error'] == 0){
      $name = $_FILES['file']['name'];
      $ext = strtolower(end(explode('.', $_FILES['file']['name'])));
      $type = $_FILES['file']['type'];
      $tmpName = $_FILES['file']['tmp_name'];
      if($ext === 'csv'){
        if(($handle = fopen($tmpName, 'r')) !== FALSE) {
          set_time_limit(0);
          $row = 0;
          $data = fgetcsv($handle, 1000, ',');
          $allgroups = array();
          if( ($data[0] == 'Firstname') && ($data[1] == 'Lastname') && ($data[2] == 'CODE MASSAR') && ($data[3] == 'GROUPE') ){
            // die;
            while(($data1 = fgetcsv($handle, 1000, ',')) !== FALSE) {
              $data1[0] = mb_convert_encoding($data1[0], 'UTF-8', 'UTF-8');
              $data1[1] = mb_convert_encoding($data1[1], 'UTF-8', 'UTF-8');
              $data1[2] = mb_convert_encoding($data1[2], 'UTF-8', 'UTF-8');
              $data1[3] = mb_convert_encoding($data1[3], 'UTF-8', 'UTF-8');
              if(!in_array($data1[3], $allgroups)){array_push($allgroups, $data1[3]);}
              array_push($searchreq->usersdata, $data1);
            }
          }else{
            $fileerr .= '<div class = "alert alert-danger">Please upload the valid formate csv file</div>';
          }
        } else {
          $fileerr .= '<div class = "alert alert-danger">unable to get file, please select again</div>';
        }
      } else {
        $fileerr .= '<div class = "alert alert-danger">Please upload csv file</div>';
      }
    } else {
      $fileerr .= '<div class = "alert alert-danger">Error... Please try again</div>';
    }
    if(sizeof($searchreq->usersdata) > 0){
      $searchreq->allgroups = $allgroups;
      $UPLOADAPIRES = $MOODLE->get("importUsersData", null, $searchreq);
    } else {
      $fileerr .= '<div class = "alert alert-danger">Empty CSV File, please check</div>';
    }
  }
  $GROUPAPIRES = $MOODLE->get("getGroupByInstituteId", null, array());
  
  // $html .='<pre>'.print_r($GROUPAPIRES, true).'</pre>';
  // $html .='<pre>'.print_r($fileerr, true).'</pre>';


  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">Upload Bulk User</h4>
                  <a class="btn btn-primary card-body-action" href="/add-group"><i class="mdi mdi-plus"></i></a>
                  <form class="forms-sample" method="POST" enctype="multipart/form-data" >
                    <div class="form-group row">
                      <label for="file" class="col-sm-2 col-form-label">Upload file * : </label>
                      <div class="col-sm-10">
                        <input type="file" name="file" class="form-control" id="file">
                        <span>only csv file upload here.</span>
                      </div>
                    </div>
                    '.$fileerr.'
                    <button type="submit" name="uploaduser" class="btn btn-primary mr-2">Upload User</button>
                    <button type="submit" name="cancel" class="btn btn-light">Cancel</button>
                  </form>
                </div>
              </div>
            </div>';

  $html .=  '</div>
            </div>
          </div>';
  return $html;
}