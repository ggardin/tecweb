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

	public function getStatus() : int {
		return $this->status;
	}

	public function getMessage() : string {
		return $this->message;
	}

	public function getData() : array {
		return $this->data;
	}
}

?>
