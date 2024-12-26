<?php

    header("Access-Control-Allow-Origin: *");
    // header("Access-Control-Allow-Origin: https://prince-panday-store.myshopify.com");
    header('Frame-Ancestors: ALLOWALL');
    header('X-Frame-Options: ALLOWALL');

    header('Set-Cookie: cross-site-cookie=bar; SameSite=None; Secure');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

   header('Content-type: text/csv');
   header('Content-Disposition: attachment; filename="sample_csv.csv"');
   $dirpath = dirname(dirname(__FILE__));
   echo $dirpath;

   ob_clean();
   readfile($dirpath.'/application/assets/csv/sample_csv.csv');
   echo json_encode(array('status'=>true, 'message' => 'File Exported Successfully'));
?>