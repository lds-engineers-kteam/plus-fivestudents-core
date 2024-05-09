<?php
function plus_view_resourcedetails(){
  global $wp;
  if ( !is_user_logged_in() || !current_user_can('view_plusresources')) {
    return plus_view_noaccess();
  }
  $current_user = wp_get_current_user();
  $current_user = wp_get_current_user();
  $searchreq = new stdClass();
  $searchreq->id = plus_get_request_parameter("id", "");
  $html='';
  $html .='<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style type="text/css">
  div#resourcedetails {
  /*
  min-height: 300px;
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  width: 100%;
  justify-content: center;
  align-items: center;
  transtion:all .5s;
  */
  }

  div#resourcedetails>img {
    width: 100%;
    height: auto;
    border: 1px solid;
    border-radius: 20px;
    margin: 20px 0px;
    padding: 0px;
    max-width: 100%;
}

</style>
  ';
  $html .= '<div class="row">
              <div class="col-md-12 grid-margin transparent">
                <div class="row">
                  <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                      <div class="card-body">
                        <h4 class="card-title" id="activity_name"></h4>
                        <nav aria-label="breadcrumb" id="resource_breadcrumb"></nav>
                        <div id="activitydetails" data-id="'.$searchreq->id.'" data-page="0" data-resource="0"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

<script>
$(document).ready(function(){
  loadResourceDetails();
});
$(document).on("click", ".nextresporce", function(){
  loadNextResourceDetails();
});
$(document).on("click", ".preresporce", function(){
  console.log("ok------");
  loadpreResourceDetails();
});
</script>
';
  return $html;
}



     

