<?php

/**
 * My person abstraction object.
 * @access public
 */
class My_Person
{
	public function __construct() 
	{
		
	}	

	public static function getPersonsByFamilyId($family_id)
	{
		$parseQuery = new My_Parse_Query('Person');
		$parseQuery->wherePointer('family', 'Family', $family_id);
		$parseQuery->orderByAscending('lastName');
		$persons = $parseQuery->find();

		return $persons->request_successfull ? $persons->results : false;
	}

	public static function getPersonById($id)
	{
		$parseQuery = new My_Parse_Query('Person');
		$parseQuery->where('objectId', $id);
		$person = $parseQuery->find();

		return $person->request_successfull ? $person->results[0] : false;
	}

	public static function addPerson($values)
	{
		$parseObj = new My_Parse_Object('Person');
		foreach ($values as $key => $value) {
			$parseObj->$key = $value;
			if ($key == 'birthday')
				$parseObj->$key = $parseObj->dataType('date', $value);
			if ($key == 'picture')
				$parseObj->$key = $parseObj->dataType('file', array($value));
			if ($key == 'family')
				$parseObj->$key = $parseObj->dataType('pointer', array('Family', $value));
			if ($key == 'parish')
				$parseObj->$key = $parseObj->dataType('pointer', array('Parish', $value));
		}
		// setting the rest of the person values
		$parseObj->acceptedAgreements = true;
		$add = $parseObj->save();

		return $add->request_successfull ? $add->objectId : false;
	}

	public static function updatePerson($id, $values)
	{
		$parseObj = new My_Parse_Object('Person');
		foreach ($values as $key => $value) {
			$parseObj->$key = $value;
			if ($key == 'birthday')
				$parseObj->$key = $parseObj->dataType('date', $value);
			if ($key == 'picture')
				$parseObj->$key = $parseObj->dataType('file', array($value));
		}
		$update = $parseObj->update($id);

		return $update->request_successfull ? true : false;
	}

	public static function removePersonsFromFamily($family_id, $master_family_id = null)
	{
		$persons = self::getPersonsByFamilyId($family_id);

		if (count($persons) == 0)
			return true;

		foreach ($persons as $key => $person) 
		{
			$familyValue = null;
			if (!is_null($master_family_id))
			{
				$familyValue =  array(
					"__type" => "Pointer",
					"className" => 'Family',
					"objectId" => $master_family_id
				);
			}
			$requests[] = array(
				'method' => 'PUT',
				'path' => '/1/classes/Person/' . $person->objectId,
				'body' => array(
					'family' => $familyValue
				)
			);
		}
		$parseObj = new My_Parse_Object('Person');
		$parseObj->requests = $requests;
		$batch = $parseObj->batch();

		return $batch ? true : false;
	}

	public static function deletePerson($id, $user_id = null, $fileName)
	{
		// delete person
		$parseObj = new My_Parse_Object('Person');
		$delete = $parseObj->delete($id);
		if (!$delete)
			return false;

		// delete the photo
		$fileName != "" ? My_File::deleteFile($fileName) : '';
		
		// update user --> set person column to null
		if (!is_null($user_id) && $user_id != '')
			if (!My_User::setPersonToNull($user_id))
				return false;

		return true;
	}	
}