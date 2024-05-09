<?php
function plus_view_surveys(){
  global $wp;
  if ( !is_user_logged_in() || !current_user_can('view_plussurveys')) {
    return plus_view_noaccess();
  }
  $MOODLE = new MoodleManager();
  $formdata = new stdClass();
  $formdata->id = plus_get_request_parameter("id", 0);
  $formdata->name = plus_get_request_parameter("groupname", "");
  $formdata->clone = plus_get_request_parameter("clone", 0);
  if(current_user_can('view_pluseditsurvey') && $formdata->clone ==1 && !empty($formdata->id)){
    $CLONERES = $MOODLE->get("cloneSurveys", null, $formdata);
    plus_redirect(home_url()."/surveys");
    exit;

  }

  $APIRES = $MOODLE->get("BrowseSurveys", null, $formdata);
  $html ='';
  // $html .='<pre>'.print_r($APIRES, true).'</pre>';
  $html .=  '<div class="row">
            <div class="col-md-12 grid-margin transparent">
              <div class="row">';
  $html .=  '<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body haveaction">
                  <h4 class="card-title">'.plus_get_string("surveys", "site").'</h4>
                  '.(current_user_can('view_pluseditsurvey')?'<a class="btn btn-primary card-body-action" href="/add-survey"><i class="mdi mdi-plus"></i></a>':'').'
                  <div class="table-responsive">
                    <table class="table table-striped plus_local_datatable" id="surveys">
                      <thead>
                        <tr>
                          <th>'.plus_get_string("id", "form").'</th>
                          <th>'.plus_get_string("name", "form").'</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
              if(is_object($APIRES) && is_array($APIRES->data->surveys)){
                foreach ($APIRES->data->surveys as $key => $survey) {
                  $html .=  '<tr>
                              <td class="py-1">'.$survey->id.'</td>
                              <td class="py-1">'.$survey->name.'</td>
                              <td class="">'.(current_user_can('view_pluseditsurvey')?'<a href="/add-survey?id='.$survey->id.'"><i class="mdi mdi-lead-pencil"></i> '.plus_get_string("edit", "form").'</a>':'').'</td>
                              <td class="">'.(current_user_can('view_pluseditsurvey')?'<a href="/surveys?id='.$survey->id.'&clone=1"><i class="mdi mdi-lead-copy"></i> '.plus_get_string("copy", "form").'</a>':'').'</td>
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
  return $html;
}



     

