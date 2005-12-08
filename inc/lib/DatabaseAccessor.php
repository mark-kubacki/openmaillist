<?php
/**
 * If a class ever wants to execute queries it has to implement this.
 */
interface DatabaseAccessor
{
	function __construct(ADOConnection $database_handler);

}

?>