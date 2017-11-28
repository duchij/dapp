<?php
if(!isset($_SESSION['_sf2_attributes'])) {

	echo "No valid member or not logged user!!!!<br><br> Please login as valid member into members area of
	<a href='http://espes.eu' target='_self'>espes.eu</a>";
	//var_dump($_SESSION['_sf2_attributes']);
	exit;
}

if (!isset($_SESSION['_sf2_attributes']["espes_valid_member"]))
{
	echo "No valid Espes member... Please contact espes.eu webmaster";
	//var_dump($_SESION['_sf2_attributes']["espes_valid_member"]);
	exit;
}
	



class poll extends main {
	
	
	/**
	 * @var string Account type
	 */
	var $account;
	

    function __construct(){
        
    	parent::__construct("poll");
    	
    	//$this->pollList();
    	$this->loadUserData();
    	$this->check_status();
    	
    	$this->account = $_SESSION["_sf2_attributes"]["election2017data"]["account_type"];
    	//var_dump($_SESSION["_sf2_attributes"]["election2017data"]);
        
    }
    
    public function init($data)
    {
    	$this->pollList($data);
    }
    
    
    protected function loadUserData()
    {
    	//print_r($_SESSION['_sf2_attributes']["espes_valid_member"]);
    	
    	$memberId = intval($_SESSION['_sf2_attributes']["espes_valid_member"]["membership_id"]);
    	
    	/*$res = preg_match("/M-\d{4}/", $memberId);
    	
    	if ($res == 0 || $res == FALSE){
    		$this->tplOutError("", "No valid member id exiting....Please contact system administrator of espes.eu webpage");
    		exit;
    	}*/
    	
    	if ($memberId == 0){
    		$this->tplOutError("", "No valid member id exiting....Please contact system administrator of espes.eu webpage");
    		exit;
    	}
    	
		
    	$sql = sprintf("SELECT [user_hash],[type],[name],[surname],[espes_num] from [users] WHERE [membership_id]=%d",$memberId);
    	
    	$sql = $this->db->buildSql($sql);
    	
    	
    	$row = $this->db->row($sql);
    	
    	
    	
    	if (count($row) == 0){
    		$this->tplOutError("", "No member with {$memberId} found....");
    		exit;
    	}
    	
    	if ($row !== FALSE){
    		
    		$_SESSION["_sf2_attributes"]["election2017data"] = array(
    					"user_hash" 		=> $row["user_hash"],
    					"account_type"	 	=> $row["type"],
    					"name"				=> $row["name"],
    					"surname"			=> $row["surname"],
    					"espesId"			=> $row["espes_num"],
    				
    				
    		);
    		
    	}
    	else{
    		//var_dump($row);
    		
    		$this->tplOutError("", "Error getting user data....");
    		exit;
    		
    	}
    	
    	
    }

    public function create($data)
    {
    	if ($this->account == "superadmin"){
    		$this->tplOutput("poll/pollcreate.tpl");
    	}else{
    		$this->tplOutError("", "You do not have permission to create polls!!!");
    	}
    	
        
    }
    
    public function js_disableVoteById($data)
    {
    	$data["voteId"] = intval($data["voteId"]);
	
    	$sql = "UPDATE [polls] SET [poll_active] = 0, [v_status]='closed' WHERE [poll_id]={voteId|i}";
    	
    	$sql = $this->db->buildSql($sql,$data);
    	
    	//$this->log->logData($sql,false);
    	
    	$res = $this->db->execute($sql);
    	
    	return $this->resultStatus($res["status"], $res);
    	
    }
    
