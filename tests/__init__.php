<?php
require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/Framework/TestCase.php';
require_once 'PHPUnit2/Framework/IncompleteTestError.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once './tests/oml_emailTest.php';

class OML_Test_Suite
{
	public static function main() {
		PHPUnit2_TextUI_TestRunner::run(self::suite());
	}

	public static function suite() {
		$suite = new PHPUnit2_Framework_TestSuite();

		$suite->addTestSuite('oml_emailTest');

		return $suite;
	}

}

if(PHPUnit2_MAIN_METHOD == 'OML_Test_Suite::main') {
	OML_Test_Suite::main();
}

?>
