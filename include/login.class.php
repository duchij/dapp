<?php 

class login extends main{

	function __construct(){
		parent::__construct();
	}
	
	
	public function js_getSessionID($data)
	{
		return $this->resultStatus(true, session_id());
	}
}

return "login";

?>