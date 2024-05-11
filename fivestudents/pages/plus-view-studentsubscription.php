<?php 
function studentsubscript() {
global $wp, $DB;
$html = '';
$temp = '';
$current_user = wp_get_current_user();
$MOODLE = new MoodleManager($current_user);
$data = new stdClass();
$data->groupid = plus_get_request_parameter("id", 0);
$data->userid = plus_get_request_parameter("userid", 0);
$data->paidamount = plus_get_request_parameter("paidamount", 0);
$data->extension = plus_get_request_parameter("extension", 0);

$freedays = array(0,5,10,15);
if(!in_array($data->extension, $freedays)){
  $data->extension = 0;
}
$APIVAL = $MOODLE->get("getSubstudentdata",'', $data);
$REC = $APIVAL->data;
// echo "<pre>";
// print_r($REC);
foreach ($REC->alltrans as $key ) {

$temp .= '    <tr>   <td>'.$key->tnsid.'</td>
              <td>'.$key->id.'</td>
                <td>'.$key->gradename.'</td>
                <td>'.$key->totalamount.'</td>
                <td>'.$key->amount.'</td>
                <td>'.$key->paidby.'</td>    </tr>' ;          

}

if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url()."/group-details/?id=".$data->groupid);
    exit;
}

if(isset($_POST['studentsubscript'])){
    $res1 = $MOODLE->get("studentSubscript",'', $data);
    /*echo "<pre>";
    print_r ($res1);
    die;*/
    plus_redirect(home_url()."/student-subscription/?id=".$data->groupid."&userid=".$data->userid);
    exit;
}
if($REC->remaining > 0){

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
            <label class="col-sm-2 col-form-label">Total Amount</label>
            <div class="col-sm-10">
            <label class="form-control" >'.$REC->total_amount.'</label>
            </div>
            </div>
            <div class="form-group row">
            <label class="col-sm-2 col-form-label">Remaining</label>
            <div class="col-sm-10">
            <label class="form-control" >'.$REC->remaining.'</label>
            </div>
          </div>

         


          <div class="form-group row">
            <label for="lastname" class="col-sm-2 col-form-label">'.plus_get_string("extension", "student").'*</label>
            <div class="col-sm-10">
              <select required="required" name="extension" class="form-control" id="extension">
                <option value="0" '.($data->extension=="0"?'selected':"").' >None</option>
                <option value="5" '.($data->extension=="5"?'selected':"").' >5 Days</option>
                <option value="10" '.($data->extension=="10"?'selected':"").' >10 Days</option>
                <option value="15" '.($data->extension=="15"?'selected':"").' >15 Days</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Pay</label>
            <div class="col-sm-10">
              <input required min="'.($REC->remaining>50?50:$REC->remaining).'" max="'.$REC->remaining.'" type="number" name="paidamount" class="form-control" id="paidamount" placeholder="Amount" value="'.($REC->remaining>50?50:$REC->remaining).'">
            </div>
          </div>
          <input type="hidden" name="id" value="'.$data->groupid.'">
          <input type="hidden" name="userid" value="'.$data->userid.'">
          <button type="button" class="btn btn-primary mr-2 submit-btn" >Submit</button>
          <input type="hidden" name="studentsubscript" />
          <button type="submit" name="cancel" class="btn btn-light">Cancel</button>
        </form>
      </div>
    </div>
  </div>';

}

  $html .='<div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body haveaction">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Transection Id</th>
                    <th>Subscription Id</th>
                    <th>Grade</th>
                    <th>Total Amount</th>
                    <th>Amount</th>
                    <th>Paid By</th>
                    </tr>
                </thead>
                <tbody>
                <div class="row  align-items-center">
                  <label  class="col-sm-2 col-form-label">Current Grade :</label>
                    <div class="col-sm-10">
                    <div>'.$REC->name.'</div>
                    </div>
                  <label  class="col-sm-2 col-form-label">Total Amount :</label>
                    <div class="col-sm-10">
                    <div>'.$REC->total_amount.'</div>                  
                    </div>
                  <label  class="col-sm-2 col-form-label">Paid Amount :</label>
                    <div class="col-sm-10">
                    <div>'.$REC->paidamount.'</div>
                    </div>
                  <label  class="col-sm-2 col-form-label">Remain Amount :</label>
                    <div class="col-sm-10">
                    <div>'.$REC->remaining.'</div>
                    </div>

                     <label  class="col-sm-2 col-form-label">Subscription Type :</label>
                    <div class="col-sm-10">
                    <div>'.$REC->subscriptionType.'</div>
                    </div>

                     <label  class="col-sm-2 col-form-label">Expiry Date :</label>
                    <div class="col-sm-10">
                    <div>'.$REC->expiryDate.'</div>
                    </div>
                </div>
                '.$temp.'
               </tbody>
              </table>
            </div>
          </div>
        </div>

    </div>




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


  echo $html;

}



?>