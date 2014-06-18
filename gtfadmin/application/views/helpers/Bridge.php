<?php

class My_Helper_Bridge extends Zend_View_Helper_Abstract
{
	const emerg    = 0;  // Emergency: system is unusable
	const error    = 1;  // Critical: critical conditions
	const warning  = 2;  // Warning: warning conditions
	const info     = 3;  // Informational: informational messages
	const debug    = 4;  // Debug: debug messages

	protected $_priorities = array();
	protected $_extras = array();

	public $view = null;

	public function setView(Zend_View_Interface $view)
	{
		$r = new ReflectionClass($this);
		$this->_priorities = array_flip($r->getConstants());

		$this->view = $view;
		if (!isset($this->view->interventions))
			$this->view->interventions = array();
		if (!isset($this->view->callings))
			$this->view->callings = array();
	}

	public function bridge()
	{
		return $this;
	}

	public function direct()
	{
		return $this;
	}

	public function redirect($url)
	{
		$this->view->redirect = $url;
		return $this;
	}

	public function refresh($flag = true)
	{
		$this->view->refresh = $flag;
		return $this;
	}

	public function addCallback($callback)
	{
		$this->view->callings[] = $callback;
		return $this;
	}

	public function slideUp($element, $speed, $callback = null)
	{
		$this->addCallback("$('" . $element . "').slideUp('" . $speed . "'" . ($callback === null ? '' : ', function() {' . $callback . '}') . ");");
	}

	public function slideDown($element, $speed, $callback = null)
	{
		$this->addCallback("$('" . $element . "').slideDown('" . $speed . "'" . ($callback === null ? '' : ', function() {' . $callback . '}') . ");");
	}

	public function hide($element, $speed, $callback = null)
	{
		$this->addCallback("$('" . $element . "').hide('" . $speed . "'" . ($callback === null ? '' : ', function() {' . $callback . '}') . ");");
	}

	public function show($element, $speed, $callback = null)
	{
		$this->addCallback("$('" . $element . "').show('" . $speed . "'" . ($callback === null ? '' : ', function() {' . $callback . '}') . ");");
	}

	// adds a message without loging it
	public function addMessage($message)
	{
		$this->view->interventions[] = array_merge(array(
							'timestamp'    => date('Y-m-d H:i:s'),
							'message'      => $message,
							'priority'     => 3,
							'priorityName' => $this->_priorities[3]
						), 
						$this->_extras
		);
		return $this;
	}

	// shows a delayed message without loging it after $hops requests
	public function addDelayedMessage($message, $hops = 2)
	{
		$delayedMessage = new Zend_Session_Namespace('delayedMessage');
		$delayedMessage->setExpirationHops($hops);
		if (!is_array($delayedMessage->messages))
			$delayedMessage->messages = array();
		$delayedMessage->messages = array_merge($delayedMessage->messages, array($message));

		return $this;
	}

	// adds delayed callback after $hops requests
	public function addDelayedCallback($callback, $hops = 2)
	{
		$delayedCallback = new Zend_Session_Namespace('delayedCallback');
		$delayedCallback->setExpirationHops($hops);
		if (!is_array($delayedCallback->callbacks))
			$delayedCallback->callbacks = array();
		$delayedCallback->callbacks = array_merge($delayedCallback->callbacks, array($callback));

		return $this;
	}

	public function getInterventions()
	{
		$delayedMessage = new Zend_Session_Namespace('delayedMessage');
		if (is_array($delayedMessage->messages))
		{
			$messages = $delayedMessage->messages;
			foreach ($messages as $message)
			{
				$this->view->interventions[] = array_merge(array(
									'timestamp'    => date('Y-m-d H:i:s'),
									'message'      => $message,
									'priority'     => 3,
									'priorityName' => $this->_priorities[3]
								), 
								$this->_extras
				);
			}
			unset($delayedMessage->messages);
		}

		return $this->view->interventions;
	}

	public function getCallbacks()
	{
		$delayedCallback = new Zend_Session_Namespace('delayedCallback');
		if (is_array($delayedCallback->callbacks))
		{
			$this->view->callings = array_merge($this->view->callings, $delayedCallback->callbacks);
			unset($delayedCallback->callbacks);
		}
		return $this->view->callings;
	}

	public function log($message, $priority)
	{
		if (is_string($message)) 
		{
			$this->view->interventions[] = array_merge(array(
										'timestamp'    => date('Y-m-d H:i:s'),
										'message'      => $message,
										'priority'     => $priority,
										'priorityName' => $this->_priorities[$priority]
									), 
									$this->_extras
					);
			My_Core::getLogger()->log($message, $priority);
		} else {
			$trace = "\n\r ---DEBUG START===\n\r" . Zend_Debug::dump($message, null, false) . "\n\r ===DEBUG END---\n\r";
			My_Core::getLogger()->debug(html_entity_decode($trace));
		}
		My_Core::getDebugger()->log($message, $priority);
		return $this;
	}

	public function __call($priority, $params)
	{
		if (($priority = array_search($priority, $this->_priorities)) !== false) {
			$this->log(array_shift($params), $priority);
		} else throw new My_Exception($this->view->translate('Bad log priority'));
	}

}