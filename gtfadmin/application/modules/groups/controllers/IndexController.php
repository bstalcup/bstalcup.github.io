<?php

class Groups_IndexController extends My_Controller
{
	public $ajaxable = array(
							'viewgroups' => array('json'),
							'newgroup' => array('json'),
							'deletegroup' => array('json'),
							'editgroup' => array('json')
						);

	public function init()
	{
		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function viewgroupsAction()
	{
		$request = $this->getRequest();

		// get logged in user parish
		$filters['parish'] = $this->view->user->parish->objectId;

		// extract the required families and take care of pagination
		$groupsCount = My_Group::getGroupsCount($filters);
		$orderBy = 'name';
		if ($groupsCount > 0)
		{
			$offset = My_Core::getConfig()->family->itemsPerPage;
			$pagination = new My_Pagination($groupsCount, $offset, array($offset, 'All'));
			$pagination->setPagesPerSection(7);
			$startElem	= $pagination->getEntryStart();
			$offset = $pagination->getEntryEnd();
			$this->view->groups = My_Group::getGroups($filters, $startElem, $offset, $orderBy);
			$this->view->pagination = $pagination;
		}
		$this->view->groupsCount = $groupsCount;
		$this->view->content = $this->view->render('index/viewgroups.phtml');
	}

	public function newgroupAction()
	{
		$request = $this->getRequest();

		// saving the group
		if ($request->isPost() && $request->getParam('name'))
		{
			$values = $request->getPost();
			$values['parish'] = $this->view->user->parish->objectId;
			
			//add group and let the user know about it :)
			if (!My_Group::addGroup($values))
				$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save this group. Please try again.")');
			else
				$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully added this group.");');

			$this->view->showContent = 'yes';
		}

		$this->view->content = $this->view->render('index/newgroup.phtml');
	}

	public function deletegroupAction()
	{
		$request = $this->getRequest();

		if (!$request->getParam('group_id') || !My_Group::deletegroup($request->getParam('group_id')))
			return $this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. Something went wrong. Please refresh the page and try again.")');
		
		$this->view->result = array("success" => true);
	}

	public function editgroupAction()
	{
		$request = $this->getRequest();

		// the group is sent from the initial call(view groups) - so we wont request for it again
		if ($request->getParam('group') && is_array($request->getParam('group')))
			$this->view->group = $request->getParam('group');

		// saving the group
		if (trim($request->getParam('name')))
		{
			$values = $request->getPost();

			//update group and let the user know about it :)
			if (!My_Group::updateGroup($request->getParam('group_id'), $values))
				$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save your information. Please try again.")');
			else
				$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully updated this group.");');

			$this->view->group = array(
				'name' => trim($request->getParam('name')),
				'phone' => trim($request->getParam('phone')),
				'email' => trim($request->getParam('email')),
				'website' => trim($request->getParam('website'))
			);
			$this->view->showContent = 'yes';
		}

		// we must request for the family as we don't have it sent to us
		if (!isset($this->view->group))
			$this->view->group = json_decode(json_encode(My_Group::getGroupById($request->getParam('group_id'))), true);

		if ($this->getRequest()->isXmlHttpRequest())
			$this->view->content = $this->view->render('index/newgroup.phtml');
		else 
			echo $this->view->render('index/newgroup.phtml');
	}
}