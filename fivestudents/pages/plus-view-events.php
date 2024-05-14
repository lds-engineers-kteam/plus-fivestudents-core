<?php
function plus_view_events(){
  global $CFG;
  require_once($CFG->dirroot . '/api/moodlecall.php');
  
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager($current_user);
  $searchreq = new stdClass();
  $searchreq->name = plus_get_request_parameter("groupname", "");
  $searchreq->teacher = plus_get_request_parameter("teacher", "");
  $searchreq->createddatefrom = plus_get_request_parameter("createddatefrom", "");
  $searchreq->createddateto = plus_get_request_parameter("createddateto", "");
  $searchreq->start = plus_get_request_parameter("start", 0);
  $searchreq->limit = plus_get_request_parameter("limit", 10);
  $searchreq->total = 0;
  $APIRES = $MOODLE->get("BrowseEvents", null, $searchreq);
  $html='';
  
  // $html.='<pre>'.print_r($APIRES, true).'</pre>';
  // $formdata->createddatefrom
  // $formdata->createddateto

  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("events", "site").'</h4>
                  <a class="btn btn-primary card-body-action" href="'.$CFG->wwwroot.'/add-event"><i class="mdi mdi-plus"></i></a>
                  <form class="forms-sample">
                    <div class="form-group row">
                      <label for="name" class="col-sm-2 col-form-label">'.plus_get_string("name", "form").'</label>
                      <div class="col-sm-10">
                        <input type="text" name="groupname" class="form-control" id="name" placeholder="'.plus_get_string("name", "form").'" value="'.$searchreq->name.'">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="createddatefrom" class="col-sm-2 col-form-label">'.plus_get_string("from", "form").' *</label>
                      <div class="col-sm-10">
                        
                        <input type="datetime-local" name="createddatefrom" class="form-control" id="createddatefrom" value="">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="createddateto" class="col-sm-2 col-form-label">'.plus_get_string("to", "form").' *</label>
                      <div class="col-sm-10">
                       
                        <input type="datetime-local" name="createddateto" class="form-control" id="createddateto" value="">
                      </div>
                    </div>
                    <input type="hidden" name="start" value="0"/>
                    <input type="hidden" name="limit" value="10"/>
                    <button type="submit" name="filter" class="btn btn-primary mr-2">'.plus_get_string("search", "form").'</button>
                    <a href="'.$CFG->wwwroot.'/events/" class="btn btn-light">'.plus_get_string("cancel", "form").'</a>
                  </form>
                </div>
              </div>
            </div>';
  $html .=  '<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title"></h4>';
  $html .=        '<div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("name", "form").'</th>
                          <th>'.plus_get_string("school", "site").'</th>
                          <th>'.plus_get_string("teacher", "form").'</th>
                          <th>'.plus_get_string("startdate", "site").'</th>
                          <th>'.plus_get_string("enddate", "site").'</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_array($APIRES->data->events)){
                foreach ($APIRES->data->events as $key => $event) {
                  $html .=  '<tr>
                              <td class="py-1">'.$event->name.'</td>
                              <td class="py-1">'.$event->institution.'</td>
                              <td class="py-1">'.($event->teacher?:plus_get_string("all", "site")).'</td>
                              <td class="">'.plus_dateToFrench($event->timestart).'</td>
                              <td class="">'.plus_dateToFrench($event->timeend).'</td>
                              <td class="">'.(current_user_can('view_pluseditevent')?'<a href="'.$CFG->wwwroot.'/add-event?id='.$event->id.'"><i class="mdi mdi-lead-pencil"></i> '.plus_get_string("edit", "form").'</a>':'').'</td>
                              </tr>';
                }
                $searchreq->total = $APIRES->data->total;
                $searchreq->start = $APIRES->data->start;
                $searchreq->limit = $APIRES->data->limit;
              } else {
    $html .=              '<tr><td colspan="6" class="text-center">'.plus_get_string("norecordfound", "form").'</td></tr>';
              }
            $html .=  '</tbody>
                    </table>
                  </div>';
  $html .=      plus_pagination($searchreq->start, $searchreq->limit, $searchreq->total, "group");
  $html .=      '</div>
              </div>
            </div>
';
  $html .=  '</div>
            </div>
          </div>';

  return $html;
}



     

