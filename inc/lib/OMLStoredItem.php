<?php
/**
 *
 */
abstract class OMLStoredItem
	extends DataCarrier
	implements DatabaseStorer, UniqueItem
{
	protected $db;
	protected $table;

	// due to DatabaseStorer
	function __construct(ADOConnection $database_handler, $preferred_tablename) {
		$this->db	= $database_handler;
		$this->table	= $preferred_tablename;
	}

	/**
	 * Writes changes (or even new data) to db.
	 * @returns boolean	true on success
	 */
	public function write_to_db() {
		if(!$this->has($this->get_unique_key())) {
			$result = $this->db->AutoExecute($this->table, $this->confess(), 'INSERT');
			if($result) {
				$this->setter($this->get_unique_key(), $this->db->Insert_ID());
				return true;
			}
			return false;
		} else {
			$result = $this->db->AutoExecute($this->table, $this->confess(), 'UPDATE', $this->get_unique_key().'='.$this->get_unique_value(), false);
			return $result;
		}
	}

	/**
	 * returns boolean	true on success
	 */
	public function remove_from_db() {
		if($this->has($this->get_unique_key())) {
			$this->db->Execute('DELETE FROM '.$this->table.' WHERE '.$this->get_unique_key().'='.$this->get_unique_value());
			return ($this->db->Affected_Rows() > 0);
		}
		return true;
	}

	// due to UniqueItem
	public function get_unique_key() {
		return $this->unique_key;
	}
	public function get_unique_value() {
		if(!$this->has($this->get_unique_key())) {
			$this->write_to_db();
		}
		return $this->getter($this->get_unique_key());
	}

	/**
	 * Execute if you want this object be filled with contents from an already existing entry.
	 * Changes made prior to it's call will be discarded, so don't forget serialization.
	 *
	 * @returns boolean	whether unique_key was found an we acquired data successfully
	 */
	public function set_unique_value($value) {
		$rs = $this->db->GetRow('SELECT * FROM '.$this->table.' WHERE '.$this->get_unique_key().'='.$value);
		if(!$rs === false) {
			$this->become($rs);
			return true;
		}
		return false;
	}


}

?>