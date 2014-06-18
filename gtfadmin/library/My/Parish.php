<?php

/**
 * My parish abstraction object.
 * @access public
 */
class My_Parish
{
	public function __construct() 
	{
		
	}	

	public static function getParishById($id)
	{
		$parseQuery = new My_Parse_Query('Parish');
		$parseQuery->where('objectId', $id);
		$parish = $parseQuery->find();

		return $parish->request_successfull ? $parish->results[0] : false;
	}

	public static function getParishes($filters)
	{
		$parseQuery = new My_Parse_Query('Parish');
		foreach ($filters as $key => $value) {
			switch (true)
			{
				case ($key == 'whereInclude'):
					$parseQuery->whereInclude($value);
					break;
				case ($key == 'whereExists'):
					$parseQuery->whereExists($value);
					break;
				default:
					$parseQuery->where($key, $value);
			}
		}
		$parseQuery->orderByAscending('name');
		$parishes = $parseQuery->find();

		return $parishes->request_successfull ? $parishes->results : false;
	}

	public static function addParish($values)
	{
		$parseObj = new My_Parse_Object('Parish');
		foreach ($values as $key => $value) {
			$parseObj->$key = $value;
			if ($key == 'parishCoordinates')
				$parseObj->$key = $parseObj->dataType('geopoint', $value);
		}
		$add = $parseObj->save();

		return $add->request_successfull ? $add : false;
	}

	public static function updateParish($id, $values, $type = null)
	{
		$parseObj = new My_Parse_Object('Parish');
		if ($type == 'contact')
			$parseObj->Personnel = $values;
		else
			foreach ($values as $key => $value) {
				$parseObj->$key = $value;
				if ($key == 'ParishImage')
					$parseObj->$key = $parseObj->dataType('file', array($value));
			}

		$update = $parseObj->update($id);

		return $update->request_successfull ? true : false;
	}
}