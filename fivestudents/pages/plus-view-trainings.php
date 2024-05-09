<?php
function plus_view_trainings()
{
  global $wp;
  if (!is_user_logged_in() || !current_user_can('view_plusresources')) {
    return plus_view_noaccess();
  }
  $current_user = wp_get_current_user();
  $MOODLE = new MoodleManager();
  $searchreq = new stdClass();
  $searchreq->t = plus_get_request_parameter("t", "");
  $APIRES = $MOODLE->get("Browsetrainings", null, $searchreq);
  $html = '';
  $apidata = $APIRES->data->moduletree;
  $reshtml = '';
  $found = false;
  if ($APIRES->code == 200 && !empty($APIRES->data)) {
    $course = $APIRES->data;
  }
  $reshtml .= '
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .zoom-live-parent {
      width: 100%;
      height:100vh;
    }
    .zoom-live-parent .left-col {
      flex-direction : column;
      width: 70%;
      background-color: #f6eeee61;
      text-align: left;
      height:100vh;
      min-height: 500px;
      overflow-y: auto;
    }
    .zoom-live-parent .right-col {
      width: 30%;
    }
    #accordian {
      background: #fff;
      width: 100%;
      margin: 0px 10px 0 10px;
      height:100%;
      color: white;
      box-shadow: 0 0px 1px 0px rgba(0, 0, 0, 0.6);
    }
    #accordian h3 {
  		background: #fff;
      color: #000;
      margin-top: 0px;
      margin-bottom: 10px;
    }
    #accordian a {
      cursor:pointer;
    }
    #accordian h3 a {
  		padding-left: 5px;
  		font-size: 16px;
  		line-height: 1.2rem;
  		display: block;
  		text-decoration: none;
    }
    #accordian h3:hover {
  		text-shadow: 0 0 1px rgba(255, 255, 255, 0.7);
    }
    i {
  		margin-right: 10px;
    }
    #accordian li {
  		list-style-type: none;
    }
    #accordian ul{
     margin: 0px auto 0 auto;
     padding:0px 5px;
    }
    #accordian ul ul li a, #accordian h4 {
  		color: #000;
  		text-decoration: none;
  		font-size: 14px;
  		line-height: 1.2rem;
  		display: block;
  		padding: 0 10px;
  		transition: all 0.15s;
  		position: relative;
    }
    #accordian ul ul li a {
  		border-left: 5px solid transparent;
      margin-bottom:10px;
    }
    #accordian ul ul li a:hover {
      background: #aaa;
      border-left: 5px solid #000;
    }
    #accordian ul ul {
  		display: none;
    } 
    #accordian li.active>ul {
      display: block;
    }
    #accordian ul ul ul {
  		margin-left: 0px;
  		border-left: 1px dotted rgba(0, 0, 0, 0.5);
      padding: 0px 5px;
    }
    #accordian a:not(:only-child):after {
  		content: "\f104";
  		font-family: fontawesome;
  		position: absolute;
  		right: 10px;
  		top: 0;
  		font-size: 14px;
    }
    #accordian .active>a:not(:only-child):after {
      content: "\f107";
    }
    .zoom-live-parent .container-fluid {
      display: flex;
      flex-wrap: wrap;
    }
    div#traning_data{
      margin-bottom: 20px;
      display: flex;
      flex-direction: column;
      width: 100%;
      /* justify-content: center; */
      align-items: center;
      transtion:all .5s;
    }
    div#traning_data>img {
      border: 1px solid;
      border-radius: 20px;
      margin: 20px;
      padding: 0px;
      transtion:all .5s;
      width:100%;
      object-fit: contain;
      height:90vh;
    }
    button.btn {
      display: inline-block;
      margin: 10px;
    }

    .toggle-bar{
      display:block;
      text-align: end;
      cursor:pointer;
    }

    .max-parent {
    display: flex;
    justify-content: space-between;
    align-items: center;
    }

    @media (min-width:768px){
      .toggle-bar {
     
       display:none;
      }
    }


    @media (max-width:1500px){
      #accordian>ul{
        padding: 5px;
      }
      #accordian ul ul li a, #accordian h4{
        padding:0px 5px;
      }
    }
    @media (max-width:1295px){
      .zoom-live-parent .container-fluid{
        flex-direction: column;
      }
      .zoom-live-parent .left-col{
        height: 68vh;
        width:100%;
        display:block;
      }
      .left-col.col-lg-9 {
        max-width: 100%;
      }
      #accordian ul ul li a, #accordian h4{
        font-size: 12px;
      }
      .zoom-live-parent .right-col {
        width: 50%;
      }
      #accordian{
        height: 50vh;
        overflow-y: auto;
      }
      .zoom-live-parent .right-col{
        width: 50%;
        margin-top: 78px;
      }
      .right-col.col-lg-3{
        flex: 0 0 50%;
        max-width: 50%;
      }

      div#traning_data>img{
        object-fit: contain;
        height: 54vh;
        border: none;
      }
      .stretch-card > .card{
        height: 142vh;
      }
    }


    @media(max-width:767px){
      .left-col.col-lg-9 {
          height: auto;
      }
      #accordian{
        height:100vh;
      }

      #accordian li {
          padding: 10px 0;
      }

      .right-col {
          position: absolute;
          top: -10px;
          right: -100%;
          height: 100%;
          transition: 0.5s ease-in-out;
          width: 50% !important;
      }

      .right-col.slidshow{
          right: 0%;
      }

      .zoom-live-parent .left-col{
        min-height: auto;
        padding:0px;
      }
      .zoom-live-parent .nopaddingmobile{
        padding:0px;
      }
      .stylised-player {
        height:35px;
      }


      .right-col.col-lg-3 {
          flex: 0 0 70%;
          max-width: 75%;
      }
      .zoom-live-parent .right-col {
          width: 100%;
          margin-top: 78px;
      }
    }
    @media (max-width:411px){
      .right-col.col-lg-3{
        max-width: 90%;
        padding: 0;
      }
      button.preresporce, button.nextresporce {
        margin: 2px;
        padding: 9px;
      }
    }
  </style>
  
  <div class="zoom-live-parent">
               <div class="container-fluid nopaddingmobile">
                <div class="left-col col-lg-9" >
                    <div id="activitydetails" data-page="0" data-resource="0">
                    <img src="/wp-content/plugins/el-dashboard/public/images/traningmain.png" style="max-width: 100%; margin: 0px;"/>
                    <div class="coursedescription">'.$course->summary.'</div>
                    </div>
                </div>
   <div class="right-col col-lg-3 ">
    <div id="accordian">
     <ul>';

  for ($i = 0; $i < count($apidata); $i++) {
    $reshtml .= '<li><h3><a >' . $apidata[$i]->name . '</a></h3>
                    <ul>';
    if (!empty($apidata[$i]->modules)) {
      $total77 = $apidata[$i]->modules;
      for ($x = 0; $x < count($total77); $x++) {
        $reshtml .= '<li><a class="mod_activity" modid ="' . $total77[$x]->id . '" >' . $total77[$x]->name . '</a></li>';
      }
    }
    if (!empty($apidata[$i]->subtopics)) {
      $total = $apidata[$i]->subtopics;
      for ($j = 0; $j < count($total); $j++) {
        $reshtml .= '<li><a >' . $total[$j]->name . '</a><ul>';
        if (!empty($total[$j]->modules)) {
          $total66 = $total[$j]->modules;
          for ($y = 0; $y < count($total66); $y++) {
            $reshtml .= '<li><a class="mod_activity" modid ="' . $total66[$y]->id . '" >' . $total66[$y]->name . '</a></li>';
          }
        }
        // $apidataaa = print_r($total[$j]->name,true);
        if (!empty($total[$j]->subtopics)) {
          $total2 = $total[$j]->subtopics;
          for ($k = 0; $k < count($total2); $k++) {
            $reshtml .= '<li><a >' . $total2[$k]->name . '</a><ul>';
            if (!empty($total2[$k]->modules)) {
              $total55 = $total2[$k]->modules;
              for ($z = 0; $z < count($total55); $z++) {
                $reshtml .= '<li><a class="mod_activity" modid ="' . $total55[$z]->id . '" >' . $total55[$z]->name . '</a></li>';
              }
            }
            // $apidataaa = print_r($total2[$k]->name,true);
            if (!empty($total2[$k]->subtopics)) {
              $total3 = $total2[$k]->subtopics;
              for ($l = 0; $l < count($total3); $l++) {
                $reshtml .= '<li><a >' . $total3[$l]->name . '</a><ul>';
                if (!empty($total3[$l]->modules)) {
                  $total44 = $total3[$l]->modules;
                  for ($w = 0; $w < count($total44); $w++) {
                    $reshtml .= '<li><a class="mod_activity" modid ="' . $total44[$w]->id . '" >' . $total44[$w]->name . '</a></li>';
                  }
                }
                // $apidataaa = print_r($total3[$l]->name,true);
                if (!empty($total3[$l]->subtopics)) {
                  $total4 = $total3[$l]->subtopics;
                  for ($m = 0; $m < count($total4); $m++) {
                    $reshtml .= '<li><a >' . $total4[$m]->name . '</a><ul></ul></li>';
                    if (!empty($total4[$m]->modules)) {
                      $total33 = $total4[$m]->modules;
                      for ($u = 0; $u < count($total44); $u++) {
                        $reshtml .= '<li><a class="mod_activity" modid ="' . $total33[$u]->id . '" >' . $total33[$u]->name . '</a></li>';
                      }
                    }
                    // $apidataaa = print_r($total4[$m]->name,true);
                    // $apidataaa = print_r($total3[$l]->subtopics,true);
                    // $html .= '<pre>aa====' . $apidataaa. '</pre>';  
                  }
                }
                $reshtml .= '</ul></li>';
              }
            }
            $reshtml .= '</ul></li>';
          }
        }
        $reshtml .= '</ul></li>';
      }
    }
    $reshtml .= '</ul></li>';
  }





  $reshtml .= '
	   </ul>
    </div>
