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

	/**
	 * @throw		If no value for $key has yet been set.
	 */
	protected function getter($key) {
		if(isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			throw new Exception('Container does not contain value for "'.$key.'".');
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

	/**
	 * Use this to avoid calling setter several times.
	 */
	public function become(array $data) {
		$this->data	= $data;
	}

	/**
	 * @return	Array with all keys and their values.
	 */
	protected function confess() {
		return $this->data;
	}

}
?>