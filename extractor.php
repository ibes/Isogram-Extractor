<?php

include 'simple_html_dom.php';


/**
 * This script will extract Words of a word list, than check if the words are isograms (have each letter just once in the word)
 */

 class Isogram {

  public $alphabet = array( "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z" );

  public $base_url = "http://wortsuche.com/by-length/10";

  public $extracted_words = array();

  public $isograms = array();

  public function __construct() {

    $this->extract_words();
    $this->find_isograms();

  }


  public function extract_words() {

    foreach ( $this->alphabet as $char ) {

      $html = new simple_html_dom();

      $html->load_file( 'http://wortsuche.com/by-length/10' . $char );

      foreach( $html->find('#centerbox-content li a') as $element )
        $this->extracted_words[] = $element->plaintext;

    }

  }


  public function find_isograms() {

    foreach ( $this->extracted_words as $word ) {

      if ( $this->is_isogram( $word ) ) {
        $this->isograms[] = $word;
      }

    }

  }


  public function list_isograms() {

    return $this->isograms;

  }

  public function write_to_file( $filename, $format = 'json' ) {

    if ( $format == 'json' || empty( $format ) ) {

      $fp = fopen( 'export/' . $filename, 'w' );
      fwrite( $fp, json_encode( $this->isograms ) );
      fclose( $fp );

    } elseif ( $format == 'csv' ) {

      $fp = fopen( 'export/' . $filename, 'w' );
      fputcsv( $fp, $this->isograms );
      fclose( $fp );

    } elseif ( $format == 'plaintext' ) {

      $fp = fopen( 'export/' . $filename, 'w' );
      fwrite( $fp, implode( "\n", $this->isograms ) );
      fclose( $fp );

    } else {

      // error handling ?!

    }

  }


  private function is_isogram( $string ) {

    $string = str_replace( ['-', ''], '', mb_strtolower( $string, 'UTF-8' ) );
    $letters = preg_split( '//u', $string, -1, PREG_SPLIT_NO_EMPTY );
    return count( $letters ) === count( array_unique( $letters ) );

  }

 }

 $isogram = new Isogram();
 $isograms = $isogram->write_to_file( 'isograms.json', 'json' );
 $isograms = $isogram->write_to_file( 'isograms.csv', 'csv' );
 $isograms = $isogram->write_to_file( 'isograms.txt', 'plaintext' );

