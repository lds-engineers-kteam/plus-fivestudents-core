<?php
function plus_view_globalusers(){
  global $wp;
  if ( !is_user_logged_in() || !current_user_can('view_plusglobalusers')) {
    return plus_view_noaccess();
  }
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  $searchreq->name = plus_get_request_parameter("groupname", "");
  $APIRES = $MOODLE->get("BrowseGlobalUsers", null, $searchreq);
  $html ='';
  // $html .='<pre>'.print_r($APIRES, true).'</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("globalusers", "site").'</h4>
                  '.(current_user_can('view_plusaddglobaluser')?'<a class="btn btn-primary card-body-action" href="/add-global-users"><i class="mdi mdi-plus"></i></a>':'').'
                  <!--<form class="forms-sample">
                    <div class="form-group row">
                      <label for="name" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="groupname" class="form-control" id="name" placeholder="'.plus_get_string("name", "form").'" value="'.$searchreq->name.'">
                      </div>
                    </div>
                    <input type="hidden" name="start" value="0"/>
                    <input type="hidden" name="limit" value="10"/>
                    <button type="submit" name="filter" class="btn btn-primary mr-2">'.plus_get_string("search", "form").'</button>
                    <button type="submit" name="cancel" class="btn btn-light">'.plus_get_string("cancel", "form").'</button>
                  </form>-->
                  <div class="table-responsive">
                    <table class="table table-striped plus_local_datatable" id="globalusers">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("userid", "form").'</th>
                          <th>'.plus_get_string("firstname", "form").'</th>
                          <th>'.plus_get_string("lastname", "form").'</th>
                          <th>'.plus_get_string("email", "form").'</th>
                          <th>'.plus_get_string("schools", "site").'</th>
                          <th>'.plus_get_string("roles", "form").'</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_array($APIRES->data->users)){
                foreach ($APIRES->data->users as $key => $user) {
                  $html .=  '<tr>
                              <td class="py-1">'.$user->userid.'</td>
                              <td class="py-1">'.$user->firstname.'</td>
                              <td class="py-1">'.$user->lastname.'</td>
                              <td class="py-1">'.$user->email.'</td>
                              <td class="py-1">'.$user->institution.'</td>
                              <td class="py-1">'.$user->roles.'</td>
                              <td class="">'.(current_user_can('view_plusaddglobaluser')?'<a href="/add-global-users?id='.$user->id.'"><i class="mdi mdi-lead-pencil"></i> '.plus_get_string("edit", "form").'</a>':'').' '.(current_user_can('view_pluseditglobaluserteacher')?'<a href="/users-teacher/?id='.$user->id.'"><i class="mdi mdi-lead-pencil"></i> '.plus_get_string("teachers", "site").'</a>':'').'</td>
                              </tr>';
                }
              }
            $html .=  '</tbody>
                    </table>
                  </div>';
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '</div>
            </div>
          </div>';
  echo $html;
}



     

