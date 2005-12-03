<?php
/**
 * Provides some primitive functions for error handling each class has to implement.
 * You are free to still use exceptions, where applicable, for critical/blocking errors.
 */
interface ErrorHandler
{
	/**
	 * Sets errors and infos to 'none' resp. 'false' as if nothing happened.
	 */
	public function status_reset();

	/**
	 * @return	true or false
	 */
	public function error_occured();
	/**
	 * @return	string with error message(s)
	 */
	public function errors_get();

	/**
	 * @return	true or false
	 */
	public function info_occured();
	/**
	 * @return	string with error information
	 */
	public function infos_get();

}

?>