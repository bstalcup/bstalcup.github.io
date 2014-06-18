<?php

class Settings_IndexController extends My_Controller
{
	public $ajaxable = array(
							'parish' => array('json'),
							'school' => array('json')
						);

	public function init()
	{
		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function parishAction()
	{
		$request = $this->getRequest();

		if ($request->isPost() && ($request->getParam('saveParish') || $request->getParam('saveContact')))
		{
			$values = $request->getPost();
			unset($values['saveParish']);
			unset($values['saveContact']);
			unset($values['ParishImage']);

			// upload the file if there is one and attach it to the rest of the values
			if (isset($_FILES['ParishImage']))
			{
				$fileName = My_File::uploadFile($_FILES['ParishImage']['type'], file_get_contents($_FILES['ParishImage']['tmp_name']), $_FILES['ParishImage']['name']);
				$values['ParishImage'] = $fileName ? $fileName : '';
			}
			
			// if we have contacts --> add them to the array
			if ($request->getParam('saveContact'))
			{
				foreach ($values as $key => $value) {
					if ($value and $value != '')
						$vals[] = array('name' => $key, 'value' => $value);
				}
				$values = $vals;
			}

			if (!My_Parish::updateParish($this->view->user->parish->objectId, $values, ($request->getParam('saveContact') ? 'contact' : null)))
				$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save this parish. Please try again.")');
			else
				$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully updated this parish.");');
			
			$this->view->showContent = 'yes';
		}

		$this->view->parish = My_Parish::getParishById($this->view->user->parish->objectId);
		if (isset($this->view->parish->Personnel))
		{
			$personnel = array();
			foreach ($this->view->parish->Personnel as $key => $value) {
				$personnel[$value->name] = $value->value;
			}
			$this->view->personnel = $personnel;
		}
		$this->view->content = $this->view->render('index/parish.phtml');
	}
	
	public function schoolAction()
	{
		$request = $this->getRequest();

		if ($request->isPost())
		{
			if ($request->getParam('name') && trim($request->getParam('name')) != '')
			{
				$values = $request->getPost();
				$result = true;
				if ($request->getParam('objectId') && trim($request->getParam('objectId')) != '')
				{
					// update the shcool
					if (!My_School::updateSchool($request->getParam('objectId'), $values))
						$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save this school. Please try again.")');
					else
						$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully updated this school.");');
				} 
				else 
				{
					//add the scool
					$values['parish'] = $this->view->user->parish->objectId;
					if (!$schoolData = My_School::addSchool($values))
						$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save this school. Please try again.")');
					else
					{
						$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully added this school.");');
						$values['objectId'] = $schoolData->objectId;
					}
				}
				$school = array();
				foreach ($values as $key => $value)
					$school[$key] = trim($value);
				$this->view->school = $school;
			}
			$this->view->showContent = 'yes';		
		}

		if (!isset($this->view->school))
		{
			$school = My_School::getSchoolByParishId($this->view->user->parish->objectId);
			$this->view->school = $school != false && count($school) > 0 ? json_decode(json_encode($school[0]), true) : array();
		}

		$this->view->content = $this->view->render('index/school.phtml');
	}	
}