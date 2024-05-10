<?php
function plus_view_users(){
  global $wp;
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  $generatemonthlyreport = plus_get_request_parameter("generatemonthlyreport", 0);
  $institutionid = plus_get_request_parameter("institutionid", 0);
  if(!empty($generatemonthlyreport) && !empty($institutionid)){
    $APIRES = $MOODLE->get("generaterMonthlyReport", null, array("institutionid"=>$institutionid));
    plus_redirect(home_url()."/users/");
    exit;
  }

  if(isset($_REQUEST['cancel'])){
    plus_redirect(home_url( $wp->request ));
    exit;
  }
  $searchreq->search = plus_get_request_parameter("search", "");
  $searchreq->username = plus_get_request_parameter("username", "");
  $searchreq->email = plus_get_request_parameter("email", "");
  $searchreq->phone = plus_get_request_parameter("phone", "");
  $searchreq->accounttype = plus_get_request_parameter("accounttype", "");
  $searchreq->start = plus_get_request_parameter("start", 0);
  $searchreq->limit = plus_get_request_parameter("limit", 10);
  $searchreq->total = 0;
  $APIRES = $MOODLE->get("BrowseUsers", null, $searchreq);
  // if(!is_string($APIRES)){
  //   $APIRES = json_encode($APIRES);
  // }
  $html='<link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2/select2.min.css">
  <link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
';
  // $html .= '<pre>'.print_r($APIRES, true).'</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("schools", "site").'</h4>
                  <div class="card-body-action">
                    <a class="btn btn-primary" href="/new-accountant"><i class="mdi mdi-plus mr-2"></i>Add new accountant</a>
                    <a class="btn btn-warning text-white p-3 ml-3" href="/adduser"><i class="mdi mdi-plus"></i> Add Institute</a>
                  </div>
                  <form class="forms-sample">
                    <div class="form-group row">
                      <label for="search" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="search" class="form-control" id="search" placeholder="'.plus_get_string("name", "form").'" value="'.$searchreq->search.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="email" class="col-sm-2 col-form-label">'.plus_get_string("email", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="email" class="form-control" id="email" placeholder="'.plus_get_string("email", "form").'" value="'.$searchreq->email.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="phone" class="col-sm-2 col-form-label">'.plus_get_string("phonenumber", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="phone" class="form-control" id="phone" placeholder="'.plus_get_string("phonenumber", "form").'" value="'.$searchreq->phone.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="accounttype" class="col-sm-2 col-form-label">'.plus_get_string("accounttype", "form").'</label>
                      <div class="col-sm-10">
                        <select name="accounttype" id="accounttype" class="form-control">
                          <option value="" '.($searchreq->accounttype == ''?'selected':'').'>All</option>
                          <option value="internaladmin" '.($searchreq->accounttype == 'internaladmin'?'selected':'').'>Internal Admin</option>
                          <option value="schooladmin" '.($searchreq->accounttype == 'schooladmin'?'selected':'').'>School Admin</option>
                          <option value="tutoringcenter" '.($searchreq->accounttype == 'tutoringcenter'?'selected':'').'>Tutoring Center</option>
                          <option value="tutor" '.($searchreq->accounttype == 'tutor'?'selected':'').'>Tutor</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="limit" class="col-sm-2 col-form-label">Limit</label>
                      <div class="col-sm-10">
                        <select name="limit" id="limit" class="form-control">
                          <option value="10" '.($searchreq->limit == '10'?'selected':'').'>10</option>
                          <option value="20" '.($searchreq->limit == '20'?'selected':'').'>20</option>
                          <option value="50" '.($searchreq->limit == '50'?'selected':'').'>50</option>
                          <option value="100" '.($searchreq->limit == '100'?'selected':'').'>100</option>
                        </select>
                      </div>
                    </div>
                    <input type="hidden" name="start" value="0"/>
                    <button type="submit" name="filter" class="btn btn-primary mr-2">Afficher</button>
                    <button type="submit" name="cancel" class="btn btn-light">Annuler</button>
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
                          <th>'.plus_get_string("schools", "site").'</th>
                          <th>'.plus_get_string("accounttype", "form").'</th>
                          <th>'.plus_get_string("email", "form").'</th>
                          <th>'.plus_get_string("address", "form").'</th>
                          <th>'.plus_get_string("phonenumber", "form").'</th>
                          <th>'.plus_get_string("contactname", "form").'</th>
                          <th>'.plus_get_string("jobtitle", "form").'</th>
                          <th>'.plus_get_string("datecreation", "form").'</th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_array($APIRES->data->users)){
                foreach ($APIRES->data->users as $key => $user) {
                  $html .=  '<tr>
                              <td class="py-1">'.$user->institution.'</td>
                              <td class="">'.$user->role.'</td>
                              <td class="">'.$user->email.'</td>
                              <td class="">'.$user->address.'</td>
                              <td class="">'.$user->phone.'</td>
                              <td class="">'.$user->contactname.'</td>
                              <td class="">'.$user->jobtitle.'</td>
                              <td class="">'.plus_dateToFrench($user->timecreated, "d F Y h:i A").'</td>
                              <td class=""><a href="/adduser?id='.$user->id.'"><i class="mdi mdi-lead-pencil"></i> '.plus_get_string("edit", "form").'</a></td>
                              <td class=""><a href="/subscription-setting?id='.$user->institutionid.'">'.plus_get_string("title", "subscription").'</a></td>
                              <td><a href="/users/?generatemonthlyreport=1&institutionid='.$user->institutionid.'"> '.plus_get_string("generatemonthlyreport", "form").' </a></td>
                              <td><a href="/devices-list/?id='.$user->institutionid.'"> '.plus_get_string("devices", "site").' </a></td>
                              <td><a href="/disable-courses/?id='.$user->institutionid.'"> '.plus_get_string("title", "disablecourse").' </a></td>
                              <td><a href="/enable-courses/?id='.$user->institutionid.'"> '.plus_get_string("title", "enablecourse").' </a></td>
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
  $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "user");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '</div>
            </div>
          </div><script src="'.plugin_dir_url( __FILE__ ).'/public/../../../vendors/select2/select2.min.js"></script><script src="'.plugin_dir_url( __FILE__ ).'/public/../../../js/select2.js"></script>';
  return $html;
}