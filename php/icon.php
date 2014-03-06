<?php
// test for image file names
function isImageName($name) {
    return preg_match('/^[^.\/][^\/]*\.(jpe?g|png|gif)$/i',$name)>0;
}

$dataDir = $_ENV['OPENSHIFT_DATA_DIR'];

// handle serving image files
if (isset($_REQUEST['f'])) {
    if (isImageName($_REQUEST['f']) && is_file($dataDir . $_REQUEST['f'])) {

        // set content type if a legal image extention
        $file = $dataDir . 'app-icons/' . $_REQUEST['f'];
        if (preg_match('/\.gif$/i',$file)) {
            header('Content-Type: image/gif');
        } else if (preg_match('/\.jpe?g$/i',$file)) {
            header('Content-Type: image/jpeg');
        } else if (preg_match('/\.png$/i',$file)) {
            header('Content-Type: image/png');
        } else {
            header ("HTTP/1.0 404 Not Found");
            exit;
        }

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

        // tell clients to cache for 5 years
        $seconds_to_cache = 60*60*24*365*5;
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
