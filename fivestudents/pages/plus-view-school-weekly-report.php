<?php
function plus_school_weekly_report(){
  global $wp,$CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }
  $searchreq->startdate = plus_get_request_parameter("startdate", "");
  $searchreq->enddate = plus_get_request_parameter("enddate", "");
  $searchreq->weekly = plus_get_request_parameter("weekly", date("Y-m-d"));
  $searchreq->showteacher = plus_get_request_parameter("showteacher", 0);
  $searchreq->school = plus_get_request_parameter("school", "");
  $searchreq->gradelevel = plus_get_request_parameter("gradelevel", array());
  $searchreq->group = plus_get_request_parameter("group", array());
  $searchreq->start = plus_get_request_parameter("start", 0);
  $searchreq->limit = plus_get_request_parameter("limit", 10);
  $searchreq->total = 0;
  
  $APIRES = $MOODLE->get("BrowseUsers", null, $searchreq);
  $APIRESGroup = $MOODLE->get("getGroupByInstituteId", null);
  $APIRESGrades = $MOODLE->get("GetGrades");
  $APIRESGradeLevel = $MOODLE->get("get_garde_level_data");
  $all_grade_level=array();
  $all_selctedgrouplevel=array();
  if(!empty($searchreq->weekly)){
    if(date('w', strtotime($searchreq->weekly)) === '1'){
      $monday=date('Y-m-d', strtotime($searchreq->weekly));
      $sunday=date('Y-m-d', strtotime('next sunday', strtotime($monday)));
      $searchreq->startdate=$monday;
      $searchreq->enddate =$sunday;

    }else{
      $monday=date('Y-m-d', strtotime('previous monday', strtotime($searchreq->weekly)));
      $sunday=date('Y-m-d', strtotime('next sunday', strtotime($monday)));
      $searchreq->startdate=$monday;
      $searchreq->enddate =$sunday;
    }
  }
 $APIRESWeeklyData=$MOODLE->get("getWeeklySchoolReports",null,$searchreq);
  $html='<link rel="stylesheet" href="'. __FILE__ .'/public/../../../vendors/select2/select2.min.css">
  <link rel="stylesheet" href="'. __FILE__ .'/public/../../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
';
  // $html .= '<div class="table-responsive">'.is_object($APIRESWeeklyData)?json_encode($APIRESWeeklyData):$APIRESWeeklyData.'</div>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("school_weekly_report", "site").'</h4>
                  
                  <form class="forms-sample">
                  <!--<div class="form-group row">
                      <label for="search" class="col-sm-2 col-form-label">'.plus_get_string("startdate", "site").'</label>
                      <div class="col-sm-10">
                         <input type="date" name="startdate" class="form-control" id="startdate" value="'.$searchreq->startdate.'">
                      </div>
                    </div>-->
                    <div class="form-group row">
                      <label for="weekly" class="col-sm-2 col-form-label">'.plus_get_string("weeklydate", "site").'</label>
                      <div class="col-sm-10">
                         <input type="date" name="weekly" class="form-control" id="weekly" value="'.$searchreq->weekly.'">
                      </div>
                    </div>
                   
                  <!--  <div class="form-group row">
                      <label for="school" class="col-sm-2 col-form-label">'.plus_get_string("school", "site").'</label>
                      <div class="col-sm-10">
					   <input type="text" name="school" class="form-control" id="school" value="'.$APIRESGrades->INSTITUTION->institution.'" disabled>
                        
                      </div>
                    </div>	-->			
					
					<div class="form-group row">
                      <label for="gradelevel" class="col-sm-2 col-form-label">'.plus_get_string("gradelevel", "site").'</label>
                      <div class="col-sm-10">
                        <select name="gradelevel[]" id="gradelevel" class="form-control" multiple>';
              $selected='';
              if(in_array(0, $searchreq->gradelevel)){
                $selected='selected';
              }
              $html.='<option value="0" '.$selected.'>'.plus_get_string("all", "site").'</option>';
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
                      <label for="group" class="col-sm-2 col-form-label">'.plus_get_string("group", "site").'</label>
                      <div class="col-sm-10">
						<select class="form-control" id="group" name="group[]" multiple>';
              $selected='';
              if(in_array(0, $searchreq->group)){
                $selected='selected';
              }
              $html.='<option value="0" '.$selected.'>'.plus_get_string("all", "site").'</option>';
              foreach ($all_selctedgrouplevel as $key => $selctedgrouplevel) {
                if(sizeof($selctedgrouplevel->group)>0){
  						    foreach($selctedgrouplevel->group as $group_row){
      							$selected='';
                    if(!empty($searchreq->group)){
                        if(in_array($group_row->id,$searchreq->group)){
                        $selected='selected';
                      }
                    }
      							$html.='<option value="'.$group_row->id.'" '.$selected.'>'.$group_row->name.'</option>';
  						    }
                }
              }

					   $html.= '</select>	
                      </div>
                    </div>
					<div class="form-group row">
            <label for="showteacher" class="col-sm-2 col-form-label">'.plus_get_string("showteacher", "form").'</label>
            <div class="col-sm-10">
              <input type="checkbox" name="showteacher" '.($searchreq->showteacher?' checked ':'').' class="form-control1" id="showteacher" value="1">
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
  <button  class="btn btn-primary" onclick="imageexportData(\'print_weeklyreporttable\')"> '.plus_get_string("print", "form").'</button>
  <button  class="btn btn-primary" onclick="exportData(\'weeklyreporttable\')"> Export</button>
