<?php 
function studentdatesubscript() {
global $wp, $DB;
$html = '';
$temp = '';
$current_user = wp_get_current_user();
$MOODLE = new MoodleManager($current_user);
$data = new stdClass();
$data->groupid = plus_get_request_parameter("id", 0);
$data->userid = plus_get_request_parameter("userid", 0);
$data->startdate = plus_get_request_parameter("startdate", 0);
$data->enddate = plus_get_request_parameter("enddate", 0);

$freedays = array(0,5,10,15);
if(!in_array($data->extension, $freedays)){
  $data->extension = 0;
}
$APIVAL = $MOODLE->get("getSubstudentdata",'', $data);
$REC = $APIVAL->data;
if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url()."/group-details/?id=".$data->groupid);
    exit;
}

if(isset($_POST['studentsubscript'])){
    $res1 = $MOODLE->get("studentdateSubscript",'', $data);
    plus_redirect(home_url()."/group-details/?id=".$data->groupid);
    exit;
}
if(!empty($REC->startdate)){
  $data->startdate = date("Y-m-d\TH:i", $REC->startdate);

}
if(!empty($REC->enddate)){
  $data->enddate = date("Y-m-d\TH:i", $REC->enddate);
}
// if($REC->remaining > 0){

$html .= '<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body haveaction">
        <form class="forms-sample" method="post" id="student-subscription">
          <div class="form-group row">
            <label  class="col-sm-2 col-form-label">Student Name</label>
            <div class="col-sm-10">
              <label class="form-control" >'.$REC->firstname.' '.$REC->lastname.'</label>
            </div>
          </div>
          <div class="form-group row">
            <label  class="col-sm-2 col-form-label">Grade</label>
            <div class="col-sm-10">
            <label class="form-control" >'.$REC->name.'</label>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">StartDate</label>
            <div class="col-sm-10">
              <input required min="'.date("Y-m-d").'" max="'.date("Y-m-d", strtotime("+2 year")).'" type="datetime-local" name="startdate" class="form-control" id="startdate" value="'.$data->startdate.'">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">EndDate</label>
            <div class="col-sm-10">
              <input required min="'.date("Y-m-d").'" max="'.date("Y-m-d", strtotime("+2 year")).'" type="datetime-local" name="enddate" class="form-control" id="enddate" value="'.$data->enddate.'">
            </div>
          </div>
          <input type="hidden" name="id" value="'.$data->groupid.'">
          <input type="hidden" name="userid" value="'.$data->userid.'">
          <button type="submit" class="btn btn-primary mr-2" name="studentsubscript" >Submit</button>
          <button type="submit" name="cancel" class="btn btn-light">Cancel</button>
        </form>
      </div>
    </div>
  </div>';

// }

  $html .='

  <!-- The Modal -->
  <div class="modal" id="subs-student">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">'.plus_get_string("subs_title", "student").'</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
        <p>
          '.plus_get_string("subs_message", "student").'
          </p>
          <span class=""> '.plus_get_string("subs_amount", "student").'<span class="user-amount"></span></span>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
        <button type="button" class="btn btn-primary submit-subs-form"  style="float:left">'.plus_get_string("subs_yes", "student").'</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">'.plus_get_string("subs_no", "student").'</button>
        </div>
        
      </div>
    </div>

    <script>
    $(function(){
      $(".submit-btn").click(function(){
       var amount=$("#paidamount").val();
       $(".user-amount").text(amount);
        $("#subs-student").modal("show");
        });
      });
      $(".submit-subs-form").click(function(){
          $("form#student-subscription").submit();
        });
      </script>
    ';


  return $html;

}



?>