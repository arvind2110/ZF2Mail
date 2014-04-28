ZF2Mail
=======

ZF2-Mail is a simple ZF2 module to implement and manage mail functionality in ZF2 applications.


SMTP settings : module.config.php

```php
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

```

Sending Mails

```php

$mail = $this->getServiceLocator()->get('ZF2Mail');
        
$mailOptions = array(
    'mailTo' => 'arvind.singh.2110@gmail.com',
    'mailFrom' => 'example@example.com',
    'mailSubject' => 'My Custom Subject',
    'mailBody' => 'My custom message',
    'templateFile' => 'custom-message',
    'params' => array('name' => 'Arvind Singh')
);
$mail->sendMail($mailOptions);

```

You can pass other parameters as per your requirement.
