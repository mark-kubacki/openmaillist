<?php
/**
 * In order to recoginise objects and to store them better (in relational DB)
 * we have to rely on an unique identifier.
 * As plus we can use that value as has-key.
 */
interface UniqueItem
{
	function get_unique_key();
	function get_unique_value();
	function set_unique_value($value);
}
?>