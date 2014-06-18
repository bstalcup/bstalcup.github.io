<?php

/**
 * My school abstraction object.
 * @access public
 */
class My_School
{
	public function __construct() 
	{
		
	}	

	public static function getSchoolByParishId($id)
	{
		$parseQuery = new My_Parse_Query('School');
		$parseQuery->wherePointer('parish', 'Parish', $id);
		$school = $parseQuery->find();

		return $school->request_successfull ? $school->results : false;
	}

	public static function addSchool($values)
	{
		$parseObj = new My_Parse_Object('School');
		foreach ($values as $key => $value) {
			$parseObj->$key = $value;
			if ($key == 'parish')
				$parseObj->$key = $parseObj->dataType('pointer', array('Parish', $value));
		}
		$add = $parseObj->save();

		return $add->request_successfull ? $add : false;
	}

	public static function updateSchool($id, $values)
	{
		$parseObj = new My_Parse_Object('School');
		foreach ($values as $key => $value) {
			$parseObj->$key = $value;
		}

		$update = $parseObj->update($id);

		return $update->request_successfull ? true : false;
	}
}