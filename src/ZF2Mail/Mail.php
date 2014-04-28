<?php
namespace ZF2Mail;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Part;
use Zend\Mime\Mime;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Model\ViewModel;

class Mail
{

    private $serviceManager = null;

    private $useSMTP = false;

    private $smtpName = '';

    private $smtpHost = '';

    private $smtpPort = '';

    private $smtpConnectionClass = '';

    private $smtpUsername = '';

    private $smtpPassword = '';

    private $smtpSsl = '';

    private $mailBody = '';

    private $mailFrom = '';

    private $mailSubject = '';

    private $mailFromNickName = '';

    private $mailTo = '';

    private $mailSenderType = '';

    private $mailCc = array();

    private $mailBcc = array();

    private $fileNames = array();

    private $filePaths = array();

    private $params = array();

    private $templateFile = '';

    private $templatePath = '';

    public function __construct($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        $emailConfig = $this->_getConfig("EMAIL");
        
        $this->useSMTP = $emailConfig['USE_SMTP'];
        $this->smtpSsl = $emailConfig['SMTP_SSL'];
        $this->smtpName = $emailConfig['SMTP_NAME'];
        $this->smtpHost = $emailConfig['SMTP_HOST'];
        $this->smtpPort = $emailConfig['SMTP_PORT'];
        $this->smtpUsername = $emailConfig['SMTP_USERNAME'];
        $this->smtpPassword = $emailConfig['SMTP_PASSWORD'];
        $this->smtpConnectionClass = $emailConfig['SMTP_CONNECTION_CLASS'];
        
        $this->mailBody = $emailConfig['BODY'];
        $this->mailFrom = $emailConfig['FROM'];
        $this->mailSubject = $emailConfig['SUBJECT'];
        $this->mailFromNickName = $emailConfig['FROM_NICK_NAME'];
        $this->mailTo = $emailConfig['TO'];
        $this->mailSenderType = $emailConfig['SMTP_SENDER_TYPE'];
        $this->templatePath = $emailConfig['EMAIL_TEMPLATE_PATH'];
    }

    public function sendMail($mailOptions = array())
    {
        $this->_setMailOptions($mailOptions);
        
        $text = new Part($this->mailBody);
        $text->type = Mime::TYPE_HTML;
        $mailBodyParts = new MimeMessage();
        $mailBodyParts->addPart($text);
        if (! empty($this->fileNames) && ! empty($this->filePaths)) {
            foreach ($this->filePaths as $key => $filePath) {
                $file = new Part(file_get_contents($filePath));
                $file->encoding = Mime::ENCODING_BASE64;
                $file->type = finfo_file(finfo_open(), $filePath, FILEINFO_MIME_TYPE);
                $file->disposition = Mime::DISPOSITION_ATTACHMENT;
                $file->filename = $this->fileNames[$key];
                $mailBodyParts->addPart($file);
            }
        }
        
        $options = array();
        
        if ($this->useSMTP === false) {
            $options = new SmtpOptions(array(
                "name" => $this->smtpName,
                "host" => $this->smtpHost,
                "port" => $this->smtpPort
            ));
        } else {
            $options = new SmtpOptions(array(
                'name' => $this->smtpName,
                'host' => $this->smtpHost,
                'port' => $this->smtpPort,
                'connection_class' => $this->smtpConnectionClass,
                'connection_config' => array(
                    'ssl' => $this->smtpSsl,
                    'username' => $this->smtpUsername,
                    'password' => $this->smtpPassword
                )
            ));
        }
        
        $mail = new Message();
        $mail->setBody($mailBodyParts);
        $mail->setFrom($this->mailFrom, $this->mailFromNickName);
        $mail->addTo($this->mailTo);
        if (! empty($this->mailCc)) {
            $mail->addCc($this->mailCc);
        }
        if (! empty($this->mailBcc)) {
            $mail->addBcc($this->mailBcc);
        }
        $mail->setSubject($this->mailSubject);
        $transport = new SmtpTransport();
        $transport->setOptions($options);
        $emailLogInfo = array(
            'email_to' => $this->mailTo,
            'email_from' => $this->mailFrom,
            'email_body' => $this->mailBody,
            'email_subject' => $this->mailSubject,
            'sender_type' => $this->mailSenderType
        );
        
        $emailSend = 0;
        try {
            $transport->send($mail);
            $emailSend = 1;
        } catch (\Exception $e) {
            $emailLogInfo['email_error'] = $e->getMessage();
            throw $e;
        }
        return $emailSend;
    }

    private function _setMailOptions($mailOptions)
    {
        if (array_key_exists('useSMTP', $mailOptions)) {
            $this->useSMTP = $mailOptions['useSMTP'];
        }
        if (array_key_exists('mailTo', $mailOptions)) {
            $this->mailTo = $mailOptions['mailTo'];
        }
        if (array_key_exists('mailCc', $mailOptions)) {
            $this->mailCc = $mailOptions['mailCc'];
        }
        if (array_key_exists('mailBcc', $mailOptions)) {
            $this->mailBcc = $mailOptions['mailBcc'];
        }
        if (array_key_exists('mailFrom', $mailOptions)) {
            $this->mailFrom = $mailOptions['mailFrom'];
        }
        if (array_key_exists('mailFromNickName', $mailOptions)) {
            $this->mailFromNickName = $mailOptions['mailFromNickName'];
        }
        if (array_key_exists('mailSubject', $mailOptions)) {
            $this->mailSubject = $mailOptions['mailSubject'];
        }
        if (array_key_exists('mailBody', $mailOptions)) {
            $this->mailBody = $mailOptions['mailBody'];
        }
        if (array_key_exists('sender_type', $mailOptions)) {
            $this->mailSenderType = $mailOptions['sender_type'];
        }
        if (array_key_exists('fileNames', $mailOptions)) {
            $this->fileNames = $mailOptions['fileNames'];
        }
        if (array_key_exists('filePaths', $mailOptions)) {
            $this->filePaths = $mailOptions['filePaths'];
        }
        if (array_key_exists('params', $mailOptions)) {
            $this->params = $mailOptions['params'];
        }
        if (array_key_exists('templateFile', $mailOptions)) {
            $this->templateFile = $mailOptions['templateFile'];
            $this->mailBody = $this->_readTemplate();
        }
    }

    private function _getConfig($key)
    {
        $config = $this->serviceManager->get('config');
        
        if (! empty($key)) {
            return $config[$key];
        }
        return $config;
    }

    private function _readTemplate()
    {
        $config = $this->serviceManager->get('config');
        $templateFile = $this->templatePath . $this->templateFile . ".phtml";
        $view = new PhpRenderer();
        $resolver = new TemplateMapResolver();
        
        $resolver->setMap(array(
            'mailTemplate' => $templateFile
        ));
        $view->setResolver($resolver);
        
        $viewModel = new ViewModel();
        $viewModel->setTemplate('mailTemplate');
        $viewModel->setVariables($this->params);
        $content = $view->render($viewModel);
        return $content;
    }
}