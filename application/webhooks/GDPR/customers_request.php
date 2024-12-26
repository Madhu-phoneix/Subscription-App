    <?php
        $dirPath = dirname(dirname(dirname(__DIR__)));
        include $dirPath."/application/library/config.php";
        require ($dirPath . "/PHPMailer/src/PHPMailer.php");
        require ($dirPath . "/PHPMailer/src/SMTP.php");
        require ($dirPath . "/PHPMailer/src/Exception.php");
        $store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
        $json_str = file_get_contents('php://input');
        file_put_contents($dirPath."/application/assets/txt/gdpr/customers_request.txt",$json_str);
        $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
        function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
            $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
            return hash_equals($hmac_header, $calculated_hmac);
        }
        $verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
        if ($verified) {
            $json_obj = json_decode($json_str,true);
            $customer_id = $json_obj['customer']['id'];
            $get_store_details_query = $db->query("SELECT install.id,store_details.store_email from install INNER JOIN store_details ON install.id = store_details.store_id where install.store = '$store'");
            $get_store_details = $get_store_details_query->fetch(PDO::FETCH_ASSOC);
            $store_email = $get_store_details['store_email'];
            $store_id =  $get_store_details['id'];

            $getCustomerData_query = $db->query("SELECT payment_instrument_type,payment_instrument_value,subscriptionOrderContract.contract_id,email,sa.address1 as shippingAddress1,sa.address2 as shippingAddress2,sa.province as shippingProvince,sa.country as shippingCountry,sa.city as shippingCity,sa.zip as shippingZip , ba.address1 as billingAddress1,ba.address2 as billingAddress2, ba.province as billingProvince,ba.country as billingCountry,ba.city as billingCity, ba.zip as billingZip  FROM subscriptionOrderContract
            INNER JOIN customerContractPaymentmethod
            INNER JOIN customers
            INNER JOIN subscriptionContractShippingAddress AS sa
            INNER JOIN subscriptionContractBillingAddress AS ba
            ON subscriptionOrderContract.shopify_customer_id = customerContractPaymentmethod.shopify_customer_id where customerContractPaymentmethod.shopify_customer_id=$customer_id and subscriptionOrderContract.shopify_customer_id=$customer_id and customers.shopify_customer_id = $customer_id and  subscriptionOrderContract.contract_id = sa.contract_id and subscriptionOrderContract.contract_id = ba.contract_id  GROUP BY subscriptionOrderContract.contract_id");
            $getCustomerData = $getCustomerData_query->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($getCustomerData)){
                $cr = "\n";
                $data =  "Contract Id". ',' ."Payment Instrument Type" . ',' . "Card Holder Name" . ','. "Credit Card last Digits". ',' ."Shipping Address Line 1".',' ."Shipping Address Line 2".',' ."Shipping Province".',' ."Shipping Country".',' ."Shipping City".',' ."Shipping Zip".',' ."Billing Address Line 1".',' ."Billing Address Line 2".',' ."Billing Province".',' ."Billing Country".',' ."Billing City".','."Billing Zip".$cr;

                foreach($getCustomerData as $key=>$value){
                    $payment_instrument_type = $value['payment_instrument_type'];
                    $payment_instrument_value = json_decode($value['payment_instrument_value']);
                    $card_holder_name = $payment_instrument_value->name;
                    $last_digits = $payment_instrument_value->last_digits;
                    $contract_id = $value['contract_id'];
                    $shippingAddress1 = str_replace(","," ",$value['shippingAddress1']);
                    $shippingAddress2 = str_replace(","," ",$value['shippingAddress2']);
                    $shippingProvince = $value['shippingProvince'];
                    $shippingCountry = $value['shippingCountry'];
                    $shippingCity = $value['shippingCity'];
                    $shippingZip = $value['shippingZip'];
                    $billingAddress1 =  str_replace(","," ",$value['billingAddress1']);
                    $billingAddress2 = str_replace(","," ",$value['billingAddress2']);
                    $billingProvince = $value['billingProvince'];
                    $billingCountry = $value['billingCountry'];
                    $billingCity = $value['billingCity'];
                    $billingZip = $value['billingZip'];
                    $data .= "$contract_id" . ',' . "$payment_instrument_type" . ',' . "$card_holder_name". ',' . "$last_digits". ',' . "$shippingAddress1". ',' . "$shippingAddress2". ',' . "$shippingProvince". ',' . "$shippingCountry". ',' . "$shippingCity". ',' . "$shippingZip" . ',' . "$billingAddress1". ',' . "$billingAddress2". ',' . "$billingProvince". ',' . "$billingCountry". ',' . "$billingCity". ',' . "$billingZip" . $cr;
                }
                $fp = fopen('customer_request_data.csv','a');
                fwrite($fp,$data);
                fclose($fp);

                $sendMailArray = array(
                    'sendTo' =>  $store_email,
                    'subject' => 'Customer Data',
                    'mailBody' => 'Find the customer data that you requested for the customer id #'.$customer_id,
                    'mailHeading' => 'Customer Data Request',
                );
                sendMail($sendMailArray,'false',$store_id, $db, $store);
            }else{
                $sendMailArray = array(
                    'sendTo' =>  $store_email,
                    'subject' => 'Customer Data',
                    'mailBody' => 'Our app is not using the customer data that you requested for the customer id #'.$customer_id,
                    'mailHeading' => 'Customer Data Request',
                );
                sendMail($sendMailArray,'false',$store_id, $db, $store);
            }
            http_response_code(200);
            $db = null;
    }else{
        http_response_code(401);
    }

    function sendMail($sendMailArray, $testMode, $store_id, $db, $store)
    {
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
        $store_detail_query = $db->query("SELECT pending_emails,store_email,shop_name FROM email_counter,store_details WHERE email_counter.store_id='$store_id' and store_details.store_id = '$store_id'");
        $store_detail = $store_detail_query->fetch(PDO::FETCH_ASSOC);
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
        //Set Params
        $mail->addReplyTo($store_detail['store_email'], $store_detail['shop_name']);
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
        if(file_exists('customer_request_data.csv')){
           $mail->addAttachment('customer_request_data.csv');
        }
        $mail->Body = $mailBody;
        if (!$mail->Send())
        {
           echo $mail->ErrorInfo;
        }
        else
        {
            echo 'Email Sent Successfully';
        }
        if(file_exists('customer_request_data.csv')){
          unlink('customer_request_data.csv');
        }
    }

