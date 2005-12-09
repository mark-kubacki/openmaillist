<?php
/**
 * A collection of common implementations.
 */
abstract class OMLObject
	extends DataCarrier
	implements ErrorHandler, DatabaseAccessor
{
	protected $db;
	protected $table;
	protected $factory;

	private $error		= array();
	private $info		= array();

	function __construct(ADOConnection $database_handler, oml_factory $factory = null, $preferred_tablename = null) {
		$this->db	= $database_handler;
		$this->table	= $preferred_tablename;
		$this->factory	= $factory;
	}

	/**
	 * Sets errors and infos to 'none' resp. 'false' as if nothing happened.
	 */
	public function status_reset() {
		$this->error	= array();
		$this->info	= array();
	}

	/**
	 * @return	true or false
	 */
	public function error_occured() {
		return (count($this->error) > 0);
	}
	/**
	 * @return	string with error message(s)
	 */
	public function errors_get() {
		$tmp	= implode(' ', $this->error);
		return $tmp;
	}

	/**
	 * @return	true or false
	 */
	public function info_occured() {
		return (count($this->error) > 0);
	}
	/**
	 * @return	string with error information
	 */
	public function infos_get() {
		$tmp	= implode(' ', $this->info);
		return $tmp;
	}

	/**
	 * To hide the internal structure of error message handling.
	 *
	 * Does not check whether that error already happened.
	 */
	protected function add_error($message) {
		$this->error[]	= $message;
	}
	/**
	 * To hide the internal structure of info message handling.
	 *
	 * Does not check whether that info already happened.
	 */
	protected function add_info($message) {
		$this->info[]	= $message;
	}

}

?>