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
		select li.LID, lname as name, lemailto as emailto, ldescription as description,
			count(th.TID) as threads, 
			count(tm.TID) as posts 		Lists
		from '.$cfg['tablenames']['Lists'].' li
		left outer join '.$cfg['tablenames']['Threads'].' th on (li.LID = th.LID)
		left outer join '.$cfg['tablenames']['ThreadMessages'].' tm on (th.TID = tm.TID)
		group by li.lname, li.lemailto, li.ldescription
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
		select th.TID,th.Threadname as subject, tm.Sender, count(tm.tid) as posts 
		from '.$cfg['tablenames']['Threads'].' th
		left outer join '.$cfg['tablenames']['ThreadMessages'].' tm on (th.tid = tm.tid) 
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
		select tm.tid , tm.Sender as sender, tm.DateSend as send_at, tm.DateReceived as received_at,
			me.Subject as subject,  me.body as text, 
			att.location as location
		from  '.$cfg['tablenames']['ThreadMessages'].' tm
		left outer join '.$cfg['tablenames']['Messages'].' me on (tm.MsgID = me.MsgID)
		left outer join '.$cfg['tablenames']['Attachements'].' att on (tm.MsgID = att.MsgID) 
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
