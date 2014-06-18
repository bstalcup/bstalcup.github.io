<?php

/**
 * My event abstraction object.
 * @access public
 */
class My_Event
{
	public function __construct() 
	{
		
	}	

	public static function getEvents($filters = array(), $startDate = null, $endDate = null, $orderBy = null)
	{
		$parseQuery = new My_Parse_Query('Event');
		foreach ($filters as $key => $value) {
			switch (true)
			{
				case ($key == 'createdAt'):
					$parseQuery->whereGreaterThanOrEqualTo($key, array('__type' => 'Date', 'iso' => date('c', strtotime($value))));
					break;
				case ($key == 'parish'):
					$parseQuery->wherePointer('parish', 'Parish', $filters['parish']);
					break;
				case ($key == 'containedIn'):
					$parseQuery->whereContainedIn($value['name'], $value['values']);
					break;
				default:
					$parseQuery->where($key, trim($value));
			}
		}

		if (!is_null($startDate))
			$parseQuery->whereGreaterThanOrEqualTo('startDateTime', array('__type' => 'Date', 'iso' => date('c', strtotime($startDate))));
		if (!is_null($endDate))
			$parseQuery->whereLessThanOrEqualTo('endDateTime', array('__type' => 'Date', 'iso' => date('c', strtotime($endDate))));

		if (!is_null($orderBy))
		{
			if (is_array($orderBy))
				$parseQuery->$orderBy['type']($orderBy['by']);
			else
				$parseQuery->orderByAscending($orderBy);
		}
		else
			$parseQuery->orderByAscending('createdAt');

		$events = $parseQuery->find();

		return $events->request_successfull ? $events->results : false;
	}

	public static function getEventById($id)
	{
		$parseQuery = new My_Parse_Query('Event');
		$parseQuery->where('objectId', $id);
		$event = $parseQuery->find();

		return $event->request_successfull ? $group->results[0] : false;
	}

	public static function addEvent($values)
	{
		$parseObj = new My_Parse_Object('Event');

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
				case ($key == 'endDateTime' || $key == 'startDateTime'):
					$parseObj->$key = $parseObj->dataType('date', $value);
					break;
				default:
					$parseObj->$key = trim($value);
			}
		}

		$add = $parseObj->save();

		return $add->request_successfull ? $add->objectId : false;
	}

	public static function editEvent($id, $values)
	{
		$parseObj = new My_Parse_Object('Event');
		foreach ($values as $key => $value)
			switch (true)
			{
				case ($key == 'name'):
					$parseObj->$key = ucfirst(trim($value));
					break;
				case ($key == 'endDateTime' || $key == 'startDateTime'):
					$parseObj->$key = $parseObj->dataType('date', $value);
					break;
				default:
					$parseObj->$key = trim($value);
			}
		$update = $parseObj->update($id);
		
		return $update->request_successfull ? true : false;
	}

	public static function deleteEvent($id)
	{
		// deleting the group
		$parseObj = new My_Parse_Object('Event');
		$delete = $parseObj->delete($id);
		if (!$delete)
			return false;

		return true;
	}
}