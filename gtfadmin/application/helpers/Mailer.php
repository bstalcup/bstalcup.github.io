<?php

class My_Helper_Mailer extends Zend_Controller_Action_Helper_Abstract
{
	public function direct($script, $subject, $toName, $toEmail, array $viewVars = array(), $layoutName = 'email', $fromName = null, $fromEmail = null, $testOutput = false)
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$layout = Zend_Layout::getMvcInstance();

		$econf = My_Core::getConfig()->mail;

		if ($econf->smtp->enabled)
		{
			$config = null;
			if ($econf->smtp->auth)
				$config = $econf->smtp->config->toArray();
			$tr = new Zend_Mail_Transport_Smtp($econf->smtp->server, $config);
			Zend_Mail::setDefaultTransport($tr);
		}

		// store original view variables
		$oVars = $viewRenderer->view->getVars();
		$viewRenderer->view->name = $toName;
		$viewRenderer->view->email = $toEmail;

		foreach ($viewVars as $key => $value)
			$viewRenderer->view->{$key} = $value;

		$layout->content = $viewRenderer->view->render($script);

		$mailHtml = $layout->render($layoutName);
		$mailText = str_replace("\t", "", trim(strip_tags($mailHtml)));

		// for testing purposes
		if ($testOutput)
		{
			echo $mailHtml;
			echo '<hr />';
			echo '<pre>' . $mailText . '</pre>';
			die();
		}

		// clear all view variables
		$viewRenderer->view->clearVars();

		// restore original variables
		foreach ($oVars as $key => $value)
			$viewRenderer->view->{$key} = $value;

		Zend_Mail::setDefaultReplyTo($econf->from->reply->email, $econf->from->reply->name);

		$mail = new Zend_Mail();
		$mail->setBodyText($mailText);
		$mail->setBodyHtml($mailHtml);
		if ($fromName && $fromEmail) $mail->setFrom($fromEmail, $fromName);
			else $mail->setFrom($econf->from->email, $econf->from->name);

		if (strpos($toEmail, ';') !== false)
		{
			$ems = array();
			foreach (explode(';', $toEmail) as $em)
				$ems[] = trim($em);
			$toEmail = $ems;
		}

		if (isset($econf->to->overwrite)) $mail->addTo($econf->to->overwrite->email, $econf->to->overwrite->name);
			else $mail->addTo($toEmail, $toName);

		/*
		foreach ($econf->to->cc->{$service} as $cc)
			$mail->addCc($cc->email, $cc->name);
		*/
		$mail->setSubject($subject);

		return $mail;
	}
}