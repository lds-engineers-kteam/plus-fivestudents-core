<?php
function plus_view_print_password(){
 global $wp;
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
  
  $password_data=null;
  if(isset($_REQUEST['filter'])){
    $APIRES = $MOODLE->get("getGroupPassword", null, $searchreq);
    $password_data=$APIRES->data;
  }
  
  /*echo "<pre>";
  print_r($password_data);*/

  $APIRESGroup = $MOODLE->get("getGroupByInstituteId", null);
  $APIRESGrades = $MOODLE->get("GetGrades");
  $APIRESGradeLevel = $MOODLE->get("getGardeLevelData");
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
 //$APIRESWeeklyData=$MOODLE->get("getWeeklySchoolReports",null,$searchreq);
  $html='<link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2/select2.min.css">
  <link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
';
  // $html .= '<div class="table-responsive">'.is_object($APIRESWeeklyData)?json_encode($APIRESWeeklyData):$APIRESWeeklyData.'</div>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                <h4>'.plus_get_string("users", "student").'</h4>
                 <!-- <h4 class="card-title">'.plus_get_string("school_weekly_report", "site").'</h4>-->
                  
                  <form class="forms-sample" id="print-password-form">
                  <!--<div class="form-group row">
                      <label for="search" class="col-sm-2 col-form-label">'.plus_get_string("startdate", "site").'</label>
                      <div class="col-sm-10">
                         <input type="date" name="startdate" class="form-control" id="startdate" value="'.$searchreq->startdate.'">
                      </div>
                    </div>-->
                    <!--<div class="form-group row">
                      <label for="weekly" class="col-sm-2 col-form-label">'.plus_get_string("weeklydate", "site").'</label>
                      <div class="col-sm-10">
                         <input type="date" name="weekly" class="form-control" id="weekly" value="'.$searchreq->weekly.'">
                      </div>
                    </div>-->
                   
                  <!--  <div class="form-group row">
                      <label for="school" class="col-sm-2 col-form-label">'.plus_get_string("school", "site").'</label>
                      <div class="col-sm-10">
             <input type="text" name="school" class="form-control" id="school" value="'.$APIRESGrades->INSTITUTION->institution.'" disabled>
                        
                      </div>
                    </div>  -->     
          
          <div class="form-group row">
                      <label for="gradelevel" class="col-sm-2 col-form-label">'.plus_get_string("gradelevel", "site").'</label>
                      <div class="col-sm-10">
                        <select name="gradelevel[]" id="gradelevel" class="form-control" multiple>';
              $selected='';
              if(in_array(0, $searchreq->gradelevel)){
                $selected='selected';
              }
             // $html.='<option value="0" '.$selected.'>'.plus_get_string("all", "site").'</option>';
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
          <!--<div class="form-group row">
            <label for="showteacher" class="col-sm-2 col-form-label">'.plus_get_string("showteacher", "form").'</label>
            <div class="col-sm-10">
              <input type="checkbox" name="showteacher" '.($searchreq->showteacher?' checked ':'').' class="form-control1" id="showteacher" value="1">
            </div>
          </div>-->
          
          
          <!--<div class="form-group row">
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
                    </div>-->
          
          
          
          
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
  <button  class="btn btn-primary" onclick="htmltopdfexport(\'print_password\', \'User details\')"> '.plus_get_string("print", "form").'</button>
  <!--<button  class="btn btn-primary" onclick="exportData(\'weeklyreporttable\')"> Export</button>-->
</div>                  
                  ';
  $html .=        '<div class="table-responsive" id="print_password">';
                    if(isset($password_data)){
                      $counter = 0;
                        $printcontent = '<div class="cutterpage">';
                      foreach($password_data as $row){
                        if($counter!=0 && $counter%24 == 0){
                          $printcontent .= '</div><div class="cutterpage">';
                        }
          $printcontent .= '<p  class="print-password-data box">
                    
                      <span>'.plus_get_string("firstname", "student").': </span>
                      <span>'.$row->firstname.'</span>
                     <br/> 
                      <span>'.plus_get_string("lastname", "student").': </span>
                      <span>'.$row->lastname.'</span>
                     <br/> 
                      <span>'.plus_get_string("group", "site").': </span>
                      <span>'.$row->groupname.'</span>
                     <br/> 
                      <span>'.plus_get_string("username", "student").':</span>
                      <span>'.$row->username.'</span>
                     <br/> 
                      <span>'.plus_get_string("password", "student").': </span>
                      <span>'.$row->password.'</span><br/><small>Site : https://web.fivestudents.com</small> 
          </p>';              
          $counter++;

                      }
                        $printcontent .= '</div>';
                        $html .= $printcontent;
                    }
                    
  $html .=        '</div>';
  //$html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "user");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '
 
  </div>
            </div>
          </div><script src="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2/select2.min.js"></script><script src="'.plugin_dir_url( __FILE__ ).'/public/../../../js/select2.js"></script>';
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