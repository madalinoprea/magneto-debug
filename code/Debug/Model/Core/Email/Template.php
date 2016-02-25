<?php

/**
 * Class Sheep_Debug_Model_Core_Email_Template rewrites core/email_template class in order
 * to capture e-mail variables.
 *
 * @method string getSenderName()
 * @method string getSenderEmail()
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
trait Sheep_Debug_Model_Core_Email_Template_Capture
{

    /**
     * Calls real send() method
     *
     * @param array|string $email
     * @param null         $name
     * @param array        $variables
     * @return bool
     */
    public function parentSend($email, $name = null, array $variables = array())
    {
        return parent::send($email, $name, $variables);
    }


    /**
     * Overwrites parent method to capture e-mail details
     *
     * @param array|string $email
     * @param null         $name
     * @param array        $variables
     * @return bool
     */
    public function send($email, $name = null, array $variables = array())
    {
        // store a reference to mail object that get populate by parent send()
        $zendMail = $this->getMail();

        $result = $this->parentSend($email, $name, $variables);

        try {
            $this->addEmailToProfile($email, $name, $variables, $result, $zendMail);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $result;
    }


    /**
     * Adds e-mail information on request profiler
     *
     * @param           $email
     * @param           $name
     * @param           $variables
     * @param           $result
     * @param Zend_Mail $mail
     */
    public function addEmailToProfile($email, $name, $variables, $result, Zend_Mail $mail)
    {
        $emailCapture = Mage::getModel('sheep_debug/email');

        $subject = $this->decodeSubject($mail->getSubject());
        $body = $this->getContent($mail);

        $emailCapture->setFromName($this->getSenderName());
        $emailCapture->setFromEmail($this->getSenderEmail());

        $emailCapture->setToEmail($email);
        $emailCapture->setToName($name);
        $emailCapture->setSubject($subject);
        $emailCapture->setIsPlain($this->isPlain());
        $emailCapture->setBody($body);
        $emailCapture->setIsAccepted($result);
        $emailCapture->setVariables($variables);

        Mage::getSingleton('sheep_debug/observer')->getRequestInfo()->addEmail($emailCapture);
    }


    /**
     * Returns raw content attached to specified mail object
     *
     * @param Zend_Mail $mail
     * @return string
     */
    public function getContent(Zend_Mail $mail)
    {
        $hasQueue = $this->hasQueue();

        if ($hasQueue && $queue = $this->getQueue()) {
            return $queue->getMessageBody();
        }

        /** @var Zend_Mime_Part $content */
        $content = $this->isPlain() ? $mail->getBodyText() : $mail->getBodyHtml();

        return $content ? $content->getRawContent() : '';
    }


    /**
     * Returns raw subject
     *
     * @param $subject
     * @return string
     */
    public function decodeSubject($subject)
    {
        if ($this->hasQueue() && $queue = $this->getQueue()) {
            return $queue->getMessageParameters('subject');
        }

        return base64_decode(substr($subject, strlen('=?utf-8?B?'), -1 * strlen('?=')));
    }

}


if (Mage::helper('core')->isModuleEnabled('Aschroder_SMTPPro')) {

    class Sheep_Debug_Model_Core_Email_Template extends Aschroder_SMTPPro_Model_Email_Template
    {
        use Sheep_Debug_Model_Core_Email_Template_Capture;
    }

} else {

    class Sheep_Debug_Model_Core_Email_Template extends Mage_Core_Model_Email_Template
    {
        use Sheep_Debug_Model_Core_Email_Template_Capture;
    }
}
