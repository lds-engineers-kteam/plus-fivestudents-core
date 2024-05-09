<?php 
require_once(__DIR__ . "/../config.php");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
header("HTTP/1.0 200 Successfull operation");
$getpatameter=json_decode(file_get_contents('php://input',True),true);
$wstoken = "";
if(isset($getpatameter['wstoken'])){
    $wstoken = $getpatameter['wstoken'];
}
$functionname = null;
$args = null;
if(is_array($getpatameter)){
    $functionname = $getpatameter['wsfunction'];
    $args = $getpatameter['wsargs'];
}
class APIManager {
	private $wpdb;
    public $status = 0; 
    public $message = "Error";
    public $data = null;
    public $code = 404;
	private $loginuserid;
    private $loginuser;
    public $error = array(
        "code"=> 404,
        "title"=> "Server Error.",
        "message"=> "Server under maintenance"
    );
    function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->code = 404;
        $this->error = array(
            "code"=> 404,
            "title"=> "Server Error..",
            "message"=> "Missing functionality"
        );
    }
    public function validatetoken($token){
        // if($userid = plus_validatetoken($token)){
        //     wp_set_current_user( $userid );
        //     $this->loginuserid = $userid;
        //     $this->loginuser = wp_get_current_user();
        //     $this->sendResponse("");
        //     return true;
        // } else {
        //     $this->sendError("Error", "Invalid Token");
        // }
        if($token){
            $this->loginuser = wp_get_current_user();
            $this->sendResponse($token);
            return $this->sendResponse($token);
        } else {
            $this->sendError("Error", "Invalid Token");
        }
    }
    public function getGroupLinkID($args){
        $MOODLE = new MoodleManager();
        $APIRES = $MOODLE->get("getGroupLinkID", null, $args);
        if($APIRES->code == 200){
            $fbasereq= new stdClass();
            $fbasereq->longDynamicLink = "https://fivestudents.page.link/?link=https://www.fivestudents.com/joinGroup?groupid=".$APIRES->data->grouplinkid;
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyB6QKpOJe_qNbiMnx4aQw4zK10dzUvueNM',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>json_encode($fbasereq),
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $this->sendResponse(json_decode($response));
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }
    public function getGroupCode($args){
        $MOODLE = new MoodleManager();
        $APIRES = $MOODLE->get("getGroupLinkID", null, $args);
        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }  
	public function getHomeWork($args){
        $MOODLE = new MoodleManager();
        $APIRES = $MOODLE->get("getHomeWork", null, $args);
        if($APIRES->code == 200){
            $APIRES->data->questionstring = self::randerQuestions($APIRES->data->allquestions);
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }
	public function getQuizes($args){
        $MOODLE = new MoodleManager();
        $APIRES = $MOODLE->get("getQuizes", null, $args);
        if($APIRES->code == 200){
            $APIRES->data->questionstring = self::randerQuizesQuestions($APIRES->data->allquestions);
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }
    public function printHtmlToImage($args){
        $printcss = file_get_contents("print.css");
        $imagereq = new stdClass();
        $imagereq->console_mode="";
        $imagereq->css=$printcss;
        $imagereq->device_scale="";
        $imagereq->google_fonts="";
        $imagereq->html=$args['content'];
        $imagereq->ms_delay="";
        $imagereq->render_when_ready=false;
        $imagereq->selector="";
        $imagereq->url="";
        $imagereq->viewport_height="";
        $imagereq->viewport_width="";
        $curl = curl_init();
        curl_setopt_array($curl, 
            array(
                CURLOPT_URL => 'https://htmlcsstoimage.com/demo_run',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>json_encode($imagereq),
                CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
                ),
            )
        );
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        if($response->url){
            $this->sendResponse($response->url);
        } else {
            $this->sendError("Error", "Failed to generate image", 400);
        }
    }
    private function sendResponse($data) {
        $this->status = 1;
        $this->message = "Success";
        $this->data = $data;
        $this->code = 200;
        $this->error = null;
    }
	
	private function randerQuizesQuestions($allquestions){
        $finalquestionstr = "";
        if(sizeof($allquestions) > 0){
            $finalquestionstr.='<div class="questionlist">'; 
            $i=1;
            foreach ($allquestions as $key => $question) {
                $finalquestionstr.='<div class="questionset">';
                    $finalquestionstr.='<div class="questionhead">'.$i .'.'.$question->questionTitle.'</div>';
					if($question->type=="multianswer"){														
						foreach($question->subQuestion as $qkey => $subQuestion){
							$stext = ""; 
							$questionText = $subQuestion->questionText;
							if($subQuestion->type == 'multichoiceh'){
								$stext.='<br>';
								foreach($subQuestion->options as $optionval){									
									$stext.='<label>							
									<input type="radio"> '.$optionval->answer.'
									</label>&nbsp;&nbsp';								
								}
								
							}else if($subQuestion->type == 'multichoicev'){
								$stext.='<br>';
								foreach($subQuestion->options as $optionval){									
									$stext.='<label>							
									<input type="radio"> '.$optionval->answer.'
									</label>
									</br>';								
								}
								
							
							}else if($subQuestion->type == 'shortanswer'){								
								$stext.='<label>							
								<input type="text" value="" style="display: inline-block;width: 60px;" > 
								</label>						
								<br>';
							} else {								
								$stext.='<select style="display: inline-block;width: fit-content;">';
								foreach($subQuestion->options as $optionval){
									$stext.='<option>'.$optionval->answer.'</option>';	
								}
								$stext.='</select>';		
							}
							$question->questionText = str_replace('{#'.($qkey+1).'}',$stext, $question->questionText);
						}							
					}else if($question->type=="ddwtos" || $question->type=="gapselect"){
						$question->questionText = preg_replace('/[[[1-9]*]]/m', '<input type="text" val="" style="display: inline-block;width: 60px;">', $question->questionText);;
					}
                    $finalquestionstr.='<div class="questionbody homeworkquiz">'.$question->questionText.'</div>';
					$finalquestionstr.='<div class="questionanssection">';
						if($question->type=='multichoice' || $question->type=='multiselect'){
							if($question->isRadioButton){
								$fieldtype='radio';
							}else{
								$fieldtype='checkbox';
							}													
							foreach($question->options as $optionval ){								
							$finalquestionstr.='<label>							
							<input type="'.$fieldtype.'"> '.$optionval->answer.'
							</label>
							
							<br>';	
							}														
						}else if($question->type=="gapselect"){
							$finalquestionstr.='<select>';
							foreach($question->options as $optionval){
							$finalquestionstr.='<option>'.$optionval->answer.'</option>';	   
							}
							$finalquestionstr.='</select>';						
						}else if($question->type=="truefalse"){
							foreach($question->options as $optionval ){								
							$finalquestionstr.='<label>							
							<input type="radio"> '.$optionval->answer.'
							</label>
							
							<br>';	
							}
						}else if($question->type=="shortanswer" || $question->type=="numerical" || $question->type=="calculated"){														
							$finalquestionstr.='<label>							
							<input type="text" value="" disabled> 
							</label>						
							<br>';	
						}else if($question->type=="ddwtos"){
							foreach($question->options as $optionval ){								
							$finalquestionstr.='						
							<span class="ddwtos" style="padding: 10px;border: 1px solid;">'.$optionval->answer.'</span>';	
							}
						}            
					$finalquestionstr.='</div>'; 
						
                $finalquestionstr.='</div>';   
				$i++;
            }
			
            $finalquestionstr.='</div>';   
        } else {
            $finalquestionstr .= '<div class="alert alert-warning">'.plus_get_string("questionnotfound", "form").'</div>';

        }
        return $finalquestionstr;
    }
	
    private function randerQuestions($allquestions){
        $finalquestionstr = "";
        if(sizeof($allquestions) > 0){
            $finalquestionstr.='<div class="questionlist">';   
            foreach ($allquestions as $key => $question) {
                $finalquestionstr.='<div class="questionset">';
                    $finalquestionstr.='<div class="questionhead">'.$question->questionTitle.'</div>';
                    $finalquestionstr.='<div class="questionbody homeworkquiz">'.$question->questionText.'</div>';
					$finalquestionstr.='<div class="questionanssection">';
						if($question->type=='multichoice' || $question->type=='multiselect'){
							if($question->isRadioButton){
								$fieldtype='radio';
							}else{
								$fieldtype='checkbox';
							}													
							foreach($question->options as $optionval ){								
							$finalquestionstr.='<label>							
							<input type="'.$fieldtype.'"> '.$optionval->answer.'
							</label>
							
							<br>';	
							}														
						}else if($question->type=="multianswer"){
							$finalquestionstr.='<select>';
							foreach($question->subQuestion->options as $optionval){
							$finalquestionstr.='<option value="volvo">Volvo</option>
												<option value="saab">Saab</option>
												<option value="fiat">Fiat</option>
												<option value="audi">Audi</option>
											  ';	   
							}
							$finalquestionstr.='</select>';
							
						}else if($question->type=="gapselect"){
							$finalquestionstr.='<select>';
							foreach($question->options as $optionval){
							$finalquestionstr.='<option>'.$optionval->answer.'</option>';	   
							}
							$finalquestionstr.='</select>';						
						}else if($question->type=="truefalse"){
							foreach($question->options as $optionval ){								
							$finalquestionstr.='<label>							
							<input type="radio"> '.$optionval->answer.'
							</label>
							
							<br>';	
							}
						}else if($question->type=="shortanswer" || $question->type=="numerical" || $question->type=="calculated"){														
							$finalquestionstr.='<label>							
							<input type="text" value="'.$question->rightAnswer.'" disabled> 
							</label>						
							<br>';	
						}else if($question->type=="ddwtos"){
							foreach($question->options as $optionval ){								
							$finalquestionstr.='						
							<span class="ddwtos" style="padding: 10px;border: 1px solid;">'.$optionval->answer.'</span>';	
							}
						}            
					$finalquestionstr.='</div>'; 
						
                $finalquestionstr.='</div>';   
            }
            $finalquestionstr.='</div>';   
        } else {
            $finalquestionstr .= '<div class="alert alert-warning">'.plus_get_string("questionnotfound", "form").'</div>';
        }
        return $finalquestionstr;
    }
    private function sendError($title, $message, $code=400) {
        $this->status = 0;
        $this->message = "Error";
        $this->data = null;
        $this->code = $code;
        $this->error = array(
            "code"=> $code,
            "title"=> $title,
            "message"=> $message
        );
    }
    public function checkToken(){
        $this->sendResponse($this->loginuser);
    }
}

$baseobject = new APIManager();
$skippedfunctions = array("getGroupLinkID", "getGroupCode","getHomeWork","getQuizes", "printHtmlToImage");
if (method_exists($baseobject, $functionname)) {
    if(in_array($functionname, $skippedfunctions)){ 
        $baseobject->$functionname($args);
    } else if($baseobject->validatetoken($wstoken)) {
        $baseobject->$functionname($args);
    }
}
echo json_encode($baseobject);