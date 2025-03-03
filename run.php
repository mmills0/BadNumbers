<?php
require_once 'BadNumbers.php';

if ( ! file_exists( 'original.csv' ) ) {
	echo nl2br( "Missing CSV file \n Add the CSV file to be cleaned to this directory and name it 'original.csv' \n Then re-run this script" );
} else {
	$badNums = new BadNumbers( 'original.csv', 6 );
	$cleaned = $badNums->clean();
	echo nl2br( $cleaned );
}
