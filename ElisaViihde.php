<?php

class ElisaViihde {
	
	private $baseUrl = 'http://elisaviihde.fi/etvrecorder';
	private $sessionId = null;
	
	public function __construct($username = null, $password = null) {
		if(!is_null($username) && !is_null($password)) {
			$this->login($username, $password);
		}
	}
	
	public function login($username, $password) {
		$data = $this->request('default', ['username' => $username, 'password' => $password]);
		$success = $data->body === true;
		if($success) {
			preg_match('~JSESSIONID=(.*?);~', $data->header, $match);
			if(isset($match[1])) {
				$this->sessionId = $match[1];
			}
		}
		return $success;		
	}
	
	public function logout() {
		$this->request('logout');
		$this->sessionId = null;
	}
	
	public function getChannels() {
		$_channels = $this->request('ajaxprograminfo', ['channels'])->body->channels;
		$channels = [];
		foreach($_channels as $channel) {
			$channels[key($channel)] = reset($channel);
		}
		return $channels;
	}
	
	public function getPrograms($channel) {
		return $this->request('ajaxprograminfo', ['24h' => $channel])->body->programs;
	}
	
	public function getProgram($programId) {
		return $this->request('program', ['programid' => $programId])->body;
	}
	
	public function getReadyList($folderId = null)
	{
		$data = [];
		if(!empty($folderId)) {
			$data['folderid'] = $folderId;
			$data['ppos'] = 0;
		}
		return $this->request('ready.sl', $data)->body->ready_data[0];
	}
	
	public function getRecordingList() {
		return $this->request('recordings')->body->recordings;
	}
	
	public function getWildcardList() {
		return $this->request('wildcards')->body->wildcardrecordings;
	}
	
	public function getTopList() {
		return $this->request('channels');
	}
	
	public function addRecording($programId) {
		$this->request('program', ['programid' => $programId, 'record' => $programId]);
	}
	
	public function removeRecording($programId) {
		$this->request('program', ['remover' => $programId]);
	}
	
	public function addWildcard($channel, $wildcard, $folderId) {
		$this->request('wildcards', ['channel' => $channel, 'folderId' => $folderId, 'wildcard' => $wildcard, 'record' => 'true']);
	}
	
	public function removeWildcard($wildcardId) {
		$this->request('wildcards', ['remover' => $wildcardId]);
	}
	
	public function removeReady($programViewId) {
		$this->request('wildcards', ['remove' => 'true', 'removep' => $programViewId]);
	}
	
	private function request($endpoint, $data = []) {
		$data['ajax'] = 'true';
		$url = $this->baseUrl.'/'.$endpoint.'.sl?'.http_build_query($data);
		$c = curl_init($url);
		if(!empty($this->sessionId)) {
			curl_setopt($c, CURLOPT_COOKIE, 'JSESSIONID='.$this->sessionId);
		}
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_HEADER, true);
		list($header, $body) = explode("\r\n\r\n", curl_exec($c));
		return (object)['header' => $header, 'body' => json_decode($body)];
	}
}
