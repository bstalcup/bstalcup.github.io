<?php

/**
 * My family abstraction object.
 * @access public
 */
class My_Family
{
	public function __construct() 
	{
		
	}	

	public static function getFamilies($filters = array(), $startElem = null, $offset = null, $orderBy = null)
	{
		$parseQuery = new My_Parse_Query('Family');
		foreach ($filters as $key => $value) {
			switch (true)
			{
				case ($key == 'createdAt'):
					$parseQuery->whereGreaterThanOrEqualTo($key, array("__type" => "Date", "iso" => date("c", strtotime($value))));
					break;
				case ($key == 'parish'):
					$parseQuery->wherePointer('parish', 'Parish', $filters['parish']);
					break;
				case ($key == 'approved'):
					$parseQuery->whereNotEqualTo($key, $value);
					break;
				case ($key == 'autocomplete'):
					$parseQuery->whereRegex('lastName', '^\Q' . $filters['autocomplete'] . '\E', 'i');
					$parseQuery->keys(array('lastName'));
					break;
				case ($key == 'keys'):
					if (is_array($value))
						$parseQuery->keys($value);
					else
						$parseQuery->keys(array($value));
					break;
				case ($key == 'containedIn'):
					$parseQuery->whereContainedIn($value['name'], $value['values']);
					break;
				default:
					$parseQuery->where($key, $value);
			}
		}

		if (!is_null($startElem))
			$parseQuery->setSkip($startElem);
		if (!is_null($offset))
			$parseQuery->setLimit($offset);

		if (!is_null($orderBy))
		{
			if (is_array($orderBy))
				$parseQuery->$orderBy['type']($orderBy['by']);
			else
				$parseQuery->orderByAscending($orderBy);
		}
		else
			$parseQuery->orderByAscending('lastName');

		$families = $parseQuery->find();

		return $families->request_successfull ? $families->results : false;
	}

	public static function getFamiliesCount($filters = array())
	{
		$parseQuery = new My_Parse_Query('Family');
		foreach ($filters as $key => $value) {
			switch (true)
			{
				case ($key == 'createdAt'):
					$parseQuery->whereGreaterThanOrEqualTo($key, array("__type" => "Date", "iso" => date("c", strtotime($value))));
					break;
				case ($key == 'whereContainedIn'):
					$parseQuery->whereContainedIn($value[0], $value[1]);
					break;
				case ($key == 'parish'):
					$parseQuery->wherePointer('parish', 'Parish', $filters['parish']);
					break;
				case ($key == 'approved'):
					$parseQuery->whereNotEqualTo($key, $value);
					break;
				case ($key == 'autocomplete'):
					$parseQuery->whereRegex('lastName', '^\Q' . $filters['autocomplete'] . '\E', 'i');
					$parseQuery->keys(array('lastName'));
					break;
				case ($key == 'keys'):
					if (is_array($value))
						$parseQuery->keys($value);
					else
						$parseQuery->keys(array($value));
					break;
				case ($key == 'containedIn'):
					$parseQuery->whereContainedIn($value['name'], $value['values']);
					break;
				default:
					$parseQuery->where($key, $value);
			}
		}
		$families = $parseQuery->getCount();

		return $families->request_successfull ? $families->count : 0;
	}

	public static function getFamilyById($id)
	{
		$parseQuery = new My_Parse_Query('Family');
		$parseQuery->where('objectId', $id);
		$family = $parseQuery->find();

		return $family->request_successfull ? $family->results[0] : false;
	}

	public static function addFamily($values)
	{
		$parseObj = new My_Parse_Object('Family');
		// approving the family by default - an admin is adding it so it should be approved
		$parseObj->approved = true;
		// adding the rest of the values
		foreach ($values as $key => $value) {
			$parseObj->$key = $value;
			if ($key == 'firstName' || $key == 'lastName')
				$parseObj->$key = ucfirst($value);
			if ($key == 'parish')
				$parseObj->$key = $parseObj->dataType('pointer', array('Parish', $value));
		}
		$add = $parseObj->save();

		return $add->request_successfull ? true : false;
	}

