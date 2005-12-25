<?php
/**
 * We'd like to move all the getters and setters to one place
 * in order to store shared code within one place.
 */
abstract class DataCarrier
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
		return true;
	}

	public function become($data) {
		$this->data	= $data;
	}

	protected function confess() {
		return $this->data;
	}

}
?>