    public function pollList($data=array())
    {
    	if (!isset($data["a"])){
    		$data["a"]=1;
    	}
    	if (strpos($this->account,"admin")!==FALSE){
			$sql1="    		
    		SELECT
    		 
    		DATETIME('now') AS [today],
    		[t_polls].*,
    		[t_pdata.status] AS [poll_status],
    		[t_pdata.ipadre] AS [ip_adres],
    		[t_pdata.date] AS [vote_date],
    		[t_pdata.answer] AS [poll_answer],
    		[t_pdata.user_hash] AS [user_hash]
    		FROM [polls]  AS [t_polls]
    		LEFT JOIN [poll_data] AS [t_pdata] ON [t_pdata.poll_id] = [t_polls.poll_id]
    		WHERE
    			[t_polls.poll_active] = 1
    		-- AND ([t_polls.poll_start_date] >= DATE('now') OR [t_polls.poll_start_date] <= DATE('now'))
    		-- AND [today] BETWEEN [t_polls.poll_start_date] AND [t_polls.poll_end_date]
    		
    		ORDER BY [t_polls.poll_start_date], [t_polls.poll_order] ASC
    		";
			
			$sql = $this->db->buildSql($sql1,$data);
    	}
    	else{
    		$sql1 = "
    			SELECT
    
    				DATETIME('now') AS [today],
    				[t_polls].*,
    				[t_pdata.status] AS [poll_status],
    				[t_pdata.ipadre] AS [ip_adres],
    				[t_pdata.date] AS [vote_date],
    				[t_pdata.answer] AS [poll_answer],
    				[t_pdata.user_hash] AS [user_hash]
    			FROM [polls]  AS [t_polls]
					LEFT JOIN [poll_data] AS [t_pdata] ON [t_pdata.poll_id] = [t_polls.poll_id]
				WHERE
    			 [t_polls.poll_active] = 1
    		
    				-- AND ([t_polls.poll_start_date] >= DATE('now') OR [t_polls.poll_start_date] <= DATE('now'))
    				AND [today] BETWEEN [t_polls.poll_start_date] AND [t_polls.poll_end_date]
    		
    			ORDER BY [t_polls.poll_start_date], [t_polls.poll_order] ASC
    			";
    		
    		$sql = $this->db->buildSql($sql1,$data);
    	}
    	
    	$this->log->logData($sql,false);
    	
    	$res = $this->db->table($sql);
    	
    	//var_dump($res);
    	
    	if ($res["status"] === FALSE)
    	{
    		$this->tplOutError("", "Error: ".$res["result"]);
    	}
    	$result = array();
    	$polls = $res["table"];
    	
    	$user_hash = $_SESSION["_sf2_attributes"]["election2017data"]["user_hash"];
    		
    	foreach ($polls as &$poll){
    		
    		
    		if ($this->is_base64($poll["poll_description"])){
    			$poll["poll_description"] = base64_decode($poll["poll_description"]);
    			$poll["poll_description"] = urldecode($poll["poll_description"]);
    		}
    		
    		$poll["poll_stats"] = explode("|",$poll["poll_stats"]);
    		
    		if ($poll["answer_count"] > 1){
    			
    			//if (strpos($poll["answer"],"|") !== FALSE){
    				
    				$answrArr = explode("|",$poll["poll_answer"]);
    				
    				$voting = count($answrArr);
    				//echo "v:".$voting;
    				if ($poll["user_hash"] == $user_hash){
    					$tmp = $this->removeElected($poll["poll_stats"], $answrArr);
    					$poll["poll_stats"] = $tmp;
    				}
    				$poll["poll_m_answers"] = $answrArr;
    		}    		
    		
    		$poll["poll_start_date"] = date("d.m.Y H:i",strtotime($poll["poll_start_date"]));
    		$poll["poll_end_date"] = date("d.m.Y H:i",strtotime($poll["poll_end_date"]));
    		$poll["vote_date"] = date("d.m.Y H:i",strtotime($poll["vote_date"]));
    		
    		
    		if (isset($result[$poll["poll_hash"]])){
    			
    			if ($result[$poll["poll_hash"]]["user_hash"] != $_SESSION["_sf2_attributes"]["election2017data"]["user_hash"]){
    				$result[$poll["poll_hash"]] = $poll;
    			}
    			
    		}else{
    			$result[$poll["poll_hash"]] = $poll;
    		}
    		
    	}
    	
    	//var_dump($result);
    	//return;
    	if (count($result) == 0){
    		$this->smarty->assign("noPolls","No active polls present...!!!");
    	}else{
    		$this->smarty->assign("polls",$result);
    	}
    	
    	
    	$this->tplOutput("poll/showpolls.tpl");
    	
    }
    
    protected function removeElected($answers, $answered)
    {
    	foreach ($answered as $key=>$value){
    		
    		$index = array_search($value,$answers);
    		
    		
    		if ($index !== FALSE){
    			
    			unset($answers[$index]);
    		}
    		
    	}
    	$tmp = array_values($answers);
    	
    	//var_dump($tmp);
    	return $tmp;
    	
    }

