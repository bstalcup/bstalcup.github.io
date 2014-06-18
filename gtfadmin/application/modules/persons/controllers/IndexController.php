<?php

class Persons_IndexController extends My_Controller
{
	public $ajaxable = array(
							'addperson' => array('json'),
							'deleteperson' => array('json'),
							'editperson' => array('json'),
						);

	public function init()
	{
		if (!$this->view->user->isLoggedIn())
		{
			if (!$this->getRequest()->isXmlHttpRequest())
				$this->_redirect($this->view->links('login'));
			else
				throw new Exception('You are not logged in.', 401);
		}

		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function addpersonAction()
	{
		$request = $this->getRequest();

		// adding the new member
		if ($request->getParam("addNewMember"))
		{
			$values = $request->getPost();
			// removing the save value --> it's only for reference
			unset($values['addNewMember']);
			unset($values['picture']);

			// upload the file if there is one and attach it to the rest of the values
			if (isset($_FILES['picture']))
			{
				$fileName = My_File::uploadFile($_FILES['picture']['type'], file_get_contents($_FILES['picture']['tmp_name']), $_FILES['picture']['name']);
				$values['picture'] = $fileName ? $fileName : '';
			}
			$values['parish'] = $request->getParam('parish');
			$values['family'] = $request->getParam('family_id');

			//add the member and let the user know about it :)
			if (!My_Person::addPerson($values))
				$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save your information. Please try again.")');
			else
				$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully added this family member.");');

			return $this->_forward('familydetails', 'index', 'families', array('personSaved' => 1));
		}

		return $this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save your information. Please try again.")');
	}

	public function deletepersonAction()
	{
		$request = $this->getRequest();

		if (!$request->getParam('person_id') || !My_Person::deletePerson($request->getParam('person_id'), $request->getParam('user_id'), $request->getParam('file_name')))
			return $this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. Something went wrong. Please refresh the page and try again.")');
		
		$this->view->result = array("success" => true);
	}

	public function editpersonAction()
	{
		$request = $this->getRequest();

		// saving the family
		if ($request->getParam("saveMember"))
		{
			$values = $request->getPost();
			// removing the save value --> it's only for reference
			unset($values['saveMember']);
			unset($values['picture']);
			// upload the file if there is one and attach it to the rest of the values
			if (isset($_FILES['picture']))
			{
				$fileName = My_File::uploadFile($_FILES['picture']['type'], file_get_contents($_FILES['picture']['tmp_name']), $_FILES['picture']['name']);
				$values['picture'] = $fileName ? $fileName : '';
			}

			//update person and let the user know about it :)
			if (!My_Person::updatePerson($request->getParam('person_id'), $values))
				$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save your information. Please try again.")');
			else
				$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully updated this person.");');

			return $this->_forward('familydetails', 'index', 'families', array('personSaved' => 1));
		}

		
		$this->view->content = $this->view->render('index/addfamily.phtml');
	}
}