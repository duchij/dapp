<?php



/**
 * @author bduchaj
 *
 */
class main {




    var $mail;

    /**
     * @var log $log logovacie funkcie
     */
    var $log;

    /**
     * @var smarty $smarty work with smarty template engine
     */
    var $smarty;

    /**
     * @var $commJs  Function with ascynv callbacks
     */
    var $commJs;

    /**
     * @var db the SQLlite object....
     */
    var $db;

    var $url;

    var $runClassStr="";

    function __construct($className=""){


        $this->log = new log();


        $dbData  = array (
                "server"=>  $_SESSION[MODULE]["server"],
                "user"=>    $_SESSION[MODULE]["user"],
                "passwd"=>  $_SESSION[MODULE]["passwd"],
                "db"=>      $_SESSION[MODULE]["db"]
        );

        if (DBSYSTEM=="sqlite"){
            $this->db = new sqlite("sqlite:db/".MODULE);
        }else{
            $this->db = new mysql2($dbData);
        }



        $this->smarty = new Smarty();

        $this->mail = new PHPMailer();

        $this->smarty->template_dir = APP_DIR."templates/";
        $this->smarty->compile_dir  = APP_DIR."templates_c/";
        $this->smarty->config_dir   = APP_DIR."../smarty/configs/";
        $this->smarty->cache_dir    = APP_DIR."cache/";

       // $this->smarty->assign("orthancUrl",O_URL);

        $this->url = O_URL;

        $this->smarty->assign("webUrl",WEB_URL);
        $this->smarty->assign("router",ROUTER);
        $this->smarty->assign("module",MODULE);

        $this->commJs = new commJs();

        //define("APP_LAYER", $value)
        //var_dump($_REQUEST);


        if ($className != "" )
        {
            $this->runClassStr = $className;
       if (empty($className))
            $this->runClassStr = MODULE;
        }


    }
    /**
     * Modifies return parameters
     *
     * @param boolean $status
     * @param mixed $result
     * @return array($status,$result);
     */
    public function resultStatus($status,$result){
        return array("status"=>$status,"result"=>$result);
    }

    public function resultStatus2($res)
    {
        return array("status"=>$res["status"],"result"=>$res["result"]);
    }

    public function resultData($resultStatus){
        return $resultStatus["result"];
    }

    public function loadObject($name){
        /*if (isset($GLOBALS[$name])){
            $class = &$GLOBALS[$name];
            */
      //  }else{
            if (file_exists(INCLUDE_DIR.$name.".class.php")){

               $classTmp = require_once INCLUDE_DIR.$name.'.class.php';

               $class = new $classTmp();
            }else{
                return NULL;
            }
//        }

       // $this->runClassStr = $class;
        return $class;
    }

    /**
     *
     * Funkcia zavola triedu a instancuje dla formularu ktory ju zavolal a odovzda jej webovsky request.
     *
     * @param array $data REQUEST data
     *
     * @todo toto treba obohatit a furu veci :)
     */
    public function run($data)
    {

       // exit;

       // var_dump($data);

        if (isset($data["a"]) && $data["a"] == "async"){
            $this->runAsync($data);

            return true;
        }

        if (isset($data["c"])) {

            $classStr = $data["c"];

            if (file_exists(INCLUDE_DIR.$classStr.".class.php"))
            {
                //$this->runClassStr = $classStr;

                if( $classStr=="main"){
                    if (!method_exists($this, $data["m"])){
                        echo "No main method exiting";
                        exit;
                    }
                    $this->$data["m"]($data);
                    return;
                }


                if (isset($GLOBALS[$classStr])){
                     $class = $GLOBALS[$classStr];
                }else{
                    require_once INCLUDE_DIR.$classStr.'.class.php';
                    $class = new $classStr();
                }

                if (isset($data["m"])){

                    $method = $data["m"];

                    if (method_exists($class, $method)){

                        unset($data["c"]);
                        unset($data["m"]);

                        $class->$method($data);
                    }else{
                        $this->smarty->assign("gError","Method {$method} in class {$classStr} Not Exists !!!!");
                        $this->smarty->display("forms/".MODULE."/main.tpl");
                        exit;
                    }
                }else{
                }
            }
            else
            {
                echo "no such class exiting";
                exit;
            }



        }
        else  //fallback trieda
        {

        	if (defined("AUTO_START") && !isset($data["mm"])){
        		$class = AUTO_START;
        		if (file_exists(INCLUDE_DIR.$class.".class.php")){
        			require_once INCLUDE_DIR.$class.".class.php";
        			$obj = new $class();
        			if (method_exists($obj, "init")){
        				$obj->init($data);
        			}
        		}
        	}
        	else{
        	    if (isset($data["mm"]) && !empty($data["mm"])){

        	        if (method_exists($this,$data["mm"])){
        	            $this->$data["mm"]($data);
        	        }
        	        else{
        	            $this->tplOutError("", "No such class exiting");
        	            exit;
        	        }


        	    }else{
        	        $this->smarty->display("forms/".MODULE."/main.tpl");
        	    }


        	}

        }
    }

