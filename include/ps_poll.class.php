<?php 
if (!isset($_SESSION['_sf2_meta'])){
	//echo "This is not a Espes.eu session. Exiting....";
	//exit;
}

class ps_poll extends main
{
	function __construct(){
		parent::__construct();
	}
	
	public function check_status()
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
	
	public function importMemId()
	{
		$fileName = "data/mem.csv";
		
		$fh = fopen($fileName, "r");
		
		$data = fread($fh, filesize($fileName));
		
		fclose($fh);
		
		$datArr = explode("\n",$data);
		
		$dtLn = count($datArr);
		
		for ($i=0; $i<$dtLn; $i++){
			
			$datArr[$i] = str_replace('"', "", $datArr[$i]);
			
			$tmp=explode(",", $datArr[$i]);
			
			if (intval($tmp[0])>0){
				$sql ="UPDATE [users] SET [membership_id]=%d 
							WHERE [espes_num]='%s'
							AND [membership_id] IS NULL
						";
				
				$sql = sprintf($sql,intval($tmp[0]),$tmp[1]);
				
				$res = $this->db->execute($sql);
				
				if ($res["status"]){
					echo "<font color='green'>OK.....</font>".$tmp[1];
					var_dump($res);
					echo "<br>";
				}
				else{
					echo "<font color='red'>Error.....</font>".$tmp[1];
					var_dump($res);
					echo "<br>";
				}
			}
		}
	}
}

return "ps_poll";
?>