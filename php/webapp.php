<?php
$dataDir = $_ENV['OPENSHIFT_REPO_DIR'].'php/';
header('application/x-web-app-manifest+json');

// handle serving image files
if (isset($_REQUEST['f'])) {
    if (is_file($dataDir . $_REQUEST['f'].'.webapp')) {

        // set content type if a legal image extention
        $file = $dataDir . $_REQUEST['f'] .'.webapp';

        // Checking if the client is validating his cache and if it is current.
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) ==  filemtime($file))) {
            // Client's cache IS current, so we just respond '304 Not Modified'.
            header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 304);
            exit;
        }

        // insert Last-Modified header for wget's -N option
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 200);
        header('Content-Length: '.filesize($file));
        // for HEAD requests (which WGET claims to use) we don't send the content
        if ($_SERVER['REQUEST_METHOD']=='HEAD') {
            exit;
        }

        $seconds_to_cache = 60*60*24;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
        header('Pragma: public');

        // send image to client
        readfile($file);
        exit;

    }

    // error
    header ("HTTP/1.0 404 Not Found");
    exit;
}
