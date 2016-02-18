<?php

/**
 * Class Sheep_Debug_Test_Model_Core_Email_Template
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Model_Core_Email_Template
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Model_Core_Email_Template extends EcomDev_PHPUnit_Test_Case
{

    public function testRewrite()
    {
        $model = Mage::getModel('core/email_template');
        $this->assertNotNull($model);
        $this->assertInstanceOf('Mage_Core_Model_Email_Template', $model);
        $this->assertInstanceOf('Sheep_Debug_Model_Core_Email_Template', $model);
    }


    public function testSend()
    {
        $mail = $this->getMock('Zend_Mail');

        $model = $this->getModelMock('core/email_template', array('getMail', 'parentSend', 'addEmailToProfile'));
        $model->expects($this->once())->method('getMail')->willReturn($mail);

        // Parameters are passed to parrent method
        $model->expects($this->once())->method('parentSend')
            ->with('mario@mailinator.com', 'Mario', array('store' => 'Pirate Sheep'))
            ->willReturn(true);
        $model->expects($this->once())->method('addEmailToProfile')->with(
            'mario@mailinator.com',
            'Mario',
            array('store' => 'Pirate Sheep'),
            true,
            $mail
        );

        $model->send('mario@mailinator.com', 'Mario', array('store' => 'Pirate Sheep'));
    }


    public function testAddEmailtoProfile()
    {
        $model = $this->getModelMock('core/email_template', array('decodeSubject', 'getContent', 'getSenderName', 'getSenderEmail', 'isPlain', 'send'));
        $model->expects($this->once())->method('decodeSubject')->with('encoded e-mail subject')->willReturn('e-mail subject');
        $model->expects($this->once())->method('getSenderName')->willReturn('Mario Sender');
        $model->expects($this->once())->method('getSenderEmail')->willReturn('mario+sender@mailinator.com');
        $model->expects($this->once())->method('isPlain')->willReturn(true);


        $emailMock = $this->getModelMock('sheep_debug/email',
            array('setFromName', 'setFromEmail', 'setToEmail', 'setToName', 'setSubject', 'setIsPlain', 'setBody', 'setIsAccepted', 'setVariables'));
        $this->replaceByMock('model', 'sheep_debug/email', $emailMock);
        $emailMock->expects($this->once())->method('setFromName')->with('Mario Sender');
        $emailMock->expects($this->once())->method('setFromEmail')->with('mario+sender@mailinator.com');
        $emailMock->expects($this->once())->method('setToEmail')->with('mario@mailinator.com');
        $emailMock->expects($this->once())->method('setToName')->with('Mario');
        $emailMock->expects($this->once())->method('setSubject')->with('e-mail subject');
        $emailMock->expects($this->once())->method('setIsPlain')->with(true);
        $emailMock->expects($this->once())->method('setBody')->with('e-mail body');
        $emailMock->expects($this->once())->method('setIsAccepted')->with(true);
        $emailMock->expects($this->once())->method('setVariables')->with(array('store' => 'Pirate Sheep'));


        $requestInfo = $this->getModelMock('sheep_debug/requestInfo', array('addEmail'));
        $requestInfo->expects($this->once())->method('addEmail')->with($emailMock);

        $observer = $this->getModelMock('sheep_debug/observer', array('getRequestInfo'));
        $this->replaceByMock('singleton', 'sheep_debug/observer', $observer);
        $observer->expects($this->any())->method('getRequestInfo')->willReturn($requestInfo);


        $mail = $this->getMock('Zend_Mail', array('getSubject'));
        $mail->expects($this->any())->method('getSubject')->willReturn('encoded e-mail subject');
        $model->expects($this->once())->method('getContent')->with($mail)->willReturn('e-mail body');



        $model->addEmailToProfile('mario@mailinator.com', 'Mario', array('store' => 'Pirate Sheep'), true, $mail);
    }


    public function testGetContentForPlain()
    {
        $content = $this->getMock('Zend_Mime_Part', array('getRawContent'), array(), '', false);;
        $content->expects($this->once())->method('getRawContent')->willReturn('raw content');

        $mail = $this->getMock('Zend_Mail', array('getBodyText', 'getBodyHtml'));
        $mail->expects($this->once())->method('getBodyText')->willReturn($content);
        $mail->expects($this->never())->method('getBodyHtml');

        $model = $this->getModelMock('core/email_template', array('isPlain'));
        $model->expects($this->any())->method('isPlain')->willReturn(true);

        $actual = $model->getContent($mail);
        $this->assertEquals('raw content', $actual);
    }


    public function testContentForNonPlain()
    {
        $content = $this->getMock('Zend_Mime_Part', array('getRawContent'), array(), '', false);;
        $content->expects($this->once())->method('getRawContent')->willReturn('raw html content');

        $mail = $this->getMock('Zend_Mail', array('getBodyText', 'getBodyHtml'));
        $mail->expects($this->never())->method('getBodyText');
        $mail->expects($this->once())->method('getBodyHtml')->willReturn($content);

        $model = $this->getModelMock('core/email_template', array('isPlain'));
        $model->expects($this->any())->method('isPlain')->willReturn(false);

        $actual = $model->getContent($mail);
        $this->assertEquals('raw html content', $actual);
    }


    public function testDecodeSubject()
    {
        $model = $this->getModelMock('core/email_template', array('send'));
        $this->assertEquals('Hello, world', $model->decodeSubject('=?utf-8?B?SGVsbG8sIHdvcmxk?='));
    }

}
