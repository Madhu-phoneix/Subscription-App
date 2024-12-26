<?php
    $order_id = $_GET['order_id'];
    // $store = $_REQUEST['store'];
    $dirPath = dirname(__DIR__);
    require '../../vendor/autoload.php';
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
        $contract_deleted_mail = sendMail($sendMailArray,$store_id,$db);
        unlink($filename);
        echo $contract_deleted_mail;
    }catch(Exception $e) {
        echo json_encode(array("status"=>false, "message"=>'Something went wrong'));
    }

    function sendMail($sendMailArray,$store_id, $db){
        //general mail configuration
        $email_configuration = 'false';
        $email_host = "your-email-host";
        $username = "apikey";
        $password = "email_password";
        $from_email = "your-from-email";
        $encryption = 'tls';
        $port_number = 587;

        // check if the email configuration setting exist and email enable is checked
        $subject = $sendMailArray['subject'];
        $sendTo = $sendMailArray['sendTo'];
        $mailBody = $sendMailArray['mailBody'];
        $mailHeading = $sendMailArray['mailHeading'];
        $email_configuration_query = $db->query("SELECT * FROM email_configuration WHERE store_id = '$store_id'");
        $email_configuration_data = $email_configuration_query->fetch(PDO::FETCH_ASSOC);
        if ($email_configuration_data)
        {
            if ($email_configuration_data['email_enable'] == 'checked')
            {
                $email_host = $email_configuration_data['email_host'];
                $username = $email_configuration_data['username'];
                $password = $email_configuration_data['password'];
                $from_email = $email_configuration_data['from_email'];
                $encryption = $email_configuration_data['encryption'];
                $port_number = $email_configuration_data['port_number'];
                $email_configuration = 'true';
            }
        }
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->Host = $email_host;
        $mail->SMTPDebug = 1;
        $mail->Port = $port_number; //465 or 587
        $mail->SMTPDebug = false;
        $mail->SMTPSecure = $encryption;
        $mail->SMTPAuth = true;
        $mail->IsHTML(true);
        //Authentication
        $mail->Username = $username;
        $mail->Password = $password;
        if($sendMailArray['ccc_email']){
            $mail->addCC($sendMailArray['ccc_email']);
        }
        if($sendMailArray['bcc_email']){
            $mail->addBCC($sendMailArray['bcc_email']);
        }
        if($sendMailArray['reply_to']){
            $mail->addReplyTo($sendMailArray['reply_to']);
        }
        //Set Params
        if(($email_configuration_data) && ($email_configuration_data['email_enable'] == 'checked')){
            $mail->SetFrom($username,$from_email);
        }else{
            $mail->SetFrom($from_email);
        }
        if (is_array($sendTo))
        {
            $mail->AddAddress($sendTo[0]);
            $mail->AddAddress($sendTo[1]);
            $decrease_counter = 2;
        }
        else
        {
            $mail->AddAddress($sendTo);
            $decrease_counter = 1;
        }
        $mail->Subject = $subject;
        $mail->Body = $mailBody;
        $mail->addAttachment($sendMailArray['mailAttachment']);
        if (!$mail->Send())
        {
            echo json_encode(array(
                "status" => false,
                "message" => $mail->ErrorInfo
            ));
        }
        else
        {
            echo json_encode(array(
                "status" => true,
                "message" => 'Email Sent Successfully'
            ));
        }
    }

?>