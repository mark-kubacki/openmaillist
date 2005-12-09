<?php
/**
 * We'd like to move all the getters and setters to one place
 * in order to store shared code within one place.
 */
abstract class DataCarrier
	implements UniqueItem
{
	private		$data	= array();

	protected function has($key) {
		return isset($this->data[$key]);
	}

	protected function getter($key) {
		if(isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			throw new Exception(self.' does not contain value for "'.$key.'".');
		}
	}

	protected function setter($key, $value) {
		if(is_null($value)) {
			if(isset($this->data[$key])) {
				unset($this->data[$key]);
			}
		} else {
			$this->data[$key] = $value;
		}
	}

	public function become($data) {
		$this->data	= $data;
	}

	// due to UniqueItem
	public function get_unique_key() {
		return $this->unique_key;
	}
	public function get_unique_value() {
		return $this->getter($this->get_unique_key());
	}

}
?>