	public static function updateFamily($id, $values)
	{
		$parseObj = new My_Parse_Object('Family');
		foreach ($values as $key => $value)
			switch (true)
			{
				case ($key == 'objectId'):
					// do nothing if we get the objectId
					break;
				case ($key == 'familyPhoto'):
					$parseObj->$key = $parseObj->dataType('file', array($value));
					break;
				case ($key == 'approved'):
					$val = ($value == 'true' ? true : false);
					$parseObj->$key = $val;
					break;
				case ($key == 'firstName' || $key == 'lastName'):
					$parseObj->$key = ucfirst($value);
					break;
				default:
					$parseObj->$key = $value;
			}
		$update = $parseObj->update($id);
		
		return $update->request_successfull ? true : false;
	}

	public static function deleteFamily($id, $fileName)
	{
		// removing members from family
		My_Person::removePersonsFromFamily($id);

		// delete the photo
		$fileName != "" ? My_File::deleteFile($fileName) : '';

		// deleting the family
		$parseObj = new My_Parse_Object('Family');
		$delete = $parseObj->delete($id);
		if (!$delete)
			return false;

		return true;
	}

	public static function mergefamilies($masterParam, $slaveParam)
	{
		//updating the array to normal state
		foreach ($masterParam as $key => $value) {
			$toUse = explode('|||', $value);
			$master[$toUse[0]] = $toUse[1];
		}
		// making this sepparately because the slave could have fields missing in the master (parse.com does not return all even if you ask for them ... :( )
		foreach ($slaveParam as $key => $value) {
			$toUse = explode('|||', $value);
			$slave[$toUse[0]] = $toUse[1];
		}
		// merging the arrays
		foreach ($slave as $key => $value) {
			if ($value != '' && (!isset($master[$key]) || $master[$key] == ''))
				$master[$key] = $value;
		}
		
		// updating the master with the slave values;
		if (!self::updateFamily($master['objectId'], $master))
			return false;
		
		// removing members from family and adding them to the master family
		if (!My_Person::removePersonsFromFamily($slave['objectId'], $master['objectId']))
			return false;

		// delete the slave photo
		if (isset($slave['familyPhoto']) && (isset($master['familyPhoto']) && $master['familyPhoto'] != $slave['familyPhoto']))
			My_File::deleteFile($slave['familyPhoto']);

		// deleting the family
		$parseObj = new My_Parse_Object('Family');
		$delete = $parseObj->delete($slave['objectId']);
		if (!$delete)
			return false;

		return true;
	}

	public static function deleteDirectory($parish_id)
	{
		// saving them in the session -> this is done multiple times becaus of the progress bar
		// so we don't to query again for them each time
		$familiesSession = new Zend_Session_Namespace('families');
		if (!isset($familiesSession->families))
		{
			$families = self::getFamilies(array('keys' => 'addressState', 'parish' => $parish_id));
			$familiesSession->families = $families;
			$familiesSession->chunk = 0;
		}

		if (is_array($familiesSession->families) && count($familiesSession->families) > 0)
		{
			$requests = array();
			$familiesChunks = array_chunk($familiesSession->families, 20);
			foreach ($familiesChunks[$familiesSession->chunk] as $family)
			{
				$requests[] = array(
					'method' => 'DELETE',
					'path' => '/1/classes/Family/' . $family->objectId
				);
			}
			$parseObj = new My_Parse_Object('Family');
			$parseObj->requests = $requests;
			$batch = $parseObj->batch();

			$familiesSession->chunk++;
			if ($familiesSession->chunk == ceil(count($familiesSession->families) / 20))
			{
				$familiesSession->chunk = $familiesSession->families = null;
				return "Done";
			}
		}
		else
			return 0;

		return $batch ? true : false;
	}
}