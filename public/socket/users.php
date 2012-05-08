<?php

class WebSocketUser implements SplObserver {

	public $socket;
	public $id;
	public $headers = array();
	public $handshake = false;

	public $handlingPartialPacket = false;
	public $partialBuffer = "";

	public $sendingContinuous = false;
	public $partialMessage = "";
	
	public $hasSentClose = false;

	function __construct($id,$socket) {
		$this->id = $id;
		$this->socket = $socket;
	}

	public function update(SplSubject $subject) {
		// i was notified! do something.
		$subject->send($this, "got updated.");
	}
}