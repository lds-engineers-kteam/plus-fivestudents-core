<?php
function main_footer() {
	global $CFG;
  
  echo  '
        <div class="pageloading"><img src="'.$CFG->wwwroot.'/images/ajax-loader-white.gif"></div>
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block"> © '.date("Y").'.  <a href="https://plus.fivestudents.com/" target="_blank">fivestudents.com</a></span>
          </div>
        </footer>

        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/jquery.min.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/jquery-migrate.min.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/el-dashboard-public.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/js/vendor.bundle.base.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/chart.js/Chart.min.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/datatables.net/jquery.dataTables.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/datatables.net/jquery.dataTables.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/dataTables.select.min.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/off-canvas.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/hoverable-collapse.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/template.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/settings.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/todolist.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/dashboard.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/Chart.roundedBarCharts.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/jquery-toast-plugin/jquery.toast.min.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/tinyPlayer/howler.min.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/tinymce/tinymce.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/vendors/plusplayer/plusplayer.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/language.js"></script>
        <script type="text/javascript" src="'.$CFG->wwwroot.'/js/plus.js"></script>
          
        </body>
      </html>
      ';
}

?>

