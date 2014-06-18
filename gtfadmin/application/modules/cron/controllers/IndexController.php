<?php

class Cron_IndexController extends My_Controller
{
	public function init()
	{
		parent::init();
		set_time_limit(0);
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	}
	/*
	 * Main cleanup job 
	 */
	public function newfamiliesAction() 
	{
		$request = $this->getRequest();
		echo date('[Y-m-d H:i:s] - ') . "[Start] \"New Families job\".\n";
		ob_flush();

		// get the parishes that have a user set and are enabled
		$filters['whereExists'] = 'user';
		$filters['whereInclude'] = 'user';
		$filters['isOneParishEnabled'] = true;
		$parishes = My_Parish::getParishes($filters);

		// get the all the new, not-approved families from the resulted parishes above
		$filters = array();
		$filters['createdAt'] = date('Y-m-d');
		$filters['approved'] = false;
		$filters['containedIn']['name'] = 'parish';
		$filters['containedIn']['values'] = array();
		foreach ($parishes as $key => $parish) 
		{
			if (isset($parish->user))
			{
				$filters['containedIn'][1][] = array(
					"__type" => "Pointer",
					"className" => 'Parish',
					"objectId" => $parish->objectId
				);
			}
		}
		$newFamilies = My_Family::getFamilies($filters);
		
		foreach ($parishes as $key => $parish) {
			$mailArr[$key] = array(
				'pName' => $parish->name,
				'pAdminEmail' => is_string($parish->user->email) && $parish->user->email != '' ? $parish->user->email : ''
			);
			$familyCount = 0;
			foreach ($newFamilies as $k => $family) {
				if ($family->parish->objectId == $parish->objectId)
					$familyCount++;
			}
			$mailArr[$key]['familyCount'] = $familyCount;
		}		

		foreach ($mailArr as $key => $mailData) {
			if ($mailData['pAdminEmail'] != '' && $mailData['familyCount'] > 0)
			{
				$email = $this->_helper->Mailer(
					'emails/newfamilies.phtml', 
					'New families on "'. $mailData['pName'] .'" parish', 
					'Parish Admin',
					$mailData['pAdminEmail'],
					array(
						'pName'		  => $mailData['pName'],
						'familyCount' => $mailData['familyCount'],
						'approveLink' => My_Core::getApplicationUri() . $this->view->links('approve_familes')
					),
					'layout_blank'
				);
				$email->send();
			}
		}

		echo date('[Y-m-d H:i:s] - ') . "[End] \"New Families job\".\n";
		ob_flush();
	}
}
