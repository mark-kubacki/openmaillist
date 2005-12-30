<?php
final class openmaillist
{
	private		$db;
	private		$factory;

	function __construct(ADOConnection $database_handler, oml_factory $factory) {
		$this->db	= $database_handler;
		$this->factory	= $factory;
	}

	/**
	 * Use this for building an overview about all lists.
	 *
	 * @return		All lists associated with this instance of openmaillist as array.
	 */
	public function get_all_lists() {
		return $this->factory->get_all_lists();
	}

	/**
	 * @throw		If list does or cannot exist.
	 */
	public function get_list($list_id) {
		if(is_numeric($list_id)) {
			$list = $this->factory->get_list($list_id);
			if(!$list === false) {
				return $list;
			} else {
				throw new Exception('Given list does not exist.');
			}
		} else {
			throw new Exception('Given list_id is not valid.');
		}
	}

	/**
	 * Use this for building an overview about all threads.
	 *
	 * @return		All threads associated with that given list as array.
	 */
	public function get_all_threads(oml_list $list) {
		return $list->get_all_threads();
	}

	/**
	 * @throw		If thread does or cannot exist.
	 */
	public function get_thread($thread_id) {
		if(is_numeric($thread_id)) {
			$thread = $this->factory->get_thread($thread_id);
			if(!$thread === false) {
				return $thread;
			} else {
				throw new Exception('Given thread does not exist.');
			}
		} else {
			throw new Exception('Given thread_id is not valid.');
		}
	}

	/**
	 * This is for integrating new emails.
	 *
	 * @param	list		List the message has been addressed to.
	 * @param	input		String with the email-message.
	 * @throw			Several exceptions. You can use their text as error message.
	 */
	public function put_email(oml_list $list, oml_email $input, $upload_dir) {
		if($input->is_administrative()) {
			return;
		}

		try {
			$myMsg	= $this->factory->get_message();
			$myMsg->let(	$input->get_header('message-id'), $input->get_header('date-send'), $input->get_header('date-received'),
					$input->get_header('from'), $input->get_header('subject'),
					$input->has_attachments() ? 1 : 0,
					$input->get_first_displayable_part(true));
		} catch (Exception $e) {
			throw new Exception('Email could not be saved as it lacks important fields in header.');
		}

		if($input->has_header('in-reply-to')) {
			$myMsg->set_in_reply_to($input->get_header('in-reply-to'));
			if($input->has_header('references')) {
				$myMsg->set_referenced($input->get_header('references'));
			} else {
				$myMsg->set_referenced('<'.$input->get_header('in-reply-to').'>');
			}
		}

		if($myMsg->write_to_db()) {
			$this->put_message($list, $myMsg);
			$this->add_attachments_to_msg($myMsg, $input, $upload_dir);
		} else {
			throw new Exception('Email could not be saved. Duplicate? Inproper Message-ID?');
		}
	}

	/**
	 * @returns		Boolean whether writing and adding attachments was successfull.
	 */
	private function add_attachments_to_msg(oml_message $msg, oml_email $email, $upload_dir) {
		if($email->has_attachments()) {
			$storage_part	= '/'.md5(rand().$msg->get_unique_value());
			mkdir($upload_dir.$storage_part);
			if(!$email->set_attachment_storage($upload_dir.$storage_part)) {
				@rmdir($upload_dir.$storage_part);
				return false;
			}
			foreach($email->write_attachments_to_disk() as $filename) {
				$tmp	= $this->factory->create_attachment($filename, $storage_part.'/'.$filename);
				$msg->add_attachment($tmp);
			}
		}
		return true;
	}

	/**
	 * Ensures the message is properly registered and attempts to delete threads without messages.
	 *
	 * @throw			Several exceptions. You can use their text as error message.
	 */
	public function put_message(oml_list $receiving_list, oml_message $msg) {
		$receiving_list->register_message($msg);
		if(!$msg->write_to_db()) {
			$msg->remove_from_db();
			$this->factory->delete_empty_threads($theList->get_unique_value());
			throw new Exception('Mssage could not be written to DB after having been registered with list.');
		}
	}

}
?>