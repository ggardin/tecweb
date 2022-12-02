<?php

class Response {
	private $status;
	private $message;
	private $data;

	public function __construct($status = 0, $message = "", $data= array()) {
		$this->status = $status;
		$this->message = $message;
		$this->data = $data;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getMessage() {
		return $this->message;
	}

	public function getData() {
		return $this->data;
	}
}

?>
