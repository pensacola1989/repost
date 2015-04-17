<?php namespace Home\Service;

class Queue {

	private $queue;

	private $size;

	private $index;

	public function __construct() {
		$this->queue = array();
		$this->size = 0;
		$this->index = 0;
	}

	public function enqueue($data) {
		$this->queue[$this->size++] = $data;
		return $this;
	}

	public function dequeue() {
		if (!$this->isEmpty()) {
			--$this->size;
			$front = array_splice($this->queue, 0, 1);
			return $front[0];
		}
		return FALSE;
	}
	public function getQueue() {
		return $this->queue;
	}

	public function getFront() {
		if (!$this->isEmpty()) {
			return $this->queue[0];
		}
		return FALSE;
	}

	public function getSize() {
		return $this->size;
	}

	public function isEmpty() {
		return 0 === $this->size;
	}

	private function getByIndex() {
		return $this->queue[$this->index];
	}

	public function getNext() {
		if ($this->index + 1 > $this->size) {
			$this->index = 0;
		}
		$i = $this->index;
		$this->index++;
		return $this->queue[$i];
	}

}