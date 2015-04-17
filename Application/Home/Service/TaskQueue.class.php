<?php namespace Home\Service;

class TaskQueue extends Queue {

	public function __construct() {
		parent::__construct();
	}

	public function addTask($data) {
		$this->enqueue($data);
	}

	public function removeTask() {
		$this->dequeue();
	}

	public function getNextTask() {
		return $this->getNext();
	}
}
