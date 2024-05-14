<?php
function plus_view_student_scorecard(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }

  $currtenddate = date("Y-m-d", strtotime("now"));
  $currtstartdate = date("Y-m-d", strtotime("-7 day"));
  $searchreq->gradelevel = plus_get_request_parameter("gradelevel", array());
  $searchreq->startdate = plus_get_request_parameter("startdate", $currtstartdate);
  $searchreq->enddate = plus_get_request_parameter("enddate", $currtenddate);
  $searchreq->start = plus_get_request_parameter("start", 0);
  $searchreq->charactername = plus_get_request_parameter("charactername", 0);
  $searchreq->showgrades = plus_get_request_parameter("showgrades", 0);
  $searchreq->limit = plus_get_request_parameter("limit", 0);
  $searchreq->total = 0;
  $APIRES = $MOODLE->get("get_student_scorecard",null,$searchreq); 
  $APIRES22 = $MOODLE->get("getGroupByInstituteId", null);
  $APIRESGrades = $MOODLE->get("GetGrades");
  $APIRESGradeLevel = $MOODLE->get("get_garde_level_data");
  $all_grade_level=array();
  $all_selctedgrouplevel=array();
  $html='<link rel="stylesheet" href="'. __FILE__ .'/public/../../../vendors/select2/select2.min.css">
  <link rel="stylesheet" href="'. __FILE__ .'/public/../../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
';
// echo "<pre>";
// print_r($APIRES);
// echo "</pre>";
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">'.plus_get_string("studentrankingreport", "site").'</h4>
                  <form class="forms-sample">
                   
                    <div class="form-group row">
                    <label for="startdate" class="col-sm-2 col-form-label">'.plus_get_string("from", "form").'</label>
                    <div class="col-sm-10">
                    <input type="date" name="startdate" class="form-control" id="startdate" value="'.$searchreq->startdate.'">
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="enddate" class="col-sm-2 col-form-label">'.plus_get_string("to", "form").'</label>
                    <div class="col-sm-10">
                    <input type="date" name="enddate" class="form-control" id="enddate" value="'.$searchreq->enddate.'">
                    </div>
                    </div>
					
					<div class="form-group row">
                      <label for="gradelevel" class="col-sm-2 col-form-label">'.plus_get_string("gradelevel", "site").'</label>
                      <div class="col-sm-10">
                        <select name="gradelevel[]" id="gradelevel" class="form-control" multiple>';
						
						  foreach($APIRESGradeLevel->data->grades_level as $grades_row){
							$selected='';
               array_push($all_grade_level,$grades_row);
							if(in_array($grades_row->id, $searchreq->gradelevel)){
							  $selected='selected';
                array_push($all_selctedgrouplevel, $grades_row);
							}
							$html.='<option value="'.$grades_row->id.'" '.$selected.'>'.$grades_row->name.'</option>';
						  }

					   $html.= '</select>	
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="charactername" class="col-sm-2 col-form-label">'.plus_get_string("chartname", "form").'</label>
                      <div class="col-sm-10">
                        <input type="checkbox" '.($searchreq->charactername==1?' checked ':'').' name="charactername" class="form-control1" id="charactername" value="1">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="showgrades" class="col-sm-2 col-form-label">'.plus_get_string("showgrades", "site").'</label>
                      <div class="col-sm-10">
                        <input type="checkbox" '.($searchreq->showgrades==1?' checked ':'').' name="showgrades" class="form-control1" id="showgrades" value="1">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label for="group" class="col-sm-2 col-form-label">'.plus_get_string("limit", "form").'</label>
                      <div class="col-sm-10">
            <select class="form-control" name="limit">
              <option value="0" '.($searchreq->limit==0?'selected':'').' >'.plus_get_string("all", "site").'</option>
              <option value="10" '.($searchreq->limit==10?'selected':'').' >10 '.plus_get_string("lines", "form").'</option>
              <option value="20" '.($searchreq->limit==20?'selected':'').' >20 '.plus_get_string("lines", "form").'</option>
              <option value="50" '.($searchreq->limit==50?'selected':'').' >50 '.plus_get_string("lines", "form").'</option>
              <option value="100" '.($searchreq->limit==100?'selected':'').' >100 '.plus_get_string("lines", "form").'</option>
              <option value="100" '.($searchreq->limit==250?'selected':'').' >250 '.plus_get_string("lines", "form").'</option>
              ';

             $html.= '</select> 
                      </div>
                    </div>
					
					
					
                    <input type="hidden" name="start" value="0"/>
                    <button type="submit" name="filter" class="btn btn-primary mr-2">'.plus_get_string("search", "form").'</button>
                    <button type="submit" name="cancel" class="btn btn-light">'.plus_get_string("cancel", "form").'</button>
                  </form>
                </div>
              </div>
            </div>';
  // $html .=  '<div class="col-lg-12 grid-margin stretch-card table-responsive">'.$APIRES.'</div>';
  $html .=  '<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title"></h4>
