<?php
/**
 * We'd like to move all the getters and setters to one place
 * in order to store shared code within one place.
 */
abstract class DataCarrier
{
	private		$data	= array();

	protected function getter($key) {
		if(isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			throw new Exception(self.' does not contain value for "'.$key.'".');
		}
	}

	protected function setter($key, $value) {
		$this->data[$key] = $value;
	}

}
?>