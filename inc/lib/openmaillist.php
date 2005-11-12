<?php

	
class openmaillist {

	// Connecting, selecting database
		
		$link = mysql_connect($host, $user, $pass)
		or die('Could not connect: ' . mysql_error());
		//echo 'Connected successfully';
		mysql_select_db('test') or die('Could not select database');
		
	function get_lists() {
		global $cfg;
		$tmp	= array();

	
		$result = mysql_query('
		select li.LID, lname as Name, LEmailTo as EmailTo, LDescription as Description,
			count(th.TID) as Threads, 
			count(tm.TID) as Posts
		from Lists li
		left outer join Threads th on (li.LID = th.LID)
		left outer join ThreadMessages tm on (th.TID = tm.TID)
		group by li.lname, li.LEmailTo, li.LDescription
		-- Name Status 	Threads Posts 	Last post
		');

		if(mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_assoc($result)) {
			$tmp[] = $row;
		}
		mysql_free_result($result);
		}
		return $tmp;
		}

		




	function get_threads() {
		global $cfg;
		$tmp	= array();

	
		$result = mysql_query('
		select th.TID,th.Threadname as Subject, tm.Sender, count(tm.tid) as Posts 
		from Threads th
		left outer join ThreadMessages tm on (th.tid = tm.tid) 
		-- In Abhaengikeit von th.tid,
		where '.$_GET["list"].'= th.lid
		group by th.Threadname, tm.Sender
		order by tm.DateReceived
		-- Subject 	Status 	Posts 	Views 	Sender 	Last post
		';
		if(mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_assoc($result)) {
			$tmp[] = $row;
		}
		mysql_free_result($result);
		}
		return $tmp;
		}

 //href=\"http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?list=".$_GET["list"]."&thread=".$line["TID"]."\">".$col_value."</a></td>\n";
	function get_messages() {

		if ($_GET["list"] != NULL && $_GET["thread"] != NULL){
		
		$result = mysql_query('
		select tm.TID , tm.Sender, tm.DateSend as "Send at", tm.DateReceived as "Received at",
			me.Subject as "Subject",  me.body as "Text", 
			att.location as "Location"
		from ThreadMessages tm
		left outer join Messages me on (tm.MsgID = me.MsgID)
		left outer join Attachements att on (tm.MsgID = att.MsgID) 
		where '.$_GET["thread"].' = tm.TID
		order by tm.DateReceived

		') ;

		if(mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_assoc($result)) {
			$tmp[] = $row;
		}
		mysql_free_result($result);
		}
		return $tmp;
		}

		//echo "\t\t<td>".$col_value."</td>\n";

	}

}

?>
