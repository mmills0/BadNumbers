BadNumbers is a PHP script that runs through a CSV and removes phone numbers that are invalid.

Released under the [MIT License](LICENSE).

> This script is meant to be used with U.S. phone numbers.
> By default, for example purposes, this script removes all numbers with area codes in Puerto Rico and the Dominican Republic.

Requirements
============

PHP version
---------------------------

- `PHP 8.0` and above is supported.


Usage
=====

The simplest usage of the library would to download this repository an simply run the script `run.php` after adding your own CSV named `original.csv` to the local directory:

```php
<?php
require_once 'BadNumbers.php';

if ( ! file_exists( 'original.csv' ) ) {
	echo nl2br( "Missing CSV file \n Add the CSV file to be cleaned to this directory and name it 'original.csv' \n Then re-run this script" );
} else {
	$badNums = new BadNumbers( 'original.csv', 6 );
	$cleaned = $badNums->clean();
	echo nl2br( $cleaned );
}

```

A sample file `original-sample.csv` is included with fake data for testing purposes. Rename this file to `original.csv` and run script `run.php` to see sample output.

Configuration
=====================

When running this script, the column of the CSV that contains the phone number must be specified. In the above example it is specified as column 6 (which coincides with the data in original-sample.csv).

Additionally, class `BadNumbers` includes `$badRegionArr`, which can be customized to disallow an array of area codes. By default, this array includes area codes in Puerto Rico and the Dominican Republic as a working example.

Output
=====================

The script will create up to six new CSV files. Only files that contain data will be created.

- `cleaned_good.csv` A new CSV that includes all cleaned rows that pass all tests.
- `removed_areacode.csv` A new CSV that includes all rows removed due to containing phone number with an invalid area code.
- `removed_duplicate.csv` A new CSV that includes all rows removed due to the presence of duplicates in the original CSV.
- `removed_length.csv` A new CSV that includes all rows removed due containing a phone number with an invalid length.
- `removed_missing.csv` A new CSV that includes all rows removed because the phone number is empty or missing.
- `removed_region.csv` A new CSV that includes all rows removed due to containing a phone number with an area code in a disallowed region (as specified in BadNumbers).

Notes
=====================

- The original CSV analyzed should include its header row.

