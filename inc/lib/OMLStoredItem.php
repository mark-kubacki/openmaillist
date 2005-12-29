<?php
/**
 * Any object which is to be stored in DB will be descendant of this class.
 */
abstract class OMLStoredItem
	extends DataCarrier
	implements DatabaseStorer, UniqueItem
{
	protected $db;
	protected $table;

	function __construct(ADOConnection $database_handler, $preferred_tablename) {
		$this->db	= $database_handler;
		$this->table	= $preferred_tablename;
	}

	public function write_to_db() {
		if(!isset($this->{$this->unique_key})) {
			$result = $this->db->AutoExecute($this->table, $this->confess(), 'INSERT');
			if($result) {
				$this->{$this->get_unique_key()}	= $this->db->Insert_ID();
				return true;
			}
			return false;
		} else {
			$result = $this->db->AutoExecute($this->table, $this->confess(), 'UPDATE', $this->get_unique_key().'='.$this->get_unique_value(), false);
			return $result;
		}
	}

	/**
	 * @return		true on success
	 */
	public function remove_from_db() {
		if(isset($this->{$this->unique_key})) {
			$this->db->Execute('DELETE FROM '.$this->table.' WHERE '.$this->get_unique_key().'='.$this->get_unique_value());
			return ($this->db->Affected_Rows() > 0);
		}
		return true;
	}

	public function get_unique_key() {
		return $this->unique_key;
	}

	public function get_unique_value() {
		if(!isset($this->{$this->unique_key})) {
			$this->write_to_db();
		}
		return $this->{$this->get_unique_key()};
	}

	/**
	 * Execute if you want this object be filled with contents from an already existing entry.
	 * Changes made prior to it's call will be discarded, so don't forget serialization.
	 *
	 * @return	Boolean, true if unique_key was found an we acquired data successfully.
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