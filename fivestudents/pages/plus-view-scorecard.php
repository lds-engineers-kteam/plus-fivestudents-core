<?php
function plus_view_scorecard(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager();
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
  $searchreq->limit = plus_get_request_parameter("limit", 10);
  $searchreq->total = 0;
  $APIRES = $MOODLE->get("get_teachers_scorecard",null,$searchreq); 
  $APIRES22 = $MOODLE->get("getGroupByInstituteId", null);
  $APIRESGrades = $MOODLE->get("GetGrades");
  $APIRESGradeLevel = $MOODLE->get("get_garde_level_data");
  $all_grade_level=array();
  $selctedgrouplevel=new stdClass();
  $selctedgrouplevel->group=array();
// print_r($APIRES);
// die;
  $html='<link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2/select2.min.css">
  <link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
';

  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">'.plus_get_string("scorecard", "site").'</h4>
                  <form class="forms-sample">
                   
                    <div class="form-group row">
                    <label for="startdate" class="col-sm-2 col-form-label">'.plus_get_string("startdate", "site").'</label>
                    <div class="col-sm-10">
                    <input type="date" name="startdate" class="form-control" id="startdate" value="'.$searchreq->startdate.'">
                    </div>
                    </div>
                    <div class="form-group row">
                    <label for="enddate" class="col-sm-2 col-form-label">'.plus_get_string("enddate", "site").'</label>
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
                $selctedgrouplevel=$grades_row;
							}
							$html.='<option value="'.$grades_row->id.'" '.$selected.'>'.$grades_row->name.'</option>';
						  }

					   $html.= '</select>	
                      </div>
                    </div>
          <div class="form-group row">
                      <label for="group" class="col-sm-2 col-form-label">'.plus_get_string("limit", "form").'</label>
                      <div class="col-sm-10">
            <select class="form-control" name="limit">
              <option value="10" '.($searchreq->limit==10?'selected':'').' >10 '.plus_get_string("lines", "form").'</option>
              <option value="20" '.($searchreq->limit==20?'selected':'').' >20 '.plus_get_string("lines", "form").'</option>
              <option value="50" '.($searchreq->limit==50?'selected':'').' >50 '.plus_get_string("lines", "form").'</option>
              <option value="100" '.($searchreq->limit==100?'selected':'').' >100 '.plus_get_string("lines", "form").'</option>
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
  <button  class="btn btn-primary" onclick="exportData(\'scorecardreport\')"> Export</button>
</div>                  
                  ';
  $html .=        '<div class="table-responsive">
                    <table id="scorecardreport" class="table table-striped">
                      <thead>
                        <tr>
						  <th>'.plus_get_string("teachername", "site").'</th>
                          <th>'.plus_get_string("status", "form").'</th>
						  <th>'.plus_get_string("gradelevel", "site").'</th>
						  <th>'.plus_get_string("startdate", "site").'</th>
						  <th>'.plus_get_string("enddate", "site").'</th>
                          <th>'.plus_get_string("score", "site").'</th>
                          
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_object($APIRES->data)){
         // echo "<pre>"; print_r($APIRES);//die;
               /* echo "<pre>";
                print_r($APIRES->data['homework']);
                echo "</pre>";*/
                foreach ($APIRES->data->scorecard as $key => $scorecard) {
      $html .=         '<tr><td>'.$scorecard->firstname .' ' .$scorecard->lastname.'</td>	  
                        <td>'.$scorecard->level.'</td>
                        <td>'.$scorecard->name.'</td>
						<td>'.$searchreq->startdate.'</td>
                        <td>'.$searchreq->enddate.'</td>
                        <td>'.$scorecard->totalscore.'</td>
                       
                        
                        </tr>';                  
                  
                }
				$searchreq->total = $APIRES->data->total; 
				$searchreq->start = $APIRES->data->start;
                $searchreq->limit = $APIRES->data->limit;
              }        
            $html .=  '</tbody>
                    </table>
                  </div>';
 

 $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "scorecard");
  $html .=      '</div>
              </div>
            </div> 
';
  $html .=  '</div>
            </div>
          </div><script src="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2/select2.min.js"></script><script src="'.plugin_dir_url( __FILE__ ).'/public/../../../js/select2.js"></script>';
  return $html;
}