<div class="card-body-action">
  <button class="btn btn-primary" onclick="imageexportData(\'print_scorecardreport\')"> '.plus_get_string("print", "form").'</button>
  <button class="btn btn-primary" onclick="exportData(\'scorecardreport\')"> Export</button>
</div>                  
                  ';
  // $html .= '<pre>'.print_r($APIRES->data->scorecardquery, true).'</pre>';
  $html .=        '<div class="table-responsive" id="print_scorecardreport">
                      <p><img src="https://plus.fivestudents.com/wp-content/plugins/el-dashboard/public/images/Five-Students-Logo_big-1.webp" width="150" class="onlyprint"/></p>
                    <h1 class="card-title1 text-center">'.plus_get_string("ranking", "site").'</h1>
                    <p> <strong>'.plus_get_string("from", "form").' </strong>: '.$searchreq->startdate.'</p>
                    <p> <strong>'.plus_get_string("to", "form").' </strong>: '.$searchreq->enddate.'</p>
                    <table id="scorecardreport" class="table table-striped">
                      <thead>
                          <tr>
                            <th>'.plus_get_string("rank", "form").'</th>
                            <th>'.plus_get_string("lastname", "form").'</th>
                            <th>'.plus_get_string("firstname", "form").'</th>
                            '.($searchreq->charactername==1?'<th>'.plus_get_string("chartname", "form").'</th>':'').'
                            '.($searchreq->showgrades==1?'<th>'.plus_get_string("level", "form").'</th>':'').'
                            <th>'.plus_get_string("group", "form").'</th>
                            <th>XP</th>
                          </tr>
                          
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_object($APIRES->data)){
         // echo "<pre>"; print_r($APIRES);//die;
               /* echo "<pre>";
                print_r($APIRES->data['homework']);
                echo "</pre>";*/
                $rank=0;
                foreach ($APIRES->data->scorecard as $key => $user) {
                  $rank++;
    $html .=              '<tr><td>'.$rank.'</td><td>'.$user->lastname.'</td><td>'.$user->firstname.'</td>'.($searchreq->charactername==1?'<td>'.$user->alternatename.'</td>':'').($searchreq->showgrades==1?'<td>'.$user->grade.'</td>':'').'<td>'.$user->groupname.'</td>';
    $html .=              '<td>'.$user->totalscore.'</td></tr>';                  
                }
				$searchreq->total = $APIRES->data->total; 
				$searchreq->start = $APIRES->data->start;
                $searchreq->limit = $APIRES->data->limit;
              }        
            $html .=  '</tbody>
                    </table>
                  </div>';
 
if(empty($searchreq->limit)){$searchreq->limit = $searchreq->total;}
 $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "scorecard");
  $html .=      '</div>
              </div>
            </div> 
';
  $html .=  '</div>
            </div>
          </div><script src="'. __FILE__ .'/public/../../../vendors/select2/select2.min.js"></script><script src="'. __FILE__ .'/public/../../../js/select2.js"></script>';
$html.= ' <script> $(function(){
          var all_grade_level = '.json_encode($all_grade_level).';
          var allgroup=new Array();
       
          $("#gradelevel").change(function(){
              var gradelevelid = $(this).val();
              console.log("gradelevelid", gradelevelid);
              var selectedgroup = all_grade_level.filter(x => gradelevelid.includes(x.id));
              if(gradelevelid.includes("0")){
                selectedgroup = all_grade_level;
              }
              console.log("selectedgroup- ", selectedgroup)
              var newoptions = \'<option value="0">'.plus_get_string("all", "site").'</option>\';
              $.each(selectedgroup, function(key,grade){
                if(grade && Array.isArray(grade.group)){
                  allgroup += grade.group;
                  $.each( grade.group, function( key1, group ) {
                    newoptions += \'<option value="\'+group.id+\'">\'+group.name+\'</option>\'
                  });
                } else {
                  allgroup = [];
                }
              });
            $("#group").html(newoptions);
           
            });
});</script>';
  return $html;
}