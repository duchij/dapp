<?php

//if (defined('__ABSTRAKTER__')) return;

class abstrakter extends main {


    function __construct(){
        parent::__construct("abstrakter");
    }

    function init($data){
        //$this->tplOutput("body.tpl");
        $this->avabKongres();
    }



    public function js_saveUserData($data)
    {
         $res = $this->db->insert_row("usersdata", $data);
         return $this->resultStatus2($res);
    }

//@TODO spravit lepsie aby som to mal ze vidim ale neda sa prihlasit
    public function avabKongres()
    {
        $today = date("Y-m-d");
        $year = date("Y");

        if ($this->isAdmin()){

            $sql = sprintf ("    SELECT  *
                            FROM [kongressdata]
                           WHERE
                               YEAR([congress_regfrom])=%d",$year);
        }else{
            $sql = sprintf ("    SELECT  *
                            FROM [kongressdata]
                           WHERE
                               '%s' BETWEEN [congress_regfrom] AND [congress_reguntil] ",$today);
        }


        //$sql = sprintf("SELECT * FROM `kongressdata` ");

        $sql = $this->db->buildSql($sql);
        $res = $this->db->table($sql);

        if ($res["status"] == false){
            $this->tplOutError("", "Error:".$res["result"]);
            return;
        }

        $table = $res["result"];

        foreach ($table as &$row){

            if ($this->is_base64($row["congres_description"])){

                $row["congres_description"] = base64_decode($row["congres_description"]);
                $row["congres_description"] = urldecode($row["congres_description"]);
            }

        }
        $this->smarty->assign("admin",1);
        $this->smarty->assign("avab_kongres",$table);
        $this->tplOutput("forms/avabkongres.tpl");

    }

    public function createCongress($data)
    {
        if (!$this->isAdmin()){
            $this->tplOutError("", "Nemáte právo editácie...");
            return;
        }

        //$this->smarty->assign('data',$insData);
        $this->tplOutput("forms/editCongres.tpl");
    }
    public function editCongress($data)
    {
        ///var_dump($_SESSION);



        $sql = sprintf("SELECT * FROM [kongressdata] WHERE [item_id]=%d",$data["id"]);
        $sql = $this->db->buildSql($sql);


        $res = $this->db->row($sql);
      //  var_dump($res);

        if ($res === FALSE)
        {
            $this->tplOutError("", $res["result"]);
            return;
        }

        if ($this->is_base64($res["congres_description"])){

            $res["congres_description"] = base64_decode($res["congres_description"]);
            $res["congres_description"] = urldecode($res["congres_description"]);
        }



        $this->smarty->assign("data",$res);
        $this->tplOutput("forms/editCongres.tpl");

    }

    public function js_saveCongress($data)
    {

        $param= "";
        if (!empty($data["item_hash"])){

            $res = $this->db->insert_row('kongressdata', $data);

        }else{

            $data["item_hash"] = md5($data["congress_titel"].$data["congress_from"].$data["congress_until"]);
            $res = $this->db->insert_row_old('kongressdata',$data,$param);
        }

        return $this->resultStatus($res["status"], $res["result"]);
    }

    public function myData($data)
    {

        if (!$this->isLogged()){
            $this->tplOutError("", "Nie ste prihlásená/ý...");
            return;
        }


        //$userId = intval($_SESSION[MODUL]["user_data"]["id"]);
        $this->smarty->assign("data",$_SESSION[MODULE]["user_data"]);

        $this->tplOutput("forms/userdata.tpl");
    }

    public function myAbstracts($data)
    {

        if (!$this->isLogged()){

            $this->tplOutError("", "Nie ste prihlásená/ý");
            return;
        }

        $user_id = $_SESSION[MODULE]["user_data"]["user_id"];

        $today = date("Y-m-d");
        $sql = sprintf("SELECT
						[registration].[item_id] AS [registr_id], [kongressdata].[item_id] AS [kongr_id],
						[registration].[user_id] AS [reg_user_id], [registration].[participation] AS [reg_participation],
						[registration].[abstract_titul] AS [abstract_titul],  [registration].[abstract_main_autor] AS [reg_main_autor],
						[registration].[abstract_autori] AS [reg_abstract_autori], [registration].[abstract_adresy] AS [reg_abstract_adresy],
						[registration].[abstract_text] AS [reg_abstract_text],[kongressdata].[congress_titel] AS [congress_titel],
						[registration].[section] AS [section],
						[kongressdata].[congress_subtitel] AS [congress_subtitel],[kongressdata].[congress_venue] AS [congress_venue],
                        [kongressdata].[congress_reguntil] AS [reg_end]

					FROM [registration]
							INNER JOIN [users] ON [users].[id] = [registration].[user_id]
							INNER JOIN [kongressdata] ON [kongressdata].[item_id] = [registration].[congress_id]
							WHERE [registration].[user_id] = %d
								AND [kongressdata].[congress_from] > '%s'

                ",$user_id,$today);

        $sql = $this->db->buildSql($sql);

        $res = $this->db->table($sql);
        //var_dump($res);

        if ($res["status"] == FALSE){

            $this->tplOutError("", $res["result"]);
            return;
        }

        $table = $res["result"];

        $nowTime = strtotime($today);

        foreach ($table as &$row){



            if ($this->is_base64($row["reg_abstract_text"])){
                $row["reg_abstract_text"] = base64_decode($row["reg_abstract_text"]);
                $row["reg_abstract_text"] = urldecode($row["reg_abstract_text"]);
            }

        }

        $this->smarty->assign("regbyuser",$table);
        $this->tplOutput("forms/regbyuser.tpl");
    }

    public function regToCongres($data)
    {

        $sql = sprintf("SELECT * FROM [kongressdata] WHERE [item_id] = %d ",intval($data["cid"]));
        $sql = $this->db->buildSql($sql);

        $res = $this->db->row($sql);

        if ($res === FALSE){
            $this->tplOutError("", $res["result"]);
            return;
        }

        $resData["congress"] = $res;
        $resData["user_id"] = $_SESSION[MODULE]["user_data"]["user_id"];
        $this->smarty->assign("data",$resData);

        $this->tplOutput("forms/abstraktreg.tpl");
    }

    public function js_saveUserAbstrReg($data)
    {

        //var_dump($data)

        if ($data["participation"] == "aktiv" && empty($data["abstract_titul"])){
            return $this->resultStatus(FALSE,"Titul je povinný....!!!");
        }

        if ($data["participation"] == "aktiv" && empty($data["abstract_main_autor"])){
            return $this->resultStatus(FALSE,"Prvý autor je povinný....!!!");
        }

        if ($data["participation"]=="aktiv" && empty($data["abstract_text"])){
            return $this->resultStatus(FALSE,"Pri aktívnej účasti je nutné vypísat aj abstrakt!!!");
        }


        if (empty($data["item_id"])){

            unset($data["item_id"]);
            $res = $this->db->insert_row_old("registration", $data, "");

            if ($res["status"] == FALSE){
                return $this->resultStatus($res["status"], $res);

            }

            $res1 = $this->sendAbstractEmailInfo($res["result"]);

            if ($res1["status"] == FALSE){
                return $this->resultStatus(FALSE, $res1["result"]);
            }


        }else{

            $data["item_id"] = intval($data["item_id"]);

            $res = $this->db->insert_row("registration", $data);
        }

        return $this->resultStatus($res["status"], $res);

    }

    public function sendAbstractEmailInfo($regId)
    {
        $sql = sprintf("SELECT
					[registration].[item_id] AS [registr_id], [kongressdata].[item_id] AS [congress_id],
					[registration].[user_id] AS [reg_user_id], [registration].[participation] AS [reg_participation],
					[registration].[abstract_titul] AS [reg_abstract_titul],  [registration].[abstract_main_autor] AS [reg_main_autor],
					[registration].[abstract_autori] AS [reg_abstract_autori], [registration].[abstract_adresy] AS [reg_abstract_adresy],
					[registration].[abstract_text] AS [reg_abstract_text],[kongressdata].[congress_titel] AS [congress_titel],
					[kongressdata].[congress_subtitel] AS [congress_subtitel],[kongressdata].[congress_venue] AS [congress_venue],
					[kongressdata].[congress_url] AS [congress_url], [kongressdata].[congress_from] AS [congress_from],
					[kongressdata].[congress_until] AS [congress_until],[kongressdata].[congress_reguntil] AS [congress_reguntil],
					[usersdata].[meno] AS [user_meno], [usersdata].[priezvisko] AS [user_priezvisko], [users].[email] AS [email]
				FROM [registration]
					INNER JOIN [users] ON [users].[id] = [registration].[user_id]
					INNER JOIN [usersdata] ON [usersdata].[user_id] = [users].[id]
					INNER JOIN [kongressdata] ON [kongressdata].[item_id] = [registration].[congress_id]
				WHERE [registration].[item_id] = %d
				",intval($regId));
        $sql = $this->db->buildSql($sql);

        $res = $this->db->row($sql);

        if (isset($res['status']))
        {
            return $this->resultStatus($res["status"], $res["result"]);
        }

        $email = array();

        $email['subject'] = "Informacia o registracii ucasti";

        $this->smarty->assign("data",$res);
        $html = $this->smarty->fetch("forms/abstrakter/mail/registration_info.tpl");

        $email["email"]=$res["email"];
        $email["message"] = $html;
       // $regData['fileName'] = 'emails/registration_info.tpl';

        $res1 = $this->sendMailMsg($email);

        return $this->resultStatus($res1["status"], $res1["message"]);

    }


    public function editUserAsbstract($data)
    {
        if (!$this->isLogged()){
            $this->tplOutError("", "Nemate pravo editacie...");
            return;
        }

        $sql = sprintf(
                "SELECT
				    [registration].[item_id] AS [registr_id], [kongressdata].[item_id] AS [congress_id],
				    [registration].[user_id] AS [reg_user_id], [registration].[participation] AS [reg_participation],
				    [registration].[abstract_titul] AS [reg_abstract_titul],  [registration].[abstract_main_autor] AS [reg_main_autor],
				    [registration].[abstract_autori] AS [reg_abstract_autori], [registration].[abstract_adresy] AS [reg_abstract_adresy],
				    [registration].[abstract_text] AS [reg_abstract_text],[kongressdata].[congress_titel] AS [congress_titel],
				    [registration].[section] AS [section], [registration].[etc] AS [etc],
				    [kongressdata].[congress_subtitel] AS [congress_subtitel],[kongressdata].[congress_venue] AS [congress_venue],
				    [kongressdata].[congress_url] AS [congress_url], [kongressdata].[congress_from] AS [congress_from],
				    [kongressdata].[congress_until] AS [congress_until],[kongressdata].[congress_reguntil] AS [congress_reguntil]
				FROM [registration]
				    INNER JOIN [users] ON [users].[id] = [registration].[user_id]
				    INNER JOIN [kongressdata] ON [kongressdata].[item_id] = [registration].[congress_id]
				WHERE [registration].[item_id] = %d

				",intval($data["aid"]));



        $sql = $this->db->buildSql($sql);

        $res = $this->db->row($sql);

        if (isset($res["status"]) && $res["status"] === FALSE){
            $this->tplOutError("", $res["result"]);
            return;
        }

        if ($res["section"] == "doctor"){
            $res["doctor_rb"] = "checked";
        }

        if ($res["section"] == "nurse"){
            $res["nurse_rb"] = "checked";
        }

        switch($res["reg_participation"]){
            case "aktiv":
                $res["aktiv_rb"] = "checked";
                break;

            case "pasiv":
                $res["pasiv_rb"] = "checked";
                break;

            case "visit":
                $res["visit_rb"] = "checked";
                break;

            default:
                $res["aktiv_rb"] = "checked";
                break;
        }

        if ($this->is_base64($res["reg_abstract_text"])){
            $res["reg_abstract_text"] = base64_decode($res["reg_abstract_text"]);
            $res["reg_abstract_text"] = urldecode($res["reg_abstract_text"]);
        }


        $nowTime = time();

        $regTime =strtotime($res["congress_reguntil"]);

        if ($nowTime > $regTime){

            $res["state"] = "readonly";
        }


        $this->smarty->assign("data",$res);
        $this->tplOutput("forms/abstraktedit2.tpl");

    }

    public function js_deleteUserAbstract($data)
    {
         $regId = intval($data["reg_id"]);

         $sql = sprintf("DELETE FROM [registration] WHERE [item_id]=%d",$regId);

         $sql = $this->db->buildSql($sql);

         $res = $this->db->execute($sql);

         return $this->resultStatus2($res);

    }

    public function getXmlData($data)
    {
        $sql = sprintf("SELECT
						[kongressdata].[congress_titel] AS [kongres],
						[usersdata].[titul_pred] AS [titul_pred], [usersdata].[meno] AS [meno], [usersdata].[priezvisko] AS [priezvisko],
						[usersdata].[titul_za] AS [titul_za], [usersdata].[contact_email] AS [contact_email],[usersdata].[contact_phone] AS [contact_phone], [usersdata].[adresa] AS [adresa],
						[users].[email] AS [email2],
						[registration].[participation] AS [ucast], [registration].[section] AS [sekcia],[registration].[abstract_titul] AS [nazov_prezentacie],
						[registration].[abstract_main_autor] AS [hlavny_autor], [registration].[abstract_autori] AS [spoluautori],
						[registration].[abstract_adresy] AS [adresy_pracoviska], [registration].[abstract_text] AS [text_abstraktu],
						[registration].[etc] AS [etc]
				FROM [registration]
							INNER JOIN [usersdata] ON [usersdata].[user_id] = [registration].[user_id]
							INNER JOIN [kongressdata] ON [kongressdata].[item_id] = [registration].[congress_id]
							INNER JOIN [users] ON [users].[id] = [registration].[user_id]
					WHERE [registration].[congress_id]=%d",intval($data["cid"]));


        $sql = $this->db->buildSql($sql);
        $res = $this->db->table($sql);


        if ($res["status"] === FALSE){
            $this->tplOutError("", "Chyba pri nacitani dat: ".$res["result"]);
            return false;
        }

        $table = $res["result"];

        if(count($table) > 0){

            foreach ($table as &$row){
                 $row["text_abstraktu"] = $this->decryptJsPhpBase64($row["text_abstraktu"]);
                 $row["text_abstraktu"] = strip_tags($row["text_abstraktu"]);
                 $row["text_abstraktu"] = html_entity_decode($row["text_abstraktu"],ENT_QUOTES, "UTF-8");
            }

            $firstRow = $table[0];
            $title = "Output";

            $this->smarty->assign("firstRow",$firstRow);
            $this->smarty->assign("data",$table);
            $this->smarty->assign("worksheetTitle",$title);

            $xml = $this->smarty->fetch("forms/abstrakter/xml/xml.tpl");


            $fh = fopen("tmp/output.xls","w+");
            fwrite($fh,$xml);
            fclose($fh);


            $this->download("tmp/output.xls");


        }

        //$data = $table['table'];


    }


    public function js_sendProgramToAll($data){

        if (!array_key_exists("conId", $data))
        {
            return $this->resultStatus(FALSE, "No congress ID provided...");
        }

        $sql = "SELECT DISTINCT([t_user.email])
                    FROM [registration] AS [t_reg]

                        INNER JOIN [users] AS [t_user] ON [t_user.id] = [t_reg.user_id]

                    WHERE [t_reg.congress_id] = %d";

        $sql = sprintf($sql,intval($data["conId"]));

        $sql=$this->db->buildSql($sql);

        $res = $this->db->table($sql);

        if ($res["status"] == FALSE){
            return $this->resultStatus(FALSE, "Error in sql:".$res["result"]);
        }


        $email = array();

        $email['subject'] = "Program kongresu";

        //$this->smarty->assign("data",$res);
        $html = $this->smarty->fetch("forms/abstrakter/mail/send_program.tpl");

        //$email["email"]=array(0=>"bduchaj@gmail.com",1=>"boris.duchaj@dfnsp.sk");

        //var_dump($res);
        //exit;
        $email["email"] = $res["result"];
        $email["addOne"] = "smrekm1@gmail.com";
        $email["message"] = $html;
        // $regData['fileName'] = 'emails/registration_info.tpl';

        $res1 = $this->sendMailMsg($email);

        if ($res1["status"] === FALSE){
            return $this->resultStatus(FALSE, $res1["message"]);
        }

        return $this->resultStatus("TRUE", "Mail was send");




    }

/** @todo spravit nasledne na kongresy
 *
 * @param unknown $data
 * @return array($status,$result);
 */
    public function js_makeAbstracts($data){

        $data["conId"] = 6;

        if (!array_key_exists("conId", $data))
        {
            return $this->resultStatus(FALSE, "No congress ID provided...");
        }

        $sql = "SELECT * FROM [def_abstracts] WHERE [congress_id]=%d";
        $sql = sprintf($sql,intval($data["conId"]));

        $sql = $this->db->buildSql($sql);


        $res = $this->db->table($sql);

        if ($res["status"] == FALSE){
            return $this->resultStatus(FALSE, "Error: ".$res["result"]);
        }

        $data = $res["result"];

        $conData = array();

        $conData["congress_title"] = "63. Kongres slovenských a českých detských chirurgov";

        $this->smarty->assign("data",$conData);
        $this->smarty->assign("defAbs",$data);

        $html = $this->smarty->fetch("forms/abstrakter/forms/abstractBook.tpl");

        $fp = fopen("tmp/abbook.html","w");
        fwrite($fp, $html);
        fclose($fp);

        return $this->resultStatus(true, "OK");


    }


    public function showRegistrations($data){



    }




}
return "abstrakter";
?>