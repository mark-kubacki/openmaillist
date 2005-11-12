<?php
class openmaillist {

	function openmaillist() {
		global $cfg;

		$link = mysql_connect($cfg['Servers']['DB'][0]['HOST'],
								$cfg['Servers']['DB'][0]['USER'],
								$cfg['Servers']['DB'][0]['PASS'])
		or die('Could not connect to MySQL Server');
		mysql_select_db($cfg['Servers']['DB'][0]['DB'])
		or die('Could not select database');
	}

	function __destruct() {
		mysql_close();
	}

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
		');

		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_assoc($result)) {
				$tmp[] = $row;
			}
			mysql_free_result($result);
		}
		return $tmp;
	}

	function get_threads($list_id) {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
			select th.TID,th.Threadname as Subject, tm.Sender, count(tm.tid) as Posts
			from Threads th
			left outer join ThreadMessages tm on (th.tid = tm.tid)
			where '.$list_id.'= th.lid
			group by th.Threadname, tm.Sender
			order by tm.DateReceived
		');

		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_assoc($result)) {
				$tmp[] = $row;
			}
			mysql_free_result($result);
		}

		return $tmp;
	}

	function get_messages($thread_id) {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
		select tm.TID , tm.Sender, tm.DateSend as "Send at", tm.DateReceived as "Received at",
			me.Subject as "Subject",  me.body as "Text",
			att.location as "Location"
		from ThreadMessages tm
		left outer join Messages me on (tm.MsgID = me.MsgID)
		left outer join Attachements att on (tm.MsgID = att.MsgID)
		where '.$thread_id.' = tm.TID
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

}

?>