</div>
</div>
</div>';





  $html .= '
   <div class="row">
              <div class="col-md-12 grid-margin transparent">
                <div class="row">
                  <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                      <div class="card-body">
                        <div class="max-parent">
                        <h4 class="card-title">' . plus_get_string("training", "site") . '</h4>
                        <div class="toggle-bar">
                         <i class="fa fa-bars"></i>
                        </div>
                        </div>
                        <div class="row">' . $reshtml . '
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


   

  <script>
    $(document).ready(function() {
      $(document).on("click", ".nextresporce", function(){
        loadNextResourceDetails();
      });
      $(document).on("click", ".preresporce", function(){
        loadpreResourceDetails();
      });
      $(".mod_activity").click(function() {
        var id = $(this).attr("modid");
        $("#activitydetails").data("id", id);
        $("#activitydetails").find("video").attr("src", "");
        $("#activitydetails").find("video").remove();
        $("#activitydetails").find("audio").attr("src", "");
        $("#activitydetails").find("audio").remove();
        $("#activitydetails").find(".plusplayer").remove();
        setTimeout(loadResourceDetails,5);
        
        $(".right-col" ).removeClass("slidshow");
      });
    	$("#accordian a").click(function() {
    			var link = $(this);
    			var closest_ul = link.closest("ul");
    			var parallel_active_links = closest_ul.find(".active")
    			var closest_li = link.closest("li");
    			var link_status = closest_li.hasClass("active");
    			var count = 0;
    			closest_ul.find("ul").slideUp(function() {
    					if (++count == closest_ul.find("ul").length)
    							parallel_active_links.removeClass("active");
    			});
    			if (!link_status) {
    					closest_li.children("ul").slideDown();
    					closest_li.addClass("active");
    			}
    	});
      $(".toggle-bar").click(function(){
        $(".right-col" ).toggleClass("slidshow");
      });
    });
  </script>
  ';
  return $html;
}