    public function js_userVotePoll($data)
    {
    	
    	
    	
    	$pollSave = array();
    	$parameter = "";
    	
    	if (array_key_exists("answer_count", $data)){
    		
    		$aCount = intval($data["answer_count"]);
    		
    		if ($aCount == 0){
    			return $this->resultStatus(false, "Data not saved illegal answer count");
    		}
    		
    		if ($aCount > 1) {
    			
    			
    			if (!empty($data["answers"]))
    			{
    				$arrTmp = explode("|",$data["answers"]);
    			}
    			else{
    				$arrTmp = array();
    			}
    			
    			$status = "voting";
    			
    			$pollAnswStr = "";
				
				array_push($arrTmp,$data["poll_answer"]);
				
				$cnt = count($arrTmp);
				
				if ($cnt == $aCount){
					$status = "voted";
				
				}
				
				$pollAnswStr=implode("|",$arrTmp);
    			
    			$pollSave = array(
    					"poll_id" => intval($data["poll_id"]),
    					"user_hash" => $_SESSION["_sf2_attributes"]["election2017data"]["user_hash"],
    					"date" => date("Y-m-d H:i"),
    					"answer" => $pollAnswStr,
    					"status" => $status,
    					"ipadre" => $this->getIp()
    			);
    			
    			$parameter ="REPLACE";
    			
    		}else{
    		
	    		$pollSave = array(
	    				"poll_id" => intval($data["poll_id"]),
	    				"user_hash" => $_SESSION["_sf2_attributes"]["election2017data"]["user_hash"],
	    				"date" => date("Y-m-d H:i"),
	    				"answer" => $data["poll_answer"],
	    				"status" => "voted",
	    				"ipadre" => $this->getIp()
	    		);
    		}
    		
    	}else{
    		
    		$pollSave = array(
    				"poll_id" => intval($data["poll_id"]),
    				"user_hash" => $_SESSION["_sf2_attributes"]["election2017data"]["user_hash"],
    				"date" => date("Y-m-d H:i"),
    				"answer" => $data["poll_answer"],
    				"status" => "voted",
    				"ipadre" => $this->getIp()
    		);
    		
    	}
    		
    	
    	
    	//var_dump($pollSave);
    	
    	$res = $this->db->insert_row("poll_data", $pollSave,true,$parameter);
    	
    	$this->log->logData($pollSave,false,"User vote data");
    	$this->log->logData($res,false,"Result of user vote");
    	
    	
    	return $this->resultStatus($res["status"],$res);
    	
    }

    public function js_saveData($data)
    {
		//var_dump($data);
		//return;
    	$startDate = strtotime($data["poll_start_date"]);
    	$endDate = strtotime($data["poll_end_date"]);
    	
    	if ($endDate < $startDate){
    		return $this->resultStatus(false, "End Date cannot be smaller then start date !!!!!");
    	}
    	
    	if (empty($data["poll_hash"])){
        	$data["poll_hash"] = md5($data["poll_title"].$data["poll_start_date"].$data["poll_end_date"]);
    	}
        
        $data["poll_stats"] = join("|",$data["poll_answers"]);

        unset($data["poll_answers"]);

        $res = $this->db->insert_row("polls", $data, false, "REPLACE");

        return array("status"=>$res["status"],"result"=>$res);
    }
    
    public function editPoll($data)
    {
    	$data["id"] = intval($data["id"]);
    	$sql = "SELECT * FROM [polls] WHERE [poll_id]={id|i}";
    	$sql = $this->db->buildSql($sql,$data);
    	
    	$res = $this->db->row($sql);
    	
    	if ($res !== FALSE){
    		
    		
    		$res["poll_stats"] = explode("|",$res["poll_stats"]);
    		
    		if ($this->is_base64($res["poll_description"])){
    			
    			$res["poll_description"] = base64_decode($res["poll_description"]);
    			$res["poll_description"] = urldecode($res["poll_description"]);
    			
    		}
    		
    		$this->smarty->assign("pollData",$res);
    		$this->tplOutput("poll/pollcreate.tpl",$res);
    	}else{
    		$this->tplOutError("", "No such poll");
    		return false;
    	}
    }
    
    public function pastPollList($data){
    	
    }
    