</div>                  
                  ';
  $html .=        '<div class="table-responsive" id="print_weeklyreporttable">
                    <table class="table table-striped" id="weeklyreporttable">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("school", "site").'</th>
                          <th>'.plus_get_string("gradelevel", "site").'</th>
                          <th>'.plus_get_string("group", "site").'</th>
                          '.($searchreq->showteacher?'<th>'.plus_get_string("teachers", "site").'</th>':'').'
                          <th>'.plus_get_string("startdate", "site").'</th>
                          <th>'.plus_get_string("enddate", "site").'</th>
                          <th class="text-center" >'.plus_get_string("sub_students_pst", "site").'</th>
                          <th class="text-center">'.plus_get_string("no_completed_homework", "site").'</th>
                          <th class="text-center">'.plus_get_string("no_completed_homework_and_pst", "site").'</th>
                          <th class="text-center">'.plus_get_string("no_completed_successful_and_pst", "site").'</th>                     
                          <th class="text-center">'.plus_get_string("totaluserhomeworkcompleted", "form").'</th>                     
                    
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRESWeeklyData) && is_array($APIRESWeeklyData->data->records)){
                foreach ($APIRESWeeklyData->data->records as $key => $record) {
                  $html .=  '<tr>
                              <td class="py-1">'.$record->school.'</td>
                              <td class="">'.$record->gradelevel.'</td>
                              <td class="">'.$record->groupname.'</td>
                              '.($searchreq->showteacher?'<td class="">'.$record->teachers.'</td>':'').'
                              <td class="">'.plus_dateToFrench($record->startdate).'</td>
                              <td class="">'.plus_dateToFrench($record->enddate).'</td>
                              <td class="text-center '.($record->totalstudentpercent>=85?' blue ':($record->totalstudentpercent>=70?' green ':($record->totalstudentpercent>=50?' yellow ':' gray'))).'">'.$record->totalstudent.' ('.$record->totalstudentpercent.'%)</td>
                              <td class="text-center '.($record->plannedhomework>=3?' blue ':($record->plannedhomework>=2?' green ':($record->plannedhomework>=1?' yellow ':' gray'))).'">'.$record->plannedhomework.'</td>
                              <td class="text-center '.($record->completedpercent>=85?' blue ':($record->completedpercent>=70?' green ':($record->completedpercent>=50?' yellow ':' gray'))).'">'.$record->completed.' ('.$record->completedpercent.'%)</td>
                              <td class="text-center '.($record->passedpercent>=85?' blue ':($record->passedpercent>=70?' green ':($record->passedpercent>=50?' yellow ':' gray'))).'">'.$record->passed.' ('.$record->passedpercent.'%)</td>
                              <td class="text-center white">'.$record->totalusercompleted.'</td>
                            </tr>';
                }
                $searchreq->total = $APIRESWeeklyData->data->total; 
                $searchreq->start = $APIRESWeeklyData->data->start;
                $searchreq->limit = $APIRESWeeklyData->data->limit;
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
  $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "user");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '
 
  </div>
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
   echo $html;
}