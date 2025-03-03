<?php
#[\AllowDynamicProperties]
class BadNumbers {

  private $allNumbersArr = array();

  private $badAreaCodeArr = array( '000', '111', '222', '333', '444', '555', '666', '777', '999' );

  // Example: Don't allow numbers from area codes in Puerto Rico and the Domincan Republic.
  private $badRegionArr = array( '787', '809', '829', '849', '939' );

  function __construct( $csv, $col = 0 ) {
    $this->csv = $csv;
    $this->col = $col;
  }

  public function clean() {
    $reason_missing  = array();
    $reason_areacode = array();
    $reason_dupe     = array();
    $reason_length   = array();
    $reason_region   = array();
    $good_numbers    = array();
    $csv_data   = array_map( 'str_getcsv', file( $this->csv ) );
    $csv_header = $csv_data[0] ?? array();
    $total_all  = 0;
    $total_bad  = 0;
    unset( $csv_data[0] );
    foreach( $csv_data as $row ) {
      $total_all++;
      $phone = trim( $row[ $this->col ] ) ?? '';
      if ( empty( $phone ) ) {
        $reason_missing[] = $row;
        $total_bad++;
      } else {
        $reason = $this->reason( $phone );
        if ( 'pass' !== $reason ) {
          $total_bad++;
          if ( 'areacode' == $reason ) {
            $reason_areacode[] = $row;
          }
          if ( 'dupe' == $reason ) {
            $reason_dupe[] = $row;
          }
          if ( 'length' == $reason ) {
            $reason_length[] = $row;
          }
          if ( 'region' == $reason ) {
            $reason_region[] = $row;
          }
        } else {
          $good_numbers[] = $row;
        }
      }
    }
    if ( ! empty( $good_numbers ) ) {
      $this->createCSV( $good_numbers, $csv_header, 'cleaned_good.csv' );
    }
    if ( ! empty( $reason_areacode ) ) {
      $this->createCSV( $reason_areacode, $csv_header, 'removed_areacode.csv' );
    }
    if ( ! empty( $reason_dupe ) ) {
     $this->createCSV( $reason_dupe, $csv_header, 'removed_duplicate.csv' );
    }
    if ( ! empty( $reason_length ) ) {
      $this->createCSV( $reason_length, $csv_header, 'removed_length.csv' );
    }
    if ( ! empty( $reason_missing ) ) {
      $this->createCSV( $reason_missing, $csv_header, 'removed_missing.csv' );
    }
    if ( ! empty( $reason_region ) ) {
      $this->createCSV( $reason_region, $csv_header, 'removed_region.csv' );
    }
    $message = 'Total CSV rows checked = ' . $total_all . PHP_EOL . 'Total CSV rows removed = ' . $total_bad . PHP_EOL;
    $message .= 'Used column ' . $this->col . ' (' . $csv_header[ $this->col ] . ') to check phone numbers.';
    return $message;
  }

  protected function reason( $number ) {
    $this->num = $number;
    $reason = 'pass';
    if ( ! $this->checkDupe() ) {
      $reason = 'dupe';
    }
    if ( ! $this->checkAreaCode() ) {
      $reason = 'areacode';
    }
    if ( ! $this->checkLength() ) {
      $reason = 'length';
    }
    if ( ! $this->checkRegion() ) {
      $reason = 'region';
    }
    return $reason;
  }

  protected function checkDupe() {
    if ( array_key_exists( $this->cleaned(), $this->allNumbersArr ) ) {
      return false;
    } else {
      $this->allNumbersArr[ $this->cleaned() ] = 1;
      return true;
    }
  }

  protected function checkAreaCode() {
    return ( in_array( $this->getAreaCode(), $this->badAreaCodeArr, true ) ) ? false : true;
  }

  protected function checkLength() {
    return ( 10 === strlen( $this->cleaned() ) ) ? true : false;
  }

  protected function checkRegion() {
    return ( in_array( $this->getAreaCode(), $this->badRegionArr, true ) ) ? false : true;
  }

  protected function getAreaCode() {
    return substr( $this->cleaned(), 0, 3 );
  }

  protected function cleaned() {
    $clean = (string) preg_replace( '/[^0-9]/', '', $this->num );
    $char1 = substr( $clean, 0, 1 );
    return ( '1' === $char1 ) ? substr( $clean, 1 ) : $clean;
  }

  protected function createCSV( $arr, $header, $filename ) {
    array_unshift( $arr, $header );
    $fp = fopen( $filename, 'w');
    foreach ( $arr as $fields ) {
      fputcsv( $fp, array_values( $fields ) );
    }
    fclose($fp);
  }

}
