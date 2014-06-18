<?php

/**
 * My group abstraction object.
 * @access public
 */
class My_Group
{
	public function __construct() 
	{
		
	}	

	public static function getGroups($filters = array(), $startElem = null, $offset = null, $orderBy = null)
	{
		$parseQuery = new My_Parse_Query('Group');
		foreach ($filters as $key => $value) {
			switch (true)
			{
				case ($key == 'createdAt'):
					$parseQuery->whereGreaterThanOrEqualTo($key, array("__type" => "Date", "iso" => date("c", strtotime($value))));
					break;
				case ($key == 'parish'):
					$parseQuery->wherePointer('parish', 'Parish', $filters['parish']);
					break;
				case ($key == 'autocomplete'):
					$parseQuery->whereRegex('name', '^\Q' . trim($filters['autocomplete']) . '\E', 'i');
					$parseQuery->keys(array('name'));
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
					$parseQuery->where($key, trim($value));
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
			$parseQuery->orderByAscending('name');

		$groups = $parseQuery->find();

		return $groups->request_successfull ? $groups->results : false;
	}

	public static function getGroupsCount($filters = array())
	{
		$parseQuery = new My_Parse_Query('Group');
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
				case ($key == 'autocomplete'):
					$parseQuery->whereRegex('name', '^\Q' . trim($filters['autocomplete']) . '\E', 'i');
					$parseQuery->keys(array('name'));
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
					$parseQuery->where($key, trim($value));
			}
		}

		$groups = $parseQuery->getCount();

		return $groups->request_successfull ? $groups->count : 0;
	}

	public static function getGroupById($id)
	{
		$parseQuery = new My_Parse_Query('Group');
		$parseQuery->where('objectId', $id);
		$group = $parseQuery->find();

		return $group->request_successfull ? $group->results[0] : false;
	}

	public static function addGroup($values)
	{
		$parseObj = new My_Parse_Object('Group');

		// adding the rest of the values
		foreach ($values as $key => $value) {
			switch (true)
			{
				case ($key == 'name'):
					$parseObj->$key = ucfirst(trim($value));
					break;
				case ($key == 'parish'):
					$parseObj->$key = $parseObj->dataType('pointer', array('Parish', $value));
					break;
				default:
					$parseObj->$key = trim($value);
			}
		}
		$add = $parseObj->save();

		return $add->request_successfull ? true : false;
	}

	public static function updateGroup($id, $values)
	{
		$parseObj = new My_Parse_Object('Group');
		foreach ($values as $key => $value)
			switch (true)
			{
				case ($key == 'name'):
					$parseObj->$key = ucfirst(trim($value));
					break;
				default:
					$parseObj->$key = trim($value);
			}
		$update = $parseObj->update($id);
		
		return $update->request_successfull ? true : false;
	}

	public static function deleteGroup($id)
	{
		// deleting the group
		$parseObj = new My_Parse_Object('Group');
		$delete = $parseObj->delete($id);
		if (!$delete)
			return false;

		return true;
	}
}