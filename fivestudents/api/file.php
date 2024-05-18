<?php 
ob_start();
session_start();
$allheader = getallheaders();
$fileid = urldecode($_GET["fileid"]);
$filename = urldecode($_GET["filename"]);
$page = urldecode(isset($_GET["page"])?$_GET["page"]:0);
$accesskey = urldecode($_GET["accesskey"]);

if($allheader['Sec-Fetch-Site'] == "same-origin" && is_array($_SESSION["resources"]) && is_array($_SESSION["fileaccesskey"]) && isset($_SESSION["resources"][$fileid]) && isset($_SESSION["fileaccesskey"][$fileid]) && $_SESSION["fileaccesskey"][$fileid] == $accesskey){
    $basepath = "/var/www/plusdata/{$fileid}/$filename";
    $resource = $_SESSION["resources"][$fileid];
    if($resource->filetype == 'pdf'){
        $basepath = "/var/www/plusdata/{$fileid}/images/page_{$page}.png";
        $resource->mimetype = "image/png";
    }
    if(file_exists($basepath)){
        $lifetime = 60*60;
        $immutable='';
        header('Cache-Control:private, max-age='.$lifetime.', no-transform'.$immutable);
        header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
        header('Pragma: ');
        header('Content-Length: ' . filesize($basepath));
        header('Content-Type: ' . mime_content_type($basepath));
        header('Content-Disposition: attachment; filename="' . $resource->filename . '"');
        header('Content-Transfer-Encoding: binary');
        if(!in_array($resource->filetype, array("audio", "video"))){
            unset($_SESSION["fileaccesskey"][$fileid]);
        }
        readfile($basepath);
        exit;
    }
}
$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
header($protocol . ' 404 File Not Found');
exit;