    function runAsync($data){

        //var_dump($data);
        $this->commJs->getRespond($data["data"],"rjson");
    }

    function showErrorMsg($error){
        $this->smarty->assign("error",$error);
        $this->smarty->display("forms/.".MODULE."/main.tpl");
        exit;
    }

    function lostPassword($data)
    {
       // var_dump(MODULE);
        $this->tplOutput("forms/resetpas.tpl");
    }

    function resetPasswd($data)
    {
        if (!isset($data["l"])){
            $this->tplOutError("", "Neplatný alebo neexistujúci link...");
            exit;
        }


        $sql = "SELECT [email] FROM [reset_passwd] WHERE [reset_link]={l|s}";

        $sql = $this->db->buildSql($sql,$data);

        $row=$this->db->row($sql);


        if (isset($res["result"]) && $res["result"]==FALSE){
            $this->tplOutError("", $res["result"]);
            exit;
        }

        if (count($row)==0){
            $this->tplOutError("", "Neexistujúci link......");
            exit;
        }

        $this->smarty->assign("reset","passwd");

        $this->smarty->assign("email",$row["email"]);
        $this->smarty->assign("state","readonly");

        $this->tplOutput("forms/reguser.tpl");

    }

    function resetHeslo1($data)
    {

        $email = trim($data["email"]);

        if ($data['password'] != $data['password2'])
        {
            $this->tplOutError("", "Heslá nie sú rovnaké..");
            return;

        }

        $passwd1 = hash('md5',$data['password']);

        $sql = "UPDATE [users] SET [password]={password|s} WHERE [email]={email|s}";

        $sql = $this->db->buildSql($sql,array("password"=>$passwd1,"email"=>$data["email"]));
        $res = $this->db->execute($sql);


        if ($res['status'])
        {
            $del = sprintf("DELETE FROM [reset_passwd] WHERE [email] = '%s'",$data['email']);

            $del = $this->db->buildSql($del);

            $res1 = $this->db->execute($del);

            if ($res1["status"] == FALSE){
                $this->tplOutError("", "Nastala chyba: ".$res1["result"]);
                return;
            }
        }

        $this->tplOut("", "Nové heslo bolo nastavené, prosím prihláste sa...");

    }


