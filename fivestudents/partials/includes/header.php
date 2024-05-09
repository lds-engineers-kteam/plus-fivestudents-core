<?php
function footer(){
	return '
<div class="pageloading"><img src="/wp-content/plugins/el-dashboard/public/images/ajax-loader-white.gif"></div>
  <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">'.plus_get_string("copyright", "footer").' © '.date("Y").'.  <a href="https://plus.fivestudents.com/" target="_blank">fivestudents.com</a></span>
            <!--<span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">made with <i class="ti-heart text-danger ml-1"></i></span>-->
          </div>
        </footer>';
}