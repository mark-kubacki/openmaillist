-- phpMyAdmin SQL Dump
-- version 2.6.4-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 05. November 2005 um 20:56
-- Server Version: 5.0.15
-- PHP-Version: 4.4.1-pl1
-- 
-- Datenbank: `test`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `Attachements`
-- 

DROP TABLE IF EXISTS `Attachements`;
CREATE TABLE `Attachements` (
  `AttID` int(10) unsigned NOT NULL auto_increment,
  `MsgID` varchar(128) collate utf8_unicode_ci NOT NULL,
  `Location` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`AttID`),
  KEY `MsgID` (`MsgID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `Attachements`
-- 

INSERT DELAYED INTO `Attachements` (`AttID`, `MsgID`, `Location`) VALUES 
(1, '200510281112.13579.alex@noligy.de', '/uploads/xorg-excerpt.conf');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `Lists`
-- 

DROP TABLE IF EXISTS `Lists`;
CREATE TABLE `Lists` (
  `LID` int(10) unsigned NOT NULL auto_increment,
  `LName` varchar(128) collate utf8_unicode_ci NOT NULL,
  `LEmailTo` varchar(96) collate utf8_unicode_ci NOT NULL,
  `LDescription` tinytext collate utf8_unicode_ci,
  PRIMARY KEY  (`LID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='This table is to contain all the available lists.' AUTO_INCREMENT=4 ;

-- 
-- Daten für Tabelle `Lists`
-- 

INSERT DELAYED INTO `Lists` (`LID`, `LName`, `LEmailTo`, `LDescription`) VALUES 
(1, 'openmailadmin', 'list@openmailadmin.org', 'Everything about openmailadmin.'),
(2, 'openmaillist', 'list@openmaillist.org', 'Do you enjoy the great product of Alex and Mark? Words of praise go here.'),
(3, 'Noligy''s Exchange', 'exchange@noligy.de', 'Ich <b>liebe</b> Möpse. Leider <i>vertragen</i> sie sich nicht mit Schnauzern.');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `Messages`
-- 

DROP TABLE IF EXISTS `Messages`;
CREATE TABLE `Messages` (
  `MsgID` varchar(128) collate utf8_unicode_ci NOT NULL,
  `Subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  `Body` text collate utf8_unicode_ci NOT NULL,
  `Header` text collate utf8_unicode_ci NOT NULL,
  `Attach` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`MsgID`),
  FULLTEXT KEY `Body` (`Body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Messages.';

-- 
-- Daten für Tabelle `Messages`
-- 

INSERT DELAYED INTO `Messages` (`MsgID`, `Subject`, `Body`, `Header`, `Attach`) VALUES 
('200510281112.13579.alex@noligy.de', 'Mouserad unter Linux', 'Hi Mark,\r\n\r\nhiermit sollte es gehen:\r\n\r\n\r\nSection "InputDevice"\r\n        Identifier      "Configured Mouse"\r\n        Driver          "mouse"\r\n        Option          "CorePointer"\r\n        Option          "Device"                "/dev/input/mice"\r\n        Option          "Protocol"              "ImPS/2"\r\n        Option          "ZAxisMapping"          "4 5"\r\n', 'Return-Path: <alex@noligy.de>\r\nReceived: from murder ([unix socket])\r\n	 by neon (Cyrus v2.2.12) with LMTPA;\r\n	 Fri, 28 Oct 2005 11:12:42 +0200\r\nX-Sieve: CMU Sieve 2.2\r\nReceived: from vandal.local (dslc-082-082-171-121.pools.arcor-ip.net [82.82.171.121])\r\n	(using TLSv1 with cipher RC4-MD5 (128/128 bits))\r\n	(No client certificate requested)\r\n	by neon.hurrikane.de (Postfix) with ESMTP id 7091DC088E5\r\n	for <wmark@hurrikane.de>; Fri, 28 Oct 2005 11:12:41 +0200 (CEST)\r\nFrom: Alexander Wall <alex@noligy.de>\r\nTo: "W-Mark Kubacki" <wmark@hurrikane.de>\r\nSubject: Mouserad unter Linux\r\nDate: Fri, 28 Oct 2005 11:12:13 +0000\r\nUser-Agent: KMail/1.8.2\r\nMIME-Version: 1.0\r\nContent-Type: text/plain;\r\n  charset="us-ascii"\r\nContent-Transfer-Encoding: 7bit\r\nContent-Disposition: inline\r\nMessage-Id: <200510281112.13579.alex@noligy.de>\r\nX-Spam-Checker-Version: SpamAssassin 3.0.4-gr0 (2005-06-05) on \r\n	neon.hurrikane.de\r\nX-Spam-Level: \r\nX-Spam-Status: No, score=-3.7 required=5.0 tests=AWL,FORGED_RCVD_HELO,\r\n	LOCAL_SENDER_FROM,RCVD_IN_NJABL_DUL,RCVD_IN_SORBS_DUL autolearn=ham \r\n	version=3.0.4-gr0\r\n', 1),
('20051101205153.GA891@dsxxxxxxxxx.dedicated.hosteurope.de', 'Mailbox names limited to 16 chars', 'Hello!\r\n\r\nIs there a technical reason that the mailbox names are limited to 16\r\ncharacters by openmailadmin?\r\nIf not, I would replace "16" with my desired max length everywhere (including\r\ndatabase.sql, before creating tables) and be happy.\r\n\r\nBye\r\nJochen Sxxxxxx\r\n', 'Return-Path: <list@openmailadmin.org>\r\nReceived: from murder ([unix socket])\r\n	 by neon (Cyrus v2.2.12) with LMTPA;\r\n	 Tue, 01 Nov 2005 22:20:13 +0100\r\nX-Sieve: CMU Sieve 2.2\r\nReceived: by neon.hurrikane.de (Postfix, from userid 81)\r\n	id 7F172C0B55D; Tue,  1 Nov 2005 22:20:12 +0100 (CET)\r\nReceived: from murder ([unix socket])\r\n	 by neon (Cyrus v2.2.12) with LMTPA;\r\n	 Tue, 01 Nov 2005 22:20:06 +0100\r\nX-Greylist: delayed 1673 seconds by postgrey-1.21 at neon; Tue, 01 Nov 2005 22:20:03 CET\r\nReceived: from sxxxxxx.net (xxxxxxx.clients.your-server.de [123.123.123.123])\r\n	by neon.hurrikane.de (Postfix) with ESMTP id C215AC0B55C\r\n	for <list@openmailadmin.org>; Tue,  1 Nov 2005 22:20:03 +0100 (CET)\r\nReceived: from localhost (localhost [127.0.0.1])\r\n	by sxxxxxxxxx.net (Postfix) with ESMTP id 6E0EAB80007\r\n	for <list@openmailadmin.org>; Tue,  1 Nov 2005 21:51:53 +0100 (CET)\r\nReceived: from sxxxxx.net ([127.0.0.1])\r\n by localhost (sxxxxx.net [127.0.0.1]) (amavisd-new, port 10024) with ESMTP\r\n id 00685-03 for <list@openmailadmin.org>;\r\n Tue,  1 Nov 2005 21:51:53 +0100 (CET)\r\nReceived: by sxxxxxxxx.net (Postfix, from userid 1000)\r\n	id 49DF7B80003; Tue,  1 Nov 2005 21:51:53 +0100 (CET)\r\nDate: Tue, 1 Nov 2005 21:51:53 +0100\r\nFrom: Jochen Sxxxxx <boger@sxxxxxx.net>\r\nTo: list@openmailadmin.org\r\nMessage-ID: <20051101205153.GA891@dsxxxxxxx-141.dedicated.hosteurope.de>\r\nMime-Version: 1.0\r\nContent-Type: text/plain; charset=iso-8859-1\r\nContent-Disposition: inline\r\nContent-Transfer-Encoding: 8bit\r\nUser-Agent: Mutt/1.5.9i\r\nX-Virus-Scanned: by amavisd-new at sxxxxxx.net\r\nReply-To: "openmailadmin" <list@openmailadmin.org>\r\nPrecedence: bulk\r\nMailing-List: <list@openmailadmin.org>\r\nDelivered-To: <list@openmailadmin.org>\r\nList-Id: openmailadmin <list.openmailadmin.org>\r\nList-Subscribe: <mailto:list@openmailadmin.org?subject=subscribe>\r\nList-Unsubscribe: <mailto:list@openmailadmin.org?subject=unsubscribe>\r\nList-Post: <mailto:list@openmailadmin.org>\r\nSubject: [openmailadmin] Mailbox names limited to 16 chars\r\nX-Spam-Checker-Version: SpamAssassin 3.0.4-gr0 (2005-06-05) on \r\n	neon.hurrikane.de\r\nX-Spam-Level: \r\nX-Spam-Status: No, score=0.1 required=5.0 tests=FORGED_RCVD_HELO \r\n	autolearn=unavailable version=3.0.4-gr0', 0),
('43692E64.5010708@hurrikane.de', 'Re: Mailbox names limited to 16 chars', 'Hallo,\r\n\r\nan older IMAP server had a limitation of 16 characters in mailbox names.\r\n\r\nIf you use something recent you can freely reset that limitation - indeed, I have already made these limits (upper and lower) configurable. See also [1].\r\n\r\n(Unless an installer is made which can query for limits you still are to modify database.sql, too.)\r\n\r\n\r\nGru�,\r\n\r\nW-Mark Kubacki\r\n\r\n[1] http://www.openmailadmin.org/changeset/138\r\n', 'Return-Path: <list@openmailadmin.org>\r\nReceived: from murder ([unix socket])\r\n	 by neon (Cyrus v2.2.12) with LMTPA;\r\n	 Wed, 02 Nov 2005 22:22:16 +0100\r\nX-Sieve: CMU Sieve 2.2\r\nReceived: by neon.hurrikane.de (Postfix, from userid 81)\r\n	id 12F30C0B55C; Wed,  2 Nov 2005 22:22:14 +0100 (CET)\r\nReceived: from murder ([unix socket])\r\n	 by neon (Cyrus v2.2.12) with LMTPA;\r\n	 Wed, 02 Nov 2005 22:22:07 +0100\r\nReceived: from [192.168.1.34] (dslc-082-082-161-031.pools.arcor-ip.net [82.82.161.31])\r\n	(using TLSv1 with cipher DHE-RSA-AES256-SHA (256/256 bits))\r\n	(No client certificate requested)\r\n	by neon.hurrikane.de (Postfix) with ESMTP id AA0D8C0B55B\r\n	for <list@openmailadmin.org>; Wed,  2 Nov 2005 22:22:05 +0100 (CET)\r\nMessage-ID: <43692E64.5010708@hurrikane.de>\r\nDisposition-Notification-To: W-Mark Kubacki <wmark@hurrikane.de>\r\nDate: Wed, 02 Nov 2005 22:23:48 +0100\r\nFrom: W-Mark Kubacki <wmark@hurrikane.de>\r\nUser-Agent: Mail/News 1.4.1 (X11/20051030)\r\nMIME-Version: 1.0\r\nTo: openmailadmin <list@openmailadmin.org>\r\nContent-Type: text/plain; charset=ISO-8859-1; format=flowed\r\nContent-Transfer-Encoding: 8bit\r\nReply-To: "openmailadmin" <list@openmailadmin.org>\r\nPrecedence: bulk\r\nMailing-List: <list@openmailadmin.org>\r\nDelivered-To: <list@openmailadmin.org>\r\nList-Id: openmailadmin <list.openmailadmin.org>\r\nList-Subscribe: <mailto:list@openmailadmin.org?subject=subscribe>\r\nList-Unsubscribe: <mailto:list@openmailadmin.org?subject=unsubscribe>\r\nList-Post: <mailto:list@openmailadmin.org>\r\nIn-Reply-To: <20051101205153.GA891@ds217-115-141-141.dedicated.hosteurope.de>\r\nReferences: <20051101205153.GA891@ds217-115-141-141.dedicated.hosteurope.de>\r\nSubject: Re: [openmailadmin] Mailbox names limited to 16 chars\r\nX-Spam-Checker-Version: SpamAssassin 3.0.4-gr0 (2005-06-05) on \r\n	neon.hurrikane.de\r\nX-Spam-Level: \r\nX-Spam-Status: No, score=-3.4 required=5.0 tests=AWL,LOCAL_SENDER_FROM,\r\n	RCVD_IN_NJABL_DUL,RCVD_IN_SORBS_DUL autolearn=unavailable \r\n	version=3.0.4-gr0', 0);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `ThreadMessages`
-- 

DROP TABLE IF EXISTS `ThreadMessages`;
CREATE TABLE `ThreadMessages` (
  `MsgID` varchar(128) collate utf8_unicode_ci NOT NULL,
  `TID` int(10) unsigned NOT NULL,
  `DateSend` datetime NOT NULL,
  `DateReceived` datetime NOT NULL,
  `Sender` varchar(128) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`MsgID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Zuordnung von Emails zu Threads.';

-- 
-- Daten für Tabelle `ThreadMessages`
-- 

INSERT DELAYED INTO `ThreadMessages` (`MsgID`, `TID`, `DateSend`, `DateReceived`, `Sender`) VALUES 
('200510281112.13579.alex@noligy.de', 2, '2005-10-28 11:12:41', '2005-10-28 11:12:42', '<alex@noligy.de>'),
('20051101205153.GA891@dsxxxxxxxxxxxx.dedicated.hosteurope.de', 1, '2005-11-01 21:51:08', '2005-11-01 22:20:23', 'Jochen SXXXX <boger@sXXXXll.net>'),
('43692E64.5010708@hurrikane.de', 1, '2005-11-02 22:23:05', '2005-11-02 22:24:16', 'W-Mark Kubacki <wmark@hurrikane.de>');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `Threads`
-- 

DROP TABLE IF EXISTS `Threads`;
CREATE TABLE `Threads` (
  `TID` int(10) unsigned NOT NULL auto_increment,
  `LID` int(10) unsigned NOT NULL,
  `Threadname` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`TID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='All the threads ever opened.' AUTO_INCREMENT=3 ;

-- 
-- Daten für Tabelle `Threads`
-- 

INSERT DELAYED INTO `Threads` (`TID`, `LID`, `Threadname`) VALUES 
(1, 1, 'Limitation of mailboxnames'),
(2, 3, 'Mouserad unter Linux');

