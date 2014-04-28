<?php

/* 
 * For local settings :  USE_SMTP = false, SMTP_NAME = localhost,SMTP_HOST = localhost,SMTP_PORT = 25 are sufficient
 * 
 * For SMTP settings  :  Use your smtp settings in the available options  with USE_SMTP = true
 *  
 */
return array(
    "EMAIL" => array(
        "SMTP_SENDER_TYPE" => "user",
        "SMTP_NAME" => "localhost",
        "SMTP_HOST" => "localhost",
        "SMTP_PORT" => "25",
        "SMTP_CONNECTION_CLASS" => "plain",
        "SMTP_USERNAME" => "",
        "SMTP_PASSWORD" => "",
        "SMTP_SSL" => "",
        "BODY" => "Custom default message",
        "FROM" => "noreply@example.com",
        "TO" => "yoremail@example.com",
        "MAIL_FROM_NICK_NAME" => "Custom Name",
        "SUBJECT" => "Custom Subject",
        "FROM_NICK_NAME" => "Cutom From",
        "EMAIL_TEMPLATE_PATH" => __DIR__ . "/../view/emails/templates/",
        "USE_SMTP" => FALSE
    )    
);