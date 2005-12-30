<?php
/**
* Test class for oml_email.
*/
class oml_emailTest
	extends PHPUnit2_Framework_TestCase
{
	private $test_emails	= array();

	/**
	 * Runs the test methods of this class.
	 */
	public static function main() {
		require_once "PHPUnit2/TextUI/TestRunner.php";

		$suite  = new PHPUnit2_Framework_TestSuite('oml_emailTest');
		$result = PHPUnit2_TextUI_TestRunner::run($suite);
	}

	/**
	 * Sets up the fixture, for example, open a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		global $cfg;

		$this->test_emails
		= array(1	=> new oml_email(file_get_contents($cfg['sample_msg'].'/1.')),
			2	=> new oml_email(file_get_contents($cfg['sample_msg'].'/2.')),
			3	=> new oml_email(file_get_contents($cfg['sample_msg'].'/3.')),
			4	=> new oml_email(file_get_contents($cfg['sample_msg'].'/4.')),
			82	=> new oml_email(file_get_contents($cfg['sample_msg'].'/82.')),
			83	=> new oml_email(file_get_contents($cfg['sample_msg'].'/83.')),
			100	=> new oml_email(file_get_contents($cfg['sample_msg'].'/100.')),
			101	=> new oml_email(file_get_contents($cfg['sample_msg'].'/101.')),
			102	=> new oml_email(file_get_contents($cfg['sample_msg'].'/102.')),
			150	=> new oml_email(file_get_contents($cfg['sample_msg'].'/150.')),
		);
	}

	/**
	 * Tears down the fixture, for example, close a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		@rmdir('/tmp/mytest');
	}

	public function testGetDateReceived() {
		$cmp
		= array(1	=> 'Fri, 28 Oct 2005 11:12:40 +0100',
			2	=> 'Fri, 28 Oct 2005 12:12:40 +0100',
			3	=> 'Mon, 26 Dec 2005 17:45:24 +0100',
			4	=> 'Sun, 27 Nov 2005 18:27:45 +0100',
			82	=> 'Sat, 17 Dec 2005 16:35:18 +0100',
			83	=> 'Sun, 18 Dec 2005 02:08:44 +0100',
		);

		foreach($this->test_emails as $n => $email) {
			if(!isset($cmp[$n]))	continue;
			$this->assertEquals($email->get_header('date-received'), strtotime($cmp[$n]), 'message number was '.$n.',');
		}
	}

	public function testGetDateSend() {
		$cmp
		= array(1	=> 'Fri, 28 Oct 2005 11:12:39 +0100 (CET)',
			2	=> 'Fri, 28 Oct 2005 12:12:39 +0100 (CET)',
			3	=> 'Fri, 28 Oct 2005 11:12:39 +0100 (CET)',
			4	=> 'Sun, 27 Nov 2005 18:27:43 +0100 (CET)',
			82	=> 'Sat, 17 Dec 2005 07:35:14 -0800 (PST)',
			83	=> 'Sun, 18 Dec 2005 02:08:44 +0100',
		);

		foreach($this->test_emails as $n => $email) {
			if(!isset($cmp[$n]))	continue;
			$this->assertEquals($email->get_header('date-send'), strtotime($cmp[$n]), 'message number was '.$n.',');
		}
	}

	public function testGetMessageIDs() {
		$cmp
		= array(1	=> '200510281111.13579.user1@example.com',
			2	=> '200510281112.13579.user2@example.com',
			3	=> '200512261117.24523.user3@example.com',
			4	=> '4389EC98.1020303@hurrikane.de',
			82	=> '20051217153514.7943F48254@mail.freshmeat.net',
			83	=> '43A4B69C.8000106@hurrikane.de',
		);

		foreach($this->test_emails as $n => $email) {
			if(!isset($cmp[$n]))	continue;
			$this->assertEquals($email->get_header('message-id'), $cmp[$n], 'message number was '.$n.',');
		}
	}

	public function testGetFrom() {
		$cmp
		= array(1	=> '"Sample User 1" <sample1@example.com>',
			2	=> '"Sample User 2" <sample2@example.com>',
			3	=> '"Sample User 3" <sample@example.com>',
			4	=> 'W-Mark Kubacki <wmark@hurrikane.de>',
			82	=> '<noreply@freshmeat.net>',
			83	=> 'W-Mark Kubacki <wmark@hurrikane.de>',
		);

		foreach($this->test_emails as $n => $email) {
			if(!isset($cmp[$n]))	continue;
			$this->assertEquals($email->get_header('from'), $cmp[$n], 'message number was '.$n.',');
		}
	}

	public function testGetRecipient() {
		$cmp
		= array(1	=> '<list@example.com>',
			2	=> '<list@example.com>',
			3	=> '<list@example.com>',
			4	=> '<list@openmailadmin.org>',
			82	=> '<wmark.freshmeat.net@hurrikane.de>',
			83	=> 'Karl Reichert <reichert@reichert.de>',
		);

		foreach($this->test_emails as $n => $email) {
			if(!isset($cmp[$n]))	continue;
			$this->assertEquals($email->get_header('_recipient'), $cmp[$n], 'message number was '.$n.',');
		}
	}

	public function testGetSubject() {
		$cmp
		= array(1	=> 'Maus unter X',
			2	=> 'Re: Maus unter X',
			3	=> 'Welcome to Openmaillist!',
			4	=> 'Nachricht mit Anhang',
			82	=> '[fmII] Openmailadmin 0.8.2 released (Default branch)',
			83	=> 'Re: openmailadmin',
		);

		foreach($this->test_emails as $n => $email) {
			if(!isset($cmp[$n]))	continue;
			$this->assertEquals($email->get_header('subject'), $cmp[$n], 'message number was '.$n.',');
		}
	}

	public function testSetValidAttachmentStorage() {
		$this->assertFalse($this->test_emails[4]->set_attachment_storage('/cannot/be/reached'));
		$this->assertTrue($this->test_emails[4]->set_attachment_storage('/tmp'));
	}

	public function testHasAttachments() {
		$this->test_emails[4]->set_attachment_storage('/tmp');
		$this->test_emails[1]->set_attachment_storage('/tmp');
		$this->assertTrue($this->test_emails[4]->has_attachments());
		$this->assertFalse($this->test_emails[1]->has_attachments());
	}

	public function testGetFirstDisplayablePart() {
		$this->test_emails[4]->set_attachment_storage('/tmp');
		$cmp
		= array(1	=> '/^Hi.*"4 5"$/s',
			2	=> '/^Hi.*EndSection$/s',
			3	=> '/^Welcome.*\.php\.$/s',
			4	=> '/^Hallo.*Mark$/s',
			82	=> '/^This email.*freshmeat.net/s',	// no $
			83	=> '/^Hallo.*>/s',
		);

		foreach($this->test_emails as $n => $email) {
			if(!isset($cmp[$n]))	continue;
			$this->assertRegExp($cmp[$n], $email->get_first_displayable_part(), 'message number was '.$n.',');
		}
	}

	public function testDetectAdministrativeEmails() {
		foreach($this->test_emails as $n => $email) {
			if($n == 100 || $n == 101 || $n == 102 || $n == 150) {
				$this->assertTrue($email->is_administrative(), 'message number was '.$n.',');
			} else {
				$this->assertFalse($email->is_administrative(), 'message number was '.$n.',');
			}
		}
	}

	public function testDetectDispositionNotifications() {
		foreach($this->test_emails as $n => $email) {
			if($n == 150) {
				$this->assertTrue($email->is_disposition_notification(), 'message number was '.$n.',');
			} else {
				$this->assertFalse($email->is_disposition_notification(), 'message number was '.$n.',');
			}
		}
	}

	public function testGet_AttachmentsIsArray() {
		$arr	= $this->test_emails[4]->get_attachments();
		$this->assertTrue(is_array($arr));
	}

	public function testGet_AttachmentsArrayContainsNumRequested() {
		$arr	= $this->test_emails[4]->get_attachments();
		$this->assertTrue(count($arr) == 2);
	}

	public function testGet_AttachmentsIsFilledArray() {
		$arr	= $this->test_emails[4]->get_attachments();
		$this->assertTrue(isset($arr['dsgesamt.pdf']));
		$this->assertTrue(isset($arr['dsgesamt2.pdf']));
	}

	public function testGet_Attachments() {
		$arr	= $this->test_emails[4]->get_attachments();
		$this->assertEquals(strlen($arr['dsgesamt.pdf']), 528);
		$this->assertEquals(strlen($arr['dsgesamt2.pdf']), 528);
		foreach($arr as $content) {
			$this->assertRegExp('/\<\!DOCTYPE.+\<html\>.+55 \(Unix\) mod_ssl\/2\.0\.55.+\<\/html\>/s', $content);
		}
	}

	public function testRetrieveAttachments() {
		mkdir('/tmp/mytest', 0777);
		$this->test_emails[4]->set_attachment_storage('/tmp/mytest');
		$this->assertTrue($this->test_emails[4]->write_attachments_to_disk(), 'could not write attachments to disk');

		$this->assertTrue(is_file('/tmp/mytest/dsgesamt.pdf'));
		$this->assertEquals(filesize('/tmp/mytest/dsgesamt.pdf'), 528);
		unlink('/tmp/mytest/dsgesamt.pdf');

		$this->assertTrue(is_file('/tmp/mytest/dsgesamt2.pdf'));
		$this->assertEquals(filesize('/tmp/mytest/dsgesamt2.pdf'), 528);
		unlink('/tmp/mytest/dsgesamt2.pdf');

		rmdir('/tmp/mytest');
	}

}

?>
