<?php
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $dirPath = dirname(dirname(__DIR__));
    use PHPShopify\ShopifySDK;
    require($dirPath."/PHPMailer/src/PHPMailer.php");
    require($dirPath."/PHPMailer/src/SMTP.php");
    require($dirPath."/PHPMailer/src/Exception.php");
    include $dirPath."/application/library/config.php";
    $store_installation_data = $db->query("SELECT * FROM install WHERE send_update_billing_mail = 'no'");
    $check_row_count = $store_installation_data->rowCount();
    if($check_row_count){
    $get_store_installation_data = $store_installation_data->fetchAll(PDO::FETCH_ASSOC);
    foreach($get_store_installation_data as $store_data) {
        $store_id = $store_data['id'];
        $store = $store_data['store'];
        $get_billing_status = $db->query("SELECT plan_id,storeInstallOffers.planName as active_plan_name,send_update_billing_mail,store_email,owner_name,memberPlanDetails.planName as member_plan_name,memberPlanDetails.price as member_plan_price, SUM(total_sale) as total_contract_sale FROM storeInstallOffers, install, contract_sale, store_details, memberPlanDetails WHERE memberPlanDetails.id = 3 and contract_sale.store_id = '$store_id' and store_details.store_id = '$store_id' and storeInstallOffers.store_id = '$store_id' and storeInstallOffers.status = '1' and install.id = '$store_id'");
        $get_store_details = $get_billing_status->fetch(PDO::FETCH_ASSOC);

        $billing_update_status = $get_store_details['send_update_billing_mail'];
        $active_plan_name = $get_store_details['active_plan_name'];
        $total_contract_sale = $get_store_details['total_contract_sale'];
        $store_owner = $get_store_details['owner_name'];
        $member_plan_name = $get_store_details['member_plan_name'];
        $member_plan_price = $get_store_details['member_plan_price'];
        if($active_plan_name != 'Basic'){
            if($store_id == '101' || $store_id == '112'){
                $max_contract_sale = 1800;
            }else if($active_plan_name == 'Free_old'){
                $max_contract_sale = 700;
            }else if($active_plan_name == 'Free' || $planName == 'Starter'){
                $max_contract_sale = 500;
            }
        if($total_contract_sale > $max_contract_sale){
            echo 'sale exceeded of the store = '.$store.'<br>';
            $notify_sale_update = '
            <div style="height:100%;width:100%;font-size:14px;font-weight:400;line-height:20px;text-transform:initial;letter-spacing:initial;color:#202223;font-family:-apple-system,BlinkMacSystemFont,San Francisco,Segoe UI,Roboto,Helvetica Neue,sans-serif;margin:0;padding:0">
            <table style="width:100%;border-collapse:collapse;border-spacing:0;margin-top:0;margin-bottom:0;padding:0" cellpadding="0" cellspacing="0">
              <tbody>
                <tr style="margin-top:0;margin-bottom:0;padding:0">
                  <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0"></td>
                  <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0">
                    <table align="center" style="width:100%;border-collapse:initial;border-spacing:0;max-width:470px;text-align:left;border-radius:8px;overflow:hidden;margin:32px auto 0;padding:0;border:1px solid #c9cccf" cellpadding="0" cellspacing="0">
                      <tbody>
                        <tr style="margin-top:0;margin-bottom:0;padding:0">
                          <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0">
                            <table style="width:100%;border-collapse:collapse;border-spacing:0;margin-top:0;margin-bottom:0;padding:0" cellpadding="0" cellspacing="0">
                              <tbody>
                                <tr style="margin-top:0;margin-bottom:0;padding:0">
                                  <td style="margin-top:0;margin-bottom:0;padding:20px;border-width:0">
                                    <table style="width:100%;border-collapse:collapse;border-spacing:0;margin-top:0;margin-bottom:0;padding:0" cellpadding="0" cellspacing="0">
                                      <tbody>
                                        <tr style="margin-top:0;margin-bottom:0;padding:0">
                                          <td style="margin-top:0;margin-bottom:0;padding:0 0 20px;border-width:0">
                                            <table style="width:100%;border-collapse:collapse;border-spacing:0;margin-top:0;margin-bottom:0;padding:0" cellpadding="0" cellspacing="0">
                                              <tbody>
                                                <tr style="margin-top:0;margin-bottom:0;padding:0">
                                                  <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0">
                                                    <p style="color:#202223;line-height:24px;font-size:16px;font-weight:600;margin:0 0 16px;padding:0">
                                                      <strong style="font-size:16px;font-weight:500;color:#202223">Dear '.$store_owner.',</strong>
                                                    </p>
                                                  </td>
                                                </tr><tr style="margin-top: 4px;margin-bottom:0;padding:0">
                                                  <td style="margin-top: 7px;margin-bottom:0;padding:0;border-width:0;font-size: 14px;">
                                                    <div>
                                                      <p style="color:#202223;line-height:20px;margin:0;padding:0"> I hope this email finds you well. We have noticed that you have exceeded your subscription sales limit on <b>Advanced Subscription-Recharge</b>. This is a friendly reminder that in order to continue using our services without interruption, it is necessary to upgrade your plan. </p>
                                                    </div>
                                                  </td>
                                                </tr>
                                                <tr style="margin-top:0;margin-bottom:0;padding:0">
                                                  <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0;font-size: 14px;">
                                                    <div>
                                                      <p style="color:#202223;line-height:20px;margin-top: 14px;padding:0"> Our upgraded plans offer increased sales limits, access to additional features, and improved customer support.  </p>
                                                    </div>
                                                  </td>
                                                </tr><tr style="margin-top:0;margin-bottom:0;padding:0">
                                                  <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0;font-size: 14px;">
                                                    <div>
                                                      <p style="color:#202223;line-height:20px;margin-top: 14px;padding:0"> If you have any questions or concerns, please do not hesitate to contact our support team. We are always here to help. </p>
                                                    </div>
                                                  </td>
                                                </tr><tr style="margin-top:0;margin-bottom:0;padding:0">
                                                  <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0;font-size: 14px;">
                                                    <div>
                                                      <p style="color:#202223;line-height:20px;margin-top: 14px;padding:0;">Thank you for choosing <b>Advanced Subscription-Recharge</b>. We appreciate your business and look forward to serving you.</p>
                                                    </div>
                                                  </td>
                                                </tr><tr style="margin-top:0;margin-bottom:0;padding:0">
                                                  <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0">
                                                    <a href="'.$SHOPIFY_DOMAIN_URL.'/admin/app_plans.php?shop='.$store.'" style="margin-top:16px;margin-bottom:0;color:white;text-decoration:none;font-size:14px;font-weight:400;line-height:1.41;text-transform:initial;letter-spacing:initial;display:inline-block;background-color:#008060;border-radius:4px;padding:0;border-color:#008060;border-style:solid;border-width:8px 16px" target="_blank" >Upgrade Plan</a>
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <table style="width:100%;border-collapse:collapse;border-spacing:0;margin-top:0;margin-bottom:0;padding:0" cellpadding="0" cellspacing="0">
                                      <tbody>
                                        <tr style="margin-top:0;margin-bottom:0;border-top-width:1px;border-top-color:#c9cccf;border-top-style:solid;padding:0">
                                          <td style="margin-top:0;margin-bottom:0;padding:20px 0;border-width:0">
                                            <table style="width:100%;border-collapse:collapse;border-spacing:0;margin-top:0;margin-bottom:0;padding:0" cellpadding="0" cellspacing="0">
                                              <thead>
                                                <tr style="margin-top:0;margin-bottom:0;padding:0">
                                                  <th colspan="2" style="font-weight:600;word-wrap:break-word;word-break:break-word;padding-bottom:20px" align="left">Pricing</th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                <tr style="margin-top:0;margin-bottom:0;padding:0">
                                                  <td style="margin-top:0;margin-bottom:0;word-wrap:break-word;word-break:break-word;padding:0;border-width:0">'.$member_plan_name.' Plan Subscription</td>
                                                  <td style="margin-top:0;margin-bottom:0;white-space:nowrap;padding:0 0 0 8px;border-width:0" align="right" valign="top">$'.$member_plan_price.' USD/month</td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr style="margin-top:0;margin-bottom:0;padding:0">
                          <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0">
                            <table style="width:100%;border-collapse:collapse;border-spacing:0;margin-top:0;margin-bottom:0;text-align:center;padding:0" cellpadding="0" cellspacing="0">
                              <tbody>
                                <tr style="margin-top:0;margin-bottom:0;padding:0">
                                  <td style="margin-top:0;margin-bottom:0;border-radius:8px;border-top-color:#c9cccf;border-top-style:solid;padding:20px 8px 20px 20px;border-width:1px 0 0" align="left" bgcolor="#f9fafb">
                                    <img alt="" width="20" src="'.$SHOPIFY_DOMAIN_URL.'/application/assets/images/information_logo.png" style="height:20px;line-height:0;outline:none;text-decoration:none;vertical-align:top;width:20px;border-width:0">
                                  </td>
                                  <td style="margin-top:0;margin-bottom:0;border-radius:8px;border-top-color:#c9cccf;border-top-style:solid;width:100%;padding:20px 20px 20px 0;border-width:1px 0 0" align="left" bgcolor="#f9fafb">Learn more about and get support for <a href="mailto:your-email.com" style="margin-top:0;margin-bottom:0;color:#2c6ecb;text-decoration:none;padding:0" target="_blank">App Support</a>. </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td style="margin-top:0;margin-bottom:0;padding:0;border-width:0"></td>
                </tr>
              </tbody>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;border-spacing:0;font-size:12px;line-height:20px;color:#6d7175;max-width:470px;margin:32px auto 0;padding:0">
              <tbody>
                <tr style="margin-top:0;margin-bottom:0;padding:0">
                  <td align="center" valign="bottom" style="margin-top:0;margin-bottom:0;padding:0 20px;border-width:0">
                    <img alt="" width="89" src="'.$SHOPIFY_DOMAIN_URL.'/application/assets/images/shopify_logo.png" style="height:auto;line-height:0;outline:none;text-decoration:none;vertical-align:top;margin-bottom:12px;border-width:0">
                  </td>
                </tr>
                <tr style="margin-top:0;margin-bottom:0;padding:0">
                  <td align="center" style="margin-top:0;margin-bottom:0;padding:0 20px;border-width:0">
                    <p style="color:#6d7175;line-height:20px;margin:0;padding:0">
                      <span>Shine Dezign Infotech</span>
                    </p>
                  </td>
                </tr>
              </tbody>
            </table>
            </div>';

        $sendMailArray = array(
          'sendTo' =>  $get_store_details['store_email'],
          'subject' => 'Subscriptions sale exceeded - Upgrade your plan',
          'mailBody' => $notify_sale_update,
          'mailHeading' => "Subscription Sale Exceeded..!!"
        );
        sendMail($sendMailArray, 'true', $store_id, $db, $store);
            }
        }
    }
  }


    function sendMail($sendMailArray, $testMode, $store_id, $db, $store){
        $email_configuration = 'false';
        $email_host =  "email-host";
        $username ="apikey";
        $password = "email-password";
        $from_email = "your-from-email";
        $encryption = 'tls';
        $port_number = 587;
        //For pending mail

        $subject = $sendMailArray['subject'];
        $sendTo = $sendMailArray['sendTo'];
        $email_template_body = $sendMailArray['mailBody'];
        // $email_template_body = $sendMailArray['mailHeading'];
        $mail =  new PHPMailer\PHPMailer\PHPMailer();
        $mail->IsSMTP();
        $mail->CharSet="UTF-8";
        $mail->Host = $email_host;
        $mail->SMTPDebug = 1;
        $mail->Port = $port_number ; //465 or 587
        $mail->SMTPDebug = false;
        $mail->SMTPSecure = $encryption;
        $mail->SMTPAuth = true;
        $mail->IsHTML(true);
        //Authentication
        $mail->Username = $username;
        $mail->Password = $password;
        //Set Params
        $mail->addReplyTo('your-reply-email.com');
        $mail->SetFrom($from_email,'Action Required');
        $mail->AddAddress($sendTo);

        $mail->Subject = $subject;
        $mail->Body = $email_template_body;
        if(!$mail->Send()) {
          echo 'mail not sent';
            return json_encode(array("status"=>false, "message"=>$mail->ErrorInfo));
        } else {
            $update_send_mail = $db->prepare("UPDATE install SET send_update_billing_mail = 'yes' WHERE store = '$store'");
            $update_send_mail->execute();
            echo 'mail sent';
        }
    }