    function lostPasswd1($data)
    {
        $data["email"] = trim($data["email"]);

        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) == FALSE)
        {
            $this->tplOutError("", "Toto nie je správna emailová adresa !!!!");
            exit;
        }


        $sql = sprintf("SELECT
								[users].[id] AS [user_id], [users].[email] AS [user_email],
								[usersdata].[meno] AS [meno], [usersdata].[priezvisko] AS [priezvisko],
								[usersdata].[contact_email] AS [contact_email]
							FROM [users]
						INNER JOIN [usersdata] ON [usersdata].[user_id] = [users].[id]
						WHERE [users].[email] = '%s'",$data['email']);

        $res = $this->db->row($sql);

        if(!isset($res['user_id']))
        {
            $this->tplOutError("", "Užívateľ s touto emailovou adresou nie je v zozname...");
            exit;
        }
        else
        {
            $valid_from = time();
            $valid_until = time()+(1*24*60*60);

            $hash_link = hash('sha1',"{$data['email']}-{$valid_from}");

            $insData = array(
                "email"=>$data['email'],
                "reset_link"=>$hash_link,
                "valid_from"=>$valid_from,
                "valid_until"=>$valid_until,
                "user_id"=>$res["user_id"]
            );

            $this->db->insert_row_old('reset_passwd',$insData,"");

            $emailData = array(
                "email"=>$data['email'],
                "reset_link" => $hash_link,
                "meno"=>$res['meno'],
                "priezvisko"=>$res['priezvisko'],
                "subject" => "Reset Hesla"
            );

            $this->smarty->assign("data",$emailData);
            $this->smarty->assign("mainServer",MAIN_SERVER);

            $str = $this->smarty->fetch("forms/abstrakter/mail/resetpasswd.tpl");

            $emailData["message"] = $str;


            $tmp = $this->sendMailMsg($emailData);

            if ($tmp['status'] == FALSE)
            {
                $this->tplOutError("","Chyba pri posielani spravy: {$tmp['message']}");
                exit;
            }
            else
            {
                $this->tplOut("","Na zadanú email adresu bol zaslaný link na reset hesla... Je platnosť je 24hod");

            }
            //var_dump($res);
        }
    }

    function newUser($data)
    {
        $this->tplOutput("forms/reguser.tpl");
    }

    function setNewUser($data)
    {
        $data['email'] = trim($data['email']);

        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL) == true)
        {
            //var_dump($data);

            $passwd1 = hash('md5',$data['password']);
            $passwd2 = hash('md5',$data['password2']);

            if ($passwd1 === $passwd2)
            {
                $insData = array();
                $insData['email'] = $data['email'];
                $insData['password'] = $passwd1;

                $trans = $this->db->startTransaction();

                if (!$trans){
                    $this->tplOutError("", "Error starting transaction, user data not saved");
                    return;
                }


                $res = $this->db->insert_row_old("users",$insData,"");

                if (!$res["status"]){

                    if (stripos($res["result"],"duplicate")!==FALSE){

                        $this->tplOutError("", "Užívateľ už existuje prosím použite tlačidlo zabudnuté heslo");
                    }
                    else{
                        $this->tplOutError("", "Chyba pri ukladani: ".$res["result"]);
                    }

                    $this->db->rollBackTransaction($trans);
                    return;
                }


                $tmp=array();
                $tmp['user_id'] = $res['result'];
                $tmp['contact_email'] = $data['email'];

                $res1 = $this->db->insert_row_old('usersdata',$tmp,"");

                if (!$res1["status"]){

                    $this->tplOutError("", "Chyba pri ukladani:".$res1["result"]);
                    $this->db->rollBackTransaction($trans);
                    return;

                }

                $this->db->commitTransaction();


                $this->smarty->assign("mail_acc",MAIL_ACC);
                $html = $this->smarty->fetch("forms/abstrakter/forms/mail/newuser_html_mail.tpl");

                $emailData = array(
                        "subject"=>"Registracia noveho uzivatela",
                        "email" =>$tmp["contact_email"],
                        "message"=>html


                );

                $tmpRes = $this->sendMailMsg($emailData);
//                 $tmpRes = true;
                if ($tmpRes['status'] == FALSE)
                {
                    $this->tplOutError("","Chyba pri posielaní mailu:".$tmpRes["result"]);
                    return;
                }
                else{
                    $this->tplOut("", "Teraz sa môžete prihlásiť do aplikácie.
                                        Prosím, čo najskôr si vyplňte Vaše údaje v menu Moje údaje.
                        ");
                    return true;
                }


            }
            else
            {
                $this->tplOutError("","Heslá sa nerovnajú");
            }

        }
        else
        {
            $this->tplOutError("","Toto nie je správna emailová adresa..");
        }
    }




    /**
     * Prints a success message to template file...
     *
     * @param string $templateFile
     * @param string $message
     */
    function tplOut($templateFile,$message)
    {

        $errorMsg="<div class='success box large'>{$message}</div>";
        $this->smarty->assign("errorMsg",$errorMsg);
        $this->smarty->assign("className",$this->runClassStr.".js");
        if (!empty($templateFile)){
            $this->smarty->assign("body",$templateFile);
        }
        $this->smarty->assign("module",MODULE);
        $this->smarty->display("forms/".MODULE."/main.tpl");
        //return false;
    }

    function tplOutError($templateFile,$error)
    {
       // var_dump(MODULE);
        $errorMsg="<div class='error box large'>{$error}</div>";
        $this->smarty->assign("errorMsg",$errorMsg);
        $this->smarty->assign("className",$this->runClassStr.".js");
        if (!empty($templateFile)){
            $this->smarty->assign("body",$templateFile);
        }

        $this->smarty->assign("module",MODULE);
        $this->smarty->display("forms/".MODULE."/main.tpl");
        //return false;
    }



    function tplOutput($templateFile)
    {
        //var_dump($this);
        $this->smarty->assign("module",MODULE);
        $this->smarty->assign("className",$this->runClassStr.".js");
        $this->smarty->assign("body",$templateFile);
        $this->smarty->display("forms/".MODULE."/main.tpl");
    }

    function tplHtmlOutPut($templateFile)
    {
    	$this->smarty->display("forms/".$this->runClassStr."/".$templateFile);
    }


    protected function evpKDF($password, $salt, $keySize = 8, $ivSize = 4, $iterations = 1, $hashAlgorithm = "md5")
    {
    	$targetKeySize = $keySize + $ivSize;
    	$derivedBytes = "";
    	$numberOfDerivedWords = 0;
    	$block = NULL;
    	$hasher = hash_init($hashAlgorithm);
    	while ($numberOfDerivedWords < $targetKeySize) {
    		if ($block != NULL) {
    			hash_update($hasher, $block);
    		}
    		hash_update($hasher, $password);
    		hash_update($hasher,$salt);
    		$block = hash_final($hasher, TRUE);
    		$hasher = hash_init($hashAlgorithm);

    		// Iterations
    		for ($i = 1; $i < $iterations; $i++) {
    			hash_update($hasher, $block);
    			$block = hash_final($hasher, TRUE);
    			$hasher = hash_init($hashAlgorithm);
    		}

    		$derivedBytes .= substr($block, 0, min(strlen($block), ($targetKeySize - $numberOfDerivedWords) * 4));

    		$numberOfDerivedWords += strlen($block)/4;
    	}

    	return array(
    			"key" => substr($derivedBytes, 0, $keySize * 4),
    			"iv"  => substr($derivedBytes, $keySize * 4, $ivSize * 4)
    	);
    }

    protected function decrypt($text)
    {
    	$kp = substr(session_id(),0,16);
    	$pp = base64_decode($text);

    	$salt = substr($pp,8,8);
    	$r = $this->evpKDF($kp, $salt);

    	$decryptPassword = mcrypt_decrypt(MCRYPT_RIJNDAEL_128,
    			$r["key"],
    			substr($pp,16),
    			MCRYPT_MODE_CBC,
    			$r["iv"]);

    	$str = substr($decryptPassword, 0, strlen($decryptPassword) - ord($decryptPassword[strlen($decryptPassword)-1]));
    	return $str;

    }


    function login($data)
    {
    	//hash_update

     //  var_dump($data);

    	if ( !isset($data["name"]) || !isset($data["password"]) ){
    		$this->smarty->display("main.tpl");
    		return;
    	}

       $name = $this->decrypt($data["name"]);
       $password = md5($this->decrypt($data["password"]));
      // $password = sha1(SALT.$this->decrypt($data["password"]));
      // var_dump($password);

      /* if (!preg_match("/^[a-z0-9_]+$/",$name))
       {
           $this->tplOutError("", "Incorect login name");
          // exit;
          return;
       }*/

       if (!filter_var($name,FILTER_VALIDATE_EMAIL) && $name !="admin"){

       		$this->tplOutError("", "Incorect login name");
       	// exit;
       		return;
       }

       $sql="";

       if (DBSYSTEM == "mysql"){
           $sql = $this->db->buildSql("
               SELECT [email],[password],[id],[account] FROM [users] WHERE [email]={name|s}", array("name"=>$name));
       }

       $row = $this->db->row($sql);

       //var_dump($row);

       if ($row === FALSE){
           $this->tplOutError("", "No such user....");
           return;
       }

       if ($row["password"] == NULL){
           $_SESSION["cpAcc"] = $name;
           $this->tplOutput("accounts/passwd.tpl");
           return;
       }

       if ($row["password"] != $password){
          $this->tplOutError("","Incorrect password.....");
          return;
       }

       setcookie("session".MODULE,session_id());

       if (DBSYSTEM == "sqlite"){
           $this->startSession($name);
       }

       if (DBSYSTEM == "mysql"){
           $this->setUserData($name);
       }

    }

    protected  function setUserData($name)
    {
        $sql = "SELECT [t_users].*,[t_data].*
                        FROM [users] AS [t_users]
                INNER JOIN [usersdata] AS [t_data] ON [t_data.user_id] = [t_users.id]
                WHERE [t_users.email] = {name|s}

            ";

        $sql = $this->db->buildSql($sql,array("name"=>$name));
        $row = $this->db->row($sql);

        if ($row == FALSE){
            $this->tplOutputError("main.tpl","Error geting user data");
            return;
        }

        unset($row["password"]);
        $_SESSION[MODULE]["user_data"] = $row;

        $_SESSION["logged"] = true;
        $_SESSION["app"] = "start";

        if ($row["account"] == "admin"){
            $_SESSION["isAdmin"] = true;
        }else{
            $_SESSION["isAdmin"] = false;
        }
        //session_start();
        $obj = $this->loadObject(MODULE);
        $obj->init($_REQUEST);


    }

    public function logout($data)
    {
        session_unset();
        session_destroy();
        setcookie("session.abstrakter","");
       // $this->smarty->display("main.tpl");
        header("Location: http://".MAIN_SERVER);
        exit;
    }

    public function startSession($name)
    {
        $sql = $this->db->buildSql("
               SELECT [login],[passwd],[id],[type],[user_hash]
                    FROM [users]
               WHERE [login]={name|s}", array("name"=>$name));
        $row = $this->db->row($sql);

        $_SESSION["account"] = $row["login"];
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["account_type"] = $row["type"];
        $_SESSION["user_hash"] = $row["user_hash"];

        $this->smarty->display("main.tpl");

    }


    function sendMailMsg($data)
	{

		$this->mail->isSMTP();                                      // Set mailer to use SMTP
		$this->mail->Host = MAIL_SERV;  	// Specify main and backup server
		$this->mail->Port = MAIL_PORT;
		$this->mail->SMTPAuth = true;                               // Enable SMTP authentication
		$this->mail->Username = MAIL_ACC;                            // SMTP username
		$this->mail->Password = MAIL_PSS;                           // SMTP password
		//$this->mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted

		$this->mail->From = MAIL_ACC;
		$this->mail->FromName = "Abstrakter web";
		//$this->mail->addAddress('josh@example.net', 'Josh Adams');  // Add a recipient
		$this->mail->addAddress($data['email']);               // Name is optional
		//$this->mail->addReplyTo($_SESSION['abstrakter']['mail_acc'], $_SESSION['abstrakter']['mail_from_name']);
		//$this->mail->addCC($_SESSION['abstrakter']['mail_acc']);
		//	$this->mail->addBCC('bcc@example.com');

		$this->mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		//	$this->mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		//	$this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$this->mail->isHTML(true);                                  // Set email format to HTML

		$this->mail->Subject = $data["subject"];
		$this->mail->Body    = $data["message"];
		$this->mail->CharSet ="UTF-8";

		$result = array("status"=>TRUE,"message"=>'');

		if (!$this->mail->send())
		{
			//$this->smarty->assign('error',$this->mail->ErrorInfo);
			//$this->smarty->display('error.tpl');
			$result['message'] = $this->mail->ErrorInfo;
			$result['status'] = FALSE;
		}

		return $result;
	}

	public function isLogged()
	{
	    if (array_key_exists("logged", $_SESSION)){
	        return true;
	    }
	    return false;
	}

	public function isAdmin()
	{
	    if (!array_key_exists("isAdmin", $_SESSION)){
	        return false;
	    }


	    return $_SESSION["isAdmin"];
	}



	function sendMail2($data)
	{
	    if (is_array($data["email"])){
	        $to = join(",", $data["email"]);
	    }else{

	        $to = $data["email"];
	    }
	    //$to .=",niekto druhy";
	    $subject = $data["subject"];

	    // To send HTML mail, the Content-type header must be set
	    $headers  = 'MIME-Version: 1.0' . "\r\n";
	    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

	    // Additional headers
	    $headers .= 'To: '.$data["email"] . "\r\n";
	    $headers .= 'From: EspesWeb <espesweb@1and1.es>' . "\r\n";
	    if (isset($data["email_cc"])){
	        $headers .= 'Cc: '.$data["email_cc"] . "\r\n";
	    }
	    if (isset($data["email_bcc"])){
	        $headers .= 'Bcc: '.$data["email_bcc"] . "\r\n";
	    }

	    $res = mail($to,$subject,$data["message"],$headers);

	    return $res;
	}

	function createMessage($text,$type)
	{
	   $result = "";
       switch ($type){
           case "success":
               $result = sprintf("<div class='success box'>%s</div>",$text);
               break;
           case "error":
               $result = sprintf("<div class='error box'>%s</div>",$text);
               break;
           default:
               $result = sprintf("<div class='box'>%s</div>",$text);
               break;
       }

	   return $result;
	}

	function download($fileName)
	{
	    //ob_start();
	    header("Content-Description: File Transfer");
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		$tmpFl = basename($fileName);
		header("Content-Disposition: attachment; filename={$tmpFl}");
		//ob_clean();
		flush();
		readfile($fileName);
	}

	function is_base64($s)
	{
	    return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
	}

	function decryptJsPhpBase64($text)
	{
	    if ($this->is_base64($text)){

	        $res= base64_decode($text);
	        $res = urldecode($res);

	        return $res;
	    }

	    return $text;
	}



}

?>
