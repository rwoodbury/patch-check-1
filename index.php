<?php

/**
 * Support patch checker tool v.0.4
 */

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('upload_max_filesize', '16M');
set_time_limit(120);
define('NO_AJAX', isset($_POST['no_ajax']) && $_POST['no_ajax'] == 1);

require_once('config.php');
require_once('vendor/autoload.php');


$action = (isset($_GET['action'])) ? $_GET['action'] : false;
$messageList = array();

try {
    if ($action == 'upload' && !empty($_POST)) {
        $fileUploader = new File_Uploader(array('upload_path' => UPLOAD_PATH));
        $result = $fileUploader->upload();

        $patchChecker = new Checker();

        if (count($result['new_file_name']) == 1) {
            $checkResults = $patchChecker->checkPatchForAllReleases($result['new_file_name'][0]);
        } else {
            $mergedPatch = $patchChecker->mergePatchesToOne($result['new_file_name'], $result['result']['filename']);
            $checkResults = $patchChecker->checkPatchForAllReleases($mergedPatch, true);
            @unlink(UPLOAD_PATH . $mergedPatch['name']);
        }

        if (isset($result['result'])) {
            $fh = @fopen(ACTIVITY_LOG, 'a');
            foreach ($result['result']['filename'] as $fileId => $filename) {
                if (file_exists($result['new_file_name'][$fileId])) {
                    unlink(UPLOAD_PATH . $result['new_file_name'][$fileId]);
                }
                // checked patches statistic collection
                $data = date('Y-m-d H:i:s') . ': ' . $filename . "\n";
                @fwrite($fh, $data);
            }
            @fclose($fh);
        }

        $result = $result['result'];
        $result['checkResults'] = $checkResults;

        if (!NO_AJAX) {
            echo json_encode($result);
            die;
        }
    }

    require_once 'view/index.phtml';

}
catch (Exception $e) {
    require_once 'view/error.phtml';
}

