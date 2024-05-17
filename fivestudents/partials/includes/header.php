<?php
function main_header() {
  global $CFG;

  echo '<!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>Fivestudents Admin</title>
            <link rel="shortcut icon" href="'.$CFG->wwwroot.'/images/logo-mini.png" />
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/feather/feather.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/ti-icons/css/themify-icons.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/css/vendor.bundle.base.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/mdi/css/materialdesignicons.min.css?ver=1.0.0" type="text/css" media="all" />
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/jquery-toast-plugin/jquery.toast.min.css?ver=1.0.0" type="text/css" media="all" />
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/js/select.dataTables.min.css" type="text/css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/css/vertical-layout-light/style.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/calendar/calender_script/jquery-ui.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/calendar/calender_script/fullcalendar.min.css" />
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/css/style.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/select2/select2.min.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/tinyPlayer/tinyPlayer.min.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/vendors/plusplayer/css/stylised.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/css/el-dashboard-public.css">
            <link rel="stylesheet" href="'.$CFG->wwwroot.'/css/style.min.css">
            
          </head>
        <body>
      ';
}



