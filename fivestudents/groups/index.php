<?php
  require_once("../config.php");
  require_login();
  $syncnow = optional_param("syncnow", 0);
  if($syncnow){
    syncAllGroups();
    redirect("{$CFG->wwwroot}/groups/");
  }
  $OUTPUT->loadjquery();
  $groupdata = get_allgroups();
  $lastsynced = get_string("lastfetched",'form');
  if(is_object($groupdata) && !empty($groupdata->lastsynced)){
    $lastsynced = plus_dateToFrench($groupdata->lastsynced);
  }
  echo $OUTPUT->header();
  $html = '';
  $html .= '<div class="row">
            <div class="col-12 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">'.get_string("groups",'site').' <span class="badge">'.get_string("lastsynced",'site').': '.$lastsynced.'</span></p>
                  <div class="text-right">
                  '.(has_internet()?'<a class="btn btn-primary" href="'.$CFG->wwwroot.'/groups?syncnow=1">'.get_string("syncnow",'site').'</a>':'').'
                  </div>
                  <br/>
                  <div class="table-responsive">
                    <table id="userlist" class="table plus_local_datatable table-borderless">
                      <thead>
                        <tr>
                          <th>'.get_string("action", "table").'</th>
                          <th>'.get_string('name', 'form').'</th>
                          <th>'.get_string('gradelevel', 'site').'</th>
                          <th>'.get_string('matter', 'form').'</th>
                          <th>'.get_string('teacher', 'form').'</th>
                          <th>'.get_string('noofstudent', 'form').'</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>';
  if(is_object($groupdata) && is_array($groupdata->groups)){
    foreach ($groupdata->groups as $key => $group) {
      $categoryid = $group->categoryid;
      if(isset($USER->disabledcourses) && isset($USER->disabledcourses->$categoryid) && is_array($USER->disabledcourses->$categoryid)){
        $courseids = explode(",", $group->courseid);
        $disabledcourse = array_intersect($USER->disabledcourses->$categoryid, $courseids);
        if(sizeof($disabledcourse) == sizeof($courseids)){
          continue;
        }
      }
      if(!in_array($group->id, $USER->groupids)){continue;}
      $html .=  '<tr>
                  <td class="p-1 text-center"><a href="'.$CFG->wwwroot.'/groups/details?id='.$group->id.'" style="font-size:32px;"><i class="mdi mdi-magnify"></i></a></td>
                  <td class="py-1">'.$group->name.'</td>
                  <td class="py-1">'.$group->grade.'</td>
                  <td class="py-1">'.$group->coursename.'</td>
                  <td class="">'.$group->teachers.'</td>
                  <td class="">'.$group->totalusers.'</td>
                  <td class="">
                    <a href="'.$CFG->wwwroot.'/groups/details?id='.$group->id.'"> '.get_string("details", "form").' </a> &nbsp; &nbsp;
                  </td>
                </tr>';
    }
  }
  $html .='
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>';
  $html .='';
  echo $html;
  echo $OUTPUT->footer();
