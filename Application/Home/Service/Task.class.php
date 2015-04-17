<?php namespace Home\Service;

class Task {

	private $taskId;

	public function __construct($taskId) {
		$this->taskId = $taskId;
	}

	public function run() {
		echo 'Task' . $this->taskId . 'is running ';
	}
}
