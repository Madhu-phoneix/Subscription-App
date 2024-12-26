<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $dirPath = dirname(dirname(__DIR__));
    require '../../vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->showImageErrors = true;
    ob_start();  // start output buffering
    include  'invoiceDetails.php';
    $content = ob_get_clean();
    $mpdf->WriteHTML($content);
    $mpdf->Output('filename.pdf', 'D');
?>
