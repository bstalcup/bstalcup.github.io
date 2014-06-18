<?php

class Families_IndexController extends My_Controller
{
	public $ajaxable = array(
							'viewallfamilies' => array('json'),
							'addfamily' => array('json'),
							'uploaddirectory' => array('json'),
							'editdirectory' => array('json'),
							'calendar' => array('json'),
							'parish' => array('json'),
							'familydetails' => array('json'),
							'school' => array('json'),
							'autocomplete' => array('json'),
							'approvefamilies' => array('json'),
							'familydelete' => array('json'),
							'mergefamilies' => array('json'),
							'deletedirectory' => array('json')
						);

	public function init()
	{
		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function viewallfamiliesAction()
	{
		$request = $this->getRequest();
		// get logged in user parish
		$filters['parish'] = $this->view->user->parish->objectId;
		// get search param if there is one
		if ($request->getParam('search') || !is_null($request->getParam('search'))) 
		{
			$filters['lastName'] = trim($request->getParam('search'));
			$this->view->search = trim($request->getParam('search'));
			$this->view->bridge()->addCallback('$("#searchResults").show(); $("#searchResults p").html("Your directory results for the last name \"'.$filters['lastName'].'\"")');
			$this->view->showContent = 'yes';
		}

		$orderBy = null;
		if ($request->getParam('orderBy'))
			$orderBy = array('by' => $request->getParam('orderBy'), 'type' => ($request->getParam('orderType') == 'Asc' ? 'orderByAscending' : 'orderByDescending') );

		// extract the required families and take care of pagination
		$familiesCount = My_Family::getFamiliesCount($filters);
		if ($familiesCount > 0)
		{
			$offset = My_Core::getConfig()->family->itemsPerPage;
			$pagination = new My_Pagination($familiesCount, $offset, array($offset, 'All'));
			$pagination->setPagesPerSection(7);
			$startElem	= $pagination->getEntryStart();
			$offset = $pagination->getEntryEnd();
			$this->view->families = My_Family::getFamilies($filters, $startElem, $offset, $orderBy);
			$this->view->pagination = $pagination;
		}
		$this->view->familiesCount = $familiesCount;
		$this->view->content = $this->view->render('index/viewallfamilies.phtml');
	}

	public function approvefamiliesAction()
	{
		$request = $this->getRequest();

		if ($request->getParam('thisFamilyId'))
		{
			if (My_Family::updateFamily($request->getParam('thisFamilyId'), array('approved' => true)))
				die(json_encode(array('success' => true)));
			die(json_encode(array('success' => false)));
		}

		// get logged in user parish
		$filters['parish'] = $this->view->user->parish->objectId;

		$orderBy = null;
		if ($request->getParam('orderBy'))
			$orderBy = array('by' => $request->getParam('orderBy'), 'type' => ($request->getParam('orderType') == 'Asc' ? 'orderByAscending' : 'orderByDescending') );
		
		// take the ones that ARE NOT TRUE !!!
		$filters['approved'] = true;

		// get search param if there is one
		if ($request->getParam('search') || !is_null($request->getParam('search'))) 
		{
			$filters['lastName'] = trim($request->getParam('search'));
			$this->view->search = trim($request->getParam('search'));
			$this->view->bridge()->addCallback('$("#searchResults").show(); $("#searchResults p").html("Your directory results for the last name \"'.$filters['lastName'].'\"")');
			$this->view->showContent = 'yes';
		}

		// extract the required families and take care of pagination
		$offset = My_Core::getConfig()->family->itemsPerPage;
		$familiesCount = My_Family::getFamiliesCount($filters);
		if ($familiesCount > 0)
		{
			$pagination = new My_Pagination($familiesCount, $offset, array($offset, 'All'));
			$pagination->setPagesPerSection(7);
			$startElem	= $pagination->getEntryStart();
			$offset = $pagination->getEntryEnd();
			$this->view->families = My_Family::getFamilies($filters, $startElem, $offset, $orderBy);
			$this->view->pagination = $pagination;
		}

		$this->view->familiesCount = $familiesCount;
		$this->view->content = $this->view->render('index/approvefamilies.phtml');
	}

	public function familydetailsAction()
	{
		$request = $this->getRequest();

		// the family is sent from the initial call - so we wont request for it again
		if ($request->getParam('family') && is_array($request->getParam('family')))
			$this->view->family = $request->getParam('family');

		// saving the family
		if ($request->getParam("saveFamily"))
		{
			$values = $request->getPost();

			// removing the save value --> it's only for reference
			unset($values['saveFamily']);
			unset($values['familyPhoto']);

			// upload the file if there is one and attach it to the rest of the values
			if (isset($_FILES['familyPhoto']))
			{
				$fileName = My_File::uploadFile($_FILES['familyPhoto']['type'], file_get_contents($_FILES['familyPhoto']['tmp_name']), $_FILES['familyPhoto']['name']);
				$values['familyPhoto'] = $fileName ? $fileName : '';
			}
			//update family and let the user know about it :)
			if (!My_Family::updateFamily($request->getParam('family_id'), $values))
				$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save your information. Please try again.")');
			else
				$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully updated this family.");');

			$this->view->showContent = 'yes';
		}

		if ($request->getParam("personSaved"))
			$this->view->showContent = 'yes';

		// we must request for the family as we don't have it sent to us
		if (!isset($this->view->family))
			$this->view->family = json_decode(json_encode(My_Family::getFamilyById($request->getParam('family_id'))), true);

		$this->view->persons = My_Person::getPersonsByFamilyId($request->getParam('family_id'));
		$this->view->content = $this->view->render('index/familydetails.phtml');
	}

	public function familydeleteAction()
	{
		$request = $this->getRequest();

		if (!$request->getParam('family_id') || !My_Family::deleteFamily($request->getParam('family_id'), $request->getParam('file_name')))
			return $this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. Something went wrong. Please refresh the page and try again.")');
		
		$this->view->result = array("success" => true);
	}

	public function autocompleteAction()
	{
		$request = $this->getRequest();
		// get logged in user parish
		$filters['parish'] = $this->view->user->parish->objectId;
		// extract the required last names
		$filters['autocomplete'] = $request->getParam('query');
		$families = My_Family::getFamilies($filters, 0, 100);
		$suggestions = array();
		foreach ($families as $key => $family) {
			$suggestions[] = $family->lastName;
		}

		$this->view->suggestions = array_values(array_unique($suggestions));
	}

	public function editdirectoryAction()
	{
		$request = $this->getRequest();
		
		// get logged in user parish
		$filters['parish'] = $this->view->user->parish->objectId;

		// get the total family number
		$familiesCount = My_Family::getFamiliesCount($filters);

		$duplicateFamilies = array();
		if ($familiesCount > 0)
		{
			// get all families addresses
			$filters['keys'] = 'addressLine1';
			$i = 0;$j = 0;
			do 
			{
				$i = $i + 1000;
				if ($familiesCount < $i)
					$j = $familiesCount - ($i-1000);	

				$chunkFamilies[] = My_Family::getFamilies($filters, $i-1000, ($j > 0 ? $j : 1000));
			}
			while ($i < $familiesCount);

			// merge all the 1000 array chunks into one big array
			$allFamilies = array();
			foreach ($chunkFamilies as $value) {
				$allFamilies = array_merge($allFamilies, $value);
			}

			// take only the address from the initial results, to count the duplicates
			foreach ($allFamilies as $family) {
				$checkFamilies[] = $family->addressLine1;
			}

			// get the duplicate addresses
			$duplicates = array_count_values($checkFamilies);
			foreach ($duplicates as $key => $value) {
				if ($value > 1)
					$queryAddresses[] = $key;
			}
			if (isset($queryAddresses) && count($queryAddresses) > 0)
			{
				// add all the duplicate families in the query
				$filters['containedIn'] = array('name' => 'addressLine1', 'values' => $queryAddresses);
				
				// take only the check duplicate fields
				$filters['keys'] = array('accessCode', 'addressCity', 'addressLine1', 'addressLine2', 'addressState', 'addressZip', 'approved', 'email', 'envelopeNumber', 'familyPhoto', 'firstName', 'lastName', 'joinDate', 'joinYear', 'parish', 'phone');

				$duplicateFamiliesArr = My_Family::getFamilies($filters, 0, 1000, 'addressLine1');

				foreach ($queryAddresses as $address) 
				{
					$testCheckFamilies = $sameFamilies = array();
					foreach ($duplicateFamiliesArr as $family)
					{
						if ($family->addressLine1 == $address)
						{
							$sameFamilies[] = json_decode(json_encode($family), true);
							$testCheckFamilies[] = array(
								'firstName' => isset($family->firstName) ? $family->firstName : '', 
								'lastName' => isset($family->lastName) ? $family->lastName : '',  
								'addressLine1' => isset($family->addressLine1) ? $family->addressLine1 : '',  
								'addressCity' => isset($family->addressCity) ? $family->addressCity : '',
								'addressState' => isset($family->addressState) ? $family->addressState : ''
							);
						}
					}

					if (count($testCheckFamilies) == 2)
					{
						if (count(array_diff($testCheckFamilies[0], $testCheckFamilies[1])) == 0)
							$duplicateFamilies[] = $sameFamilies;
					}
					elseif (count($testCheckFamilies) > 2)
					{
						$checkTheDuplicates = true;
						while ($checkTheDuplicates == true)
						{
							$duplicateCount = count($duplicateFamilies);
							foreach ($testCheckFamilies as $key => $family) {
								foreach ($testCheckFamilies as $jkey => $jfamily) {
									if ($key != $jkey && count(array_diff($family, $jfamily)) == 0) {
										$duplicateFamilies[] = array($sameFamilies[$key], $sameFamilies[$jkey]);
										break 2;
									}
								}
							}
							
							if ($duplicateCount < count($duplicateFamilies))
							{
								unset($testCheckFamilies[$key]);
								unset($testCheckFamilies[$jkey]);
							}
							else
								$checkTheDuplicates = false;
						}
					}
				}
			}
		}

		$families = json_decode(json_encode($duplicateFamilies), true);
		$this->view->jsonDuplicates = $families;
		$this->view->duplicateFamilies = $duplicateFamilies;
		$this->view->familiesCount = $familiesCount;
		$this->view->content = $this->view->render('index/editdirectory.phtml');
	}

	public function mergefamiliesAction()
	{
		$request = $this->getRequest();
		if (!$request->getParam('master') || !$request->getParam('slave'))
			return $this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. Something went wrong. Please refresh the page and try again.")');

		if (!My_Family::mergefamilies($request->getParam('master'), $request->getParam('slave')))
			return $this->view->result = array("success" => false);

		$this->view->result = array("success" => true);
	}

	public function addfamilyAction()
	{
		$request = $this->getRequest();

		// saving the family
		if ($request->isPost() && $request->getParam('firstName'))
		{
			$values = $request->getPost();
			$values['parish'] = $this->view->user->parish->objectId;
			//add family and let the user know about it :)
			if (!My_Family::addFamily($values))
				$this->view->bridge()->addCallback('showAlert("alert-danger", "Oops. We were not able to save this family. Please try again.")');
			else
				$this->view->bridge()->addCallback('showAlert("alert-success", "We successfully added this family.");');

			$this->view->showContent = 'yes';
		}

		$this->view->content = $this->view->render('index/addfamily.phtml');
	}

	public function deletedirectoryAction()
	{
		$request = $this->getRequest();

		//deleting the families
		$this->view->result = false;
		if ($request->isPost())
		{
			$result = My_Family::deleteDirectory($this->view->user->parish->objectId);
			if ($result === false)
				return $this->view->bridge()->addCallback('showAlert("alert-danger", "An error has occured while deleting your directory. Please contact your site administrator.")');
			if ($result === 0)
				return $this->view->bridge()->addCallback('showAlert("alert-info", "Your family directory is empty.")');
			$this->view->result = $result;
		}
		
		$this->view->content = $this->view->render('index/deletedirectory.phtml');
	}

	public function uploaddirectoryAction()
	{
		$this->view->content = $this->view->render('index/uploaddirectory.phtml');
	}
}