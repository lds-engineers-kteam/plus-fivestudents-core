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

    public function getGroupLinkID($args) {
        
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
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

        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("getGroupLinkID", null, $args);
        
        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }

    public function getGroupExamCode($args){

        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("getGroupExamCode", null, $args);
        
        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }

    public function generateMigrationCode($args){
        
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("generateMigrationCode", null, $args);

        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }

    public function getGroupOnetimeCode($args){

        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("getGroupOnetimeCode", null, $args);

        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }
	
	public function getQuizes($args){
        
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("getQuizes", null, $args);

        if($APIRES->code == 200){
            $APIRES->data->questionstring = self::randerQuizesQuestions($APIRES->data);
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
	
    private function randerQuizesQuestions($quizdata){
        $allquestions = $quizdata->allquestions;
        $lang = $quizdata->quiz->lang;
        $finalquestionstr = "";
        if(sizeof($allquestions) > 0){
            $finalquestionstr.='<div class="questionlist '.$lang.'">'; 
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
					}
					else if($question->type=="ddwtos" || $question->type=="gapselect"){
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
				$i++;
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

    public function getQuizStatus($args){
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $args = (object)$args;
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("getQuizStatus", null, $args);
        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }

    public function getQuizData($args){
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $args = (object)$args;
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("getQuizData", null, $args);
        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }

    public function saveAnswer($args){
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $args = (object)$args;
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("saveAnswer", null, $args);
        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }

    public function finishAttempt($args){
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $args = (object)$args;
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $APIRES = $MOODLE->get("finishAttempt", null, $args);
        if($APIRES->code == 200){
            $this->sendResponse($APIRES->data);
        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }

    public function getResourcepdfurl($args){
        global $CFG;
        $args = (object)$args;
        if(isset($_SESSION["activity"]) && !empty($_SESSION["activity"])){
            $reqresource = $args->resource;
            $reqpage = $args->page;
            $this->reqresource = $reqresource;
            $found_key = array_search($args->id, array_column($_SESSION["activity"], 'id'));
            if($found_key !== false){
                $apiresource = $_SESSION["activity"][$found_key];
                $finalreturn->id = $apiresource->id;
                $finalreturn->name = $apiresource->name;
                $finalreturn->parent = $apiresource->parent;
                $finalreturn->breadcrumbs = $apiresource->breadcrumbs;
                $finalreturn->multipage = false;
                $finalreturn->mod = $apiresource->mod;
                $finalreturn->page = 1;
                $finalreturn->totalpage = 1;
                $finalreturn->filetype = "";
                $finalreturn->mimetype = "";
                $finalreturn->currentres = $reqpage;
                switch ($apiresource->mod) {
                    case 'resource':
                        if(isset($apiresource->files[$reqresource])){
                            $resource = $apiresource->files[$reqresource];
                            if($resource = $_SESSION["resources"][$resource->id]){
                                switch ($resource->filetype) {
                                    case 'image':
                                    case 'video':
                                    case 'audio':
                                        $accesskey = sha1(rand());
                                        $_SESSION["fileaccesskey"][$resource->id]=$accesskey;
                                        $finalreturn->url = $CFG->wwwroot."/api/file.php?fileid={$resource->id}&filename={$resource->filename}&accesskey={$accesskey}";
                                        $finalreturn->filename = $resource->filename;
                                        $totalpage = 1;
                                        break;
                                    case 'pdf':
                                        $accesskey = sha1(rand());
                                        $_SESSION["fileaccesskey"][$resource->id]=$accesskey;
                                        $finalreturn->filename = $resource->filename;
                                        $finalreturn->url = $CFG->wwwroot."/api/file.php?fileid={$resource->id}&filename={$filename}&page={$reqpage}&accesskey={$accesskey}";
                                        $totalpage = $resource->totalpage;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }
                                $finalreturn->page = 1;
                                $finalreturn->totalpage = $totalpage;
                                $finalreturn->filetype = $resource->filetype;
                                $finalreturn->mimetype = $resource->mimetype;
                                $finalreturn->totalres = sizeof($apiresource->files);
                                $finalreturn->currentres = $reqpage;
                                $this->sendResponse($finalreturn);
                            } else {
                                $this->sendError("Error", "Unable to find resource");
                            }
                        } else {
                            $this->sendError("Error", "Unable to find resource");
                        }
                        break;
                    default:
                        $this->sendError("Error", "Unable to find resource");
                        # code...
                        break;
                }
            } else {
                $this->sendError("Error", "Unable to find resource");
            }
        } else {
            $this->sendError("Error", "Unable to find resource");
        }
    }

    public function getResourceDetails($args) {
        global $CFG;

        if(!isset($_SESSION["resourcedetails"])){$_SESSION["resourcedetails"] = array();}
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $args = (object)$args;
        // print_r($args);
        
        if ($args && $args->id && isset($_SESSION["resourcedetails"][$args->id]) && isset($_SESSION["resourcedetails"])) {
            $APIRES = new stdClass(); // Define $APIRES if it's not already defined
            $APIRES->code = 200; // Use assignment operator '=' instead of '=='
            $APIRES->data = $_SESSION["resourcedetails"][$args->id]; // Use assignment operator '=' instead of '=='
        } else {
            $APIRES = $MOODLE->get("getResourceDetails", null, $args);
        }

        

        // print_r($_SESSION["resourcedetails"][$apiresourceid]);
        // print_r($apiresource);
        // print_r($apiresourceid);

        if(!isset($_SESSION["resources"])){$_SESSION["resources"] = array();}
        if(!isset($_SESSION["fileaccesskey"])){$_SESSION["fileaccesskey"] = array();}
        if(!isset($_SESSION["activity"])){$_SESSION["activity"] = array();}
        if($APIRES->code == 200){
            $apiresource = $APIRES->data;
            $apiresourceid = $apiresource->id;

            $_SESSION["resourcedetails"][$apiresourceid] = $apiresource;


            $reqresource = $args->resource;
            $reqpage = $args->page;
            $finalreturn = new stdClass();
            $finalreturn->id = $apiresource->id;
            $finalreturn->name = $apiresource->name;
            $finalreturn->parent = $apiresource->parent;
            $finalreturn->breadcrumbs = $apiresource->breadcrumbs;
            $finalreturn->multipage = false;
            $finalreturn->mod = $apiresource->mod;
            $finalreturn->page = 1;
            $finalreturn->totalpage = 1;
            $finalreturn->filetype = "";
            $finalreturn->mimetype = "";
            $finalreturn->currentres = $reqpage;
            $this->reqresource = $reqresource;
            array_push($_SESSION["activity"], $apiresource);



            switch ($apiresource->mod) {
                case 'resource':
                    if(isset($apiresource->files[$reqresource])){
                        $resource = $apiresource->files[$reqresource];
                        $this->cachefiles($apiresource->files);
                        switch ($resource->filetype) {
                            case 'image':
                            case 'video':
                            case 'audio':
                                $filename = urlencode($resource->filename);
                                $accesskey = sha1(rand());
                                $_SESSION["resources"][$resource->id]=$resource;
                                $_SESSION["fileaccesskey"][$resource->id]=$accesskey;
                                $finalreturn->url = $CFG->wwwroot."/api/file.php?fileid={$resource->id}&filename={$filename}&accesskey={$accesskey}";
                                $finalreturn->filename = $resource->filename;
                                $totalpage = 1;
                                break;
                            case 'pdf':
                                $accesskey = sha1(rand());
                                $_SESSION["fileaccesskey"][$resource->id]=$accesskey;
                                try {
                                    $imagick = new Imagick();
                                    $basepath = "/var/www/plusdata";
                                    $dirpath = "{$basepath}/{$resource->id}";
                                    $this->checkpath($dirpath);
                                    $filename = urlencode($this->preparefilename($resource->filename));
                                    $fileurl = "{$dirpath}/$filename";
                                    $imagick->readImage($fileurl);
                                    $totalpage = $imagick->count();
                                } catch (Exception $e) {
                                    
                                }
                                $finalreturn->filename = $resource->filename;
                                $finalreturn->url = $CFG->wwwroot."/api/file.php?fileid={$resource->id}&filename={$filename}&page={$reqpage}&accesskey={$accesskey}";
                                $resource->totalpage = $totalpage;
                                $_SESSION["resources"][$resource->id]=$resource;
                                break;
                            default:
                                # code...
                                break;
                        }
                        $finalreturn->page = 1;
                        $finalreturn->totalpage = $totalpage;
                        $finalreturn->filetype = $resource->filetype;
                        $finalreturn->mimetype = $resource->mimetype;
                        $finalreturn->totalres = sizeof($apiresource->files);
                        $finalreturn->currentres = $reqpage;
                        $this->sendResponse($finalreturn);
                    } else {
                        $this->sendError("Error", "Unable to find resource");
                    }
                    break;
                case 'quiz':
                    $this->sendResponse($finalreturn);
                    break;
                default:
                    $this->sendError("Error", "Unable to find resource");
                    # code...
                    break;
            }



        } else {
            $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
        }
    }

    private function cachefiles($files){
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);

        $basepath = "/var/www/plusdata";        
        foreach ($files as $key => $file) {
            $link = "https://portal.fivestudents.com/local/designer/file.php?id={$file->pathnamehash}&filename={$file->filename}";
            $dirpath = "{$basepath}/{$file->id}";
            $this->checkpath($dirpath);
            $filename = $this->preparefilename($file->filename);
            $fileurl = "{$dirpath}/$filename";
            if(!file_exists($fileurl)){
                $myfile = fopen($fileurl, "w") or die("Unable to open file!");
                file_put_contents($fileurl, file_get_contents($link));
            }
            if($file->filetype == "pdf"){
                try {
                    $dirpath = "{$basepath}/{$file->id}/images";
                    $this->checkpath($dirpath);
                    $imagick = new Imagick();
                    $imagick->setResolution(150, 150);
                    $imagick->readImage($fileurl);
                    $imagick->setImageFormat('png');
                    foreach ($imagick as $key => $page) {
                        $page->writeImage("{$dirpath}/page_{$key}.png");
                    }
                    $imagick->clear();
                } catch (Exception $e) {
                    
                }
            }
        }
    }

    private function preparefilename($filename){
        $filename = str_replace(" ", "__", $filename);
        return $filename;
    }

    private function checkpath($path){
        if(!is_dir($path)) { mkdir($path, 0777, true); }
    }

    public function updateEventStatus($args) {
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $args = (object)$args;
        $APIRES = $MOODLE->get("updateEventStatus", null, $args);
        
        if(is_object($APIRES)){
            if($APIRES->code == 200){
                $this->sendResponse($APIRES->data);
            } else {
                $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
            }
        } else {
            echo $APIRES;
            die;
        }
    }

    public function visitEventStatus($args) {
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $args = (object)$args;
        $APIRES = $MOODLE->get("visitEventStatus", null, $args);
        
        if(is_object($APIRES)){
            if($APIRES->code == 200){
                $this->sendResponse($APIRES->data);
            } else {
                $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
            }
        } else {
            echo $APIRES;
            die;
        }
    }

    public function deleteEvents($args) {
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $args = (object)$args;
        $APIRES = $MOODLE->get("deleteEvents", null, $args);
        
        if(is_object($APIRES)){
            if($APIRES->code == 200){
                $this->sendResponse($APIRES->data);
            } else {
                $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
            }
        } else {
            echo $APIRES;
            die;
        }
    }

    public function editEventTime($args) {
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $args = (object)$args;
        $APIRES = $MOODLE->get("editEvent", null, $args);
        
        if(is_object($APIRES)){
            if($APIRES->code == 200){
                $this->sendResponse($APIRES->data);
            } else {
                $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
            }
        } else {
            echo $APIRES;
            die;
        }
    }

    public function getSurveyList($args) {
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $args = (object)$args;
        $APIRES = $MOODLE->get("BrowseSurveys", null, $args);
        
        if(is_object($APIRES)){
            if($APIRES->code == 200){
                $this->sendResponse($APIRES->data);
            } else {
                $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
            }
        } else {
            echo $APIRES;
            die;
        }
    }

    public function getSurveyDetails($args) {
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $args = (object)$args;
        $APIRES = $MOODLE->get("getSurveyDetails", null, $args);
        
        if(is_object($APIRES)){
            if($APIRES->code == 200){
                $this->sendResponse($APIRES->data);
            } else {
                $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
            }
        } else {
            echo $APIRES;
            die;
        }
    }

    public function saveSurveyResponce($args) {
        global $CFG;
        require_once($CFG->dirroot . '/api/moodlecall.php');
        $current_user = wp_get_current_user();
        $MOODLE = new MoodleManager($current_user);
        $args = (object)$args;
        $APIRES = $MOODLE->get("saveSurveyResponce", null, $args);

        if(is_object($APIRES)){
            if($APIRES->code == 200){
                $this->sendResponse($APIRES->data);
            } else {
                $this->sendError($APIRES->error->title, $APIRES->error->message, $APIRES->error->code);
            }
        } else {
            echo $APIRES;
            die;
        }
    }

    public function testlogin($args) {
        $args = (object)$args;
        if(!is_user_logged_in()){
            $this->sendError("Error", "Please Login");
            return;
        }
    }
}

$baseobject = new APIManager();
$skippedfunctions = array("getGroupLinkID", "getGroupCode", "getGroupExamCode", "getGroupOnetimeCode", "getQuizes", "printHtmlToImage", "generateMigrationCode");
$requirelogin = array("getResourceDetails", "getQuizStatus", "getResourcepdfurl", "getQuizData", "saveAnswer", "finishAttempt","updateEventStatus","editEventTime", "visitEventStatus", "deleteEvents", "getSurveyList", "getSurveyDetails","getInstitute_Notification", "saveSurveyResponce");
if (method_exists($baseobject, $functionname)) {
    if(in_array($functionname, $skippedfunctions)) {
        $baseobject->$functionname($args);
    } else if(in_array($functionname, $requirelogin)){
        $baseobject->$functionname($args);
    } 
}

echo json_encode($baseobject);