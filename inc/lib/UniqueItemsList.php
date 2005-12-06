<?php
/**
 * Assigns one object to another.
 */
abstract class UniqueItemsList
{
	private	$content	= array();

	public function add(UniqueItem $Item) {
		$key = $Item->get_unique_value();
		$this->content[$key]	= $Item;
	}

	/**
	 * returns	UniqueItem	the deleted item
	 */
	public function del($UniqueValue) {
		if(isset($this->content[$UniqueValue])) {
			$tmp = $this->content[$UniqueValue];
			unset($this->content[$UniqueValue]);
			return $tmp;
		} else {
			throw new Exception('Item does not exist.');
		}
	}

}
?>