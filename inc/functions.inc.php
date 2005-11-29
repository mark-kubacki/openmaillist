<?php
/**
 * The equivalent of "rm -rf" from the *nix shell.
 * Even removes any hidden files.
 *
 * @author		stefano at takys dot it
 * @source		http://de3.php.net/manual/de/function.rmdir.php; 24-Nov-2005
 * @license		public domain
 *
 * @param	$dir	Absolute path to the directory to be deleted.
 * @return		Nothing. Don't rely on it.
 */
function rm_r($dir) {
	if(!$dh = @opendir($dir))
		return;
	while (($obj = readdir($dh))) {
		if($obj=='.' || $obj=='..')
			continue;
		if (!@unlink($dir.'/'.$obj))
			rm($dir.'/'.$obj);
	}
	@rmdir($dir);
}
?>