<?php
    $order_id = $_GET['order_id'];
    $store = $_REQUEST['store'];
    $dirPath = dirname(__DIR__);
    require '../vendor/autoload.php';
    $filename = 'Invoice-'.$order_id.'.pdf';
    $mpdfConfig = [
        'tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
        'mode' => 'utf-8',
    ];
    $mpdf = new \Mpdf\Mpdf($mpdfConfig);
    $mpdf->SetMargins(10, 10, 10, 10);
    $mpdf->SetAutoPageBreak(true, 10);
    ob_start();
    include  'invoiceDetails.php';
    $content = ob_get_clean();
    $mpdf->WriteHTML($content);
    $mpdf->Output($filename, 'F');

    $sendMailArray = array(
        'sendTo' =>  $_POST['customer_email'],
        'subject' => 'Order Invoice',
        'mailBody' =>  'Please find the attachment below.',
        'mailAttachment' => $filename,
        'mailHeading' => '',
        'ccc_email' => '',
        'bcc_email' =>  '',
        'reply_to' => ''
    );
    try{
        $contract_deleted_mail = $mainobj->sendMail($sendMailArray,'sendInvoice');
        unlink($filename);
        echo $contract_deleted_mail;
    }catch(Exception $e) {
        echo json_encode(array("status"=>false, "message"=>'Something went wrong'));
    }
?>