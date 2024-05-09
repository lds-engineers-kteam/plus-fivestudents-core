<?php 

class MoodleManager {
    private $moodleurl = 'https://portal.fivestudents.com/app_rest_api/WPV3/index.php';
    private $token;

    public function __construct($param) {
        $this->token = $param->token;
    }

    public function get($wsfunction, $wsrole = "tutor", $args = array()) {
        if (is_array($args)) {
            $args = (object) $args;
        }
        $finalcall = new stdClass();
        $finalcall->wsfunction = $wsfunction;
        $finalcall->wsrole = $wsrole;
        $finalcall->wstoken = $this->token;
        $finalcall->wsargs = $args;
        if (empty($finalcall->wstoken)) {
            return null;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->moodleurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($finalcall),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        if ($response = curl_exec($curl)) {
            if (json_decode($response)) {
                return json_decode($response);
            } else {
                return $response;
            }
        }
        return null;
    }
}