    public function getResults($data)
    {
    	if (strpos($this->account, "admin") === FALSE){
    		$this->tplOutError("", "You do not have permission to check results....");
    		return;
    	}
    	
    	
    	$dt = array("pollId"=>intval($data["id"]));
    	
    	
    	$sql = "SELECT 
    					[t_data.poll_id],[t_data.answer], COUNT([t_data.answer]) AS [count],
    					[t_poll.poll_title] AS [title], [t_poll.poll_description] AS [description],
    					[t_poll.answer_count] AS [answer_count],
    					[t_poll.poll_stats] AS [stats]
    				FROM [poll_data] AS [t_data]
    			
    			INNER JOIN [polls] AS [t_poll] ON [t_poll.poll_id] = [t_data.poll_id]
    			
    			WHERE [t_data.poll_id] = {pollId|i}
    				GROUP BY [t_data.answer],[t_data.poll_id]
    			";
    	$sql = $this->db->buildSql($sql,$dt);
    	$this->log->logData($sql,false,"duch");
    	
    	 
    	$res = $this->db->table($sql);
    	
    	
    	if(!$res["status"]){
    		
    		$this->tplOutError("", $res["result"]);
    		return false;
    		
    	}
    	
    	$totalCount = 0;
    	$result = array();
    	
    	$multiChoice = array();
    	
    	if (count($res["table"])==0){
    		$this->smarty->assign("noData","No vote results yet...");
    		$this->tplOutput("poll/results.tpl");
    		return;
    	}
    	
    	foreach ($res["table"] as $row){
    		
    		if ($row["answer_count"] > 1){
    			
    			if (empty($multiChoice)){
    				
    				$ansArr = explode("|",$row["stats"]);
    				
    				foreach ($ansArr as $answer){
    					$multiChoice[$answer] = 0;
    				}
    			}
    			
    			$arrTmp = explode("|",$row["answer"]);
    			
    			//var_dump($arrTmp);
    			$cntLn = count($arrTmp);
    			
    			for ($a=0; $a<$cntLn; $a++){
    				$multiChoice[$arrTmp[$a]] += 1;
    			}
    			
    		}
    		else{
    			
    			$totalCount += intval($row["count"]);
    			$result["data"][] =array(
    									"answer" =>$row["answer"],
    									"count"=>$row["count"]
    									
    			);
    		
    		}
    		
    	}
    	
    	
    	if (!empty($multiChoice)){
    		$result["multichoice"] = $multiChoice;
    		
    		$tmp = 0;
    		foreach ($multiChoice as $choice){
    			$tmp += $choice;
    		}
    		
    		$result["totalCount"] = $tmp;
    		$result["allVotes"] = count($res["table"]);
    		
    	}else{
    		$result["totalCount"] = $totalCount;
    	}
    	
    	$result["title"] = $res["table"][0]["title"];
    	$result["description"] = $res["table"][0]["description"];
    	
    	if ($this->is_base64($result["description"])){
    		 
    		$result["description"] = base64_decode($result["description"]);
    		$result["description"] = urldecode($result["description"]);
    		 
    	}
    	
    	
    	
    	$result["answer_count"] = $res["table"][0]["answer_count"];
    	

    	if (isset($data["d"]) && $data["d"]==1){
    		$this->smarty->assign("resStatus","Definitive");
    		$this->smarty->assign("print","1");
    	}else{
    		$this->smarty->assign("resStatus","Preliminary");
    		$this->smarty->assign("print","0");
    	}
    	
    	$this->smarty->assign("resData",$result);
    	$this->tplOutput("poll/results.tpl");
    	
    }
    
    protected function getIp()
    {
    	//return $ip = $_SERVER['HTTP_CLIENT_IP']?$_SERVER['HTTP_CLIENT_IP']:($_SERVER['HTTP_X_FORWARDE‌​D_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']);
    	return $_SERVER['REMOTE_ADDR'];
    }
    
    protected function check_status()
    {
    		
    	$this->log->logData("..............Check Voting status",false,"Start",false);
    		
    	$now  = date("Y-m-d h:i");
    		
    	$sql = "UPDATE [polls] SET [v_status]='closed' WHERE [poll_end_date] < '%s' AND [v_status]='open'";
    		
    	$sql = sprintf($sql, $now);
    	$res = $this->db->execute($sql);
    	//print_r($res);
    	$this->log->logData($res,false,"status of db check.....");
    		
    	$this->log->logData("..............Check Voting status",false,"end",false);
    		
    }
    
    

}

return "poll";
?>