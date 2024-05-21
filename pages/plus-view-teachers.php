<?php
function plus_view_teachers(){
  global $CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');

  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();

  $current_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

  if(isset($_REQUEST['cancel'])){
    plus_redirect($current_url);
    exit;
  }
  $searchreq->type = plus_get_request_parameter("type", "");
  $searchreq->name = plus_get_request_parameter("teachername", "");
  $searchreq->email = plus_get_request_parameter("email", "");
  $searchreq->createddatefrom = plus_get_request_parameter("createddatefrom", "");
  $searchreq->createddateto = plus_get_request_parameter("createddateto", "");
  $searchreq->start = plus_get_request_parameter("start", 0);
  $searchreq->limit = plus_get_request_parameter("limit", 10);
  $searchreq->total = 0;
  $APIRES = $MOODLE->get("BrowseTeachers", null, $searchreq);

  $html='';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("teachers", "site").'</h4>
                  '.(current_user_can('plus_addteacher')?'<a class="btn btn-primary card-body-action" href="'.$CFG->wwwroot.'/add-teacher"><i class="mdi mdi-plus"></i></a>':'').'
                  <form class="forms-sample">
                    <!--<div class="form-group row">
                      <label for="type" class="col-sm-2 col-form-label">Type</label>
                      <div class="col-sm-10">
                      <select name="type" id="type" class="form-control">
                      <option value="" '.($searchreq->type == ''?'selected':'').'>All</option>
                      <option value="teacher" '.($searchreq->type == 'teacher'?'selected':'').'>Teacher</option>
                      <option value="student" '.($searchreq->type == 'student'?'selected':'').'>Student</option>
                      </select>
                      </div>
                    </div>-->
                    <div class="form-group row">
                      <label for="teachername" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="teachername" class="form-control" id="teachername" placeholder="'.plus_get_string("name", "form").'" value="'.$searchreq->name.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="email" class="col-sm-2 col-form-label">'.plus_get_string("email", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="email" class="form-control" id="email" placeholder="'.plus_get_string("email", "form").'" value="'.$searchreq->email.'">
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
                          <th>'.plus_get_string("firstname", "form").'</th>
                          <th>'.plus_get_string("lastname", "form").'</th>
                          <th>'.plus_get_string("email", "form").'</th>
                          <th>'.plus_get_string("creationdate", "form").'</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_array($APIRES->data->users)){
                foreach ($APIRES->data->users as $key => $teacher) {
                  $html .=  '<tr>
                              <td class="py-1">'.$teacher->firstname.'</td>
                              <td class="">'.$teacher->lastname.'</td>
                              <td class="">'.$teacher->email.'</td>
                              <td class="">'.plus_dateToFrench($teacher->timecreated, "d F Y h:i A").'</td>
                              <td class="">'.(current_user_can('plus_addteacher')?'<a href="'.$CFG->wwwroot.'/add-teacher?id='.$teacher->id.'"><i class="mdi mdi-lead-pencil"></i> '.plus_get_string("edit", "form").'</a>':'').'</td>
                            </tr>';
                }
                $searchreq->total = $APIRES->data->total; 
                $searchreq->start = $APIRES->data->start;
                $searchreq->limit = $APIRES->data->limit;
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
  $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "teacher");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '</div>
            </div>
          </div>';
  return $html;
}