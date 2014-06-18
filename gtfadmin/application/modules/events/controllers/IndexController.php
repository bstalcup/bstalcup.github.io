<?php

class Events_IndexController extends My_Controller
{
	public $ajaxable = array(
							'viewevents' => array('json'),
							'editevent' => array('json'),
							'addevent' => array('json'),
							'deleteevent' => array('json')
						);

	public function init()
	{
		parent::init();
		$this->_request->setParam('format', 'json');
		$this->_helper->ajaxContext()->initContext();
	}

	public function vieweventsAction()
	{
		$request = $this->getRequest();
		
		$filters['parish'] = $this->view->user->parish->objectId;
		$startDate = $request->getParam('startDate') ? $request->getParam('startDate') : date('Y-m-01');
		$endDate = $request->getParam('endDate') ? $request->getParam('endDate') : date('Y-m-' . cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')));
		$eventsParse = My_Event::getEvents($filters, $startDate, $endDate);
		foreach ($eventsParse as $key => $eventParse) {
			$events[] = array(
				'label' 		=> $eventParse->label,
				'title' 		=> $eventParse->name,
				'start' 		=> strtotime($eventParse->startDateTime->iso),
				'end' 			=> strtotime($eventParse->endDateTime->iso),
				'description' 	=> $eventParse->description,
				'id'	 		=> $eventParse->objectId,
				'allDay '		=> false
			);
		}
		$this->view->events = $events;

		$this->view->content = $this->view->render('index/viewevents.phtml');
	}

	public function addeventAction()
	{
		$request = $this->getRequest();

		if ($request->isPost())
		{
			$values = $request->getPost();
			$values['parish'] = $this->view->user->parish->objectId;
			$this->view->success = false;
			if (!$objectId = My_Event::addEvent($values))
				$this->view->bridge()->addCallback('resetModal("#myModal"); showAlert("alert-danger", "Oops. We were not able to save your event. Please try again.")');
			else
			{
				$this->view->success = true;
				$this->view->id = $objectId;
				$this->view->bridge()->addCallback('resetModal("#myModal"); showAlert("alert-success", "We successfully added your event.");');
			}
		}
	}

	public function editeventAction()
	{
		$request = $this->getRequest();

		if ($request->isPost())
		{
			$values = $request->getPost();
			$this->view->success = false;
			if (!My_Event::editEvent($request->getParam('event_id'), $values))
				$this->view->bridge()->addCallback('resetModal("#myModal"); showAlert("alert-danger", "Oops. We were not able to save your event. Please try again.")');
			else
			{
				$this->view->success = true;
				$this->view->bridge()->addCallback('resetModal("#edit-event"); showAlert("alert-success", "We successfully updated your event.");');
			}
		}
	}

	public function deleteeventAction()
	{
		$request = $this->getRequest();

		if ($request->isPost())
		{
			$values = $request->getPost();
			$this->view->success = false;
			if (!My_Event::deleteEvent($request->getParam('event_id')))
				$this->view->bridge()->addCallback('resetModal("#myModal"); showAlert("alert-danger", "Oops. We were not able to delete your event. Please try again.")');
			else
			{
				$this->view->success = true;
				$this->view->bridge()->addCallback('resetModal("#edit-event"); showAlert("alert-success", "We successfully deleted your event.");');
			}
		}
	}
}