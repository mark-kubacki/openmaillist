<?php
final class openmaillist
	extends OMLObject
	implements ErrorHandler
{
	/**
	 * returns array	All lists associated with this instance of openmaillist.
	 */
	public function get_all_lists() {
		return $this->factory->get_all_lists();
	}

}
?>