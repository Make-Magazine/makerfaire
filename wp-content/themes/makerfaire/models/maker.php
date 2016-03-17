<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of maker
 *
 * @author rich.haynie
 */

class maker
{
   
  /**
   * @var string
   */
  var $maker_email;
  
  private $_settings;
  /**
   * @var string
   */
  private $_displayname;
  /**
   * @var array
   */
  
  private $_initialized = false;
  
    /**
   * @param string $maker_email
   * @param array $args
   */
  function __construct( $maker_email, $args = array() ) {
    $this->maker_email = $maker_email;
    /**
     * Copy properties in from $args, if they exist.
     */
    foreach( $args as $property => $value ) {
      if ( property_exists( $this, $property ) ) {
        $this->$property = $value;
      } else if ( property_exists( $this, $property = "form_{$property}" ) ) {
        $this->$property = $value;
      }
    }
  }

   /**
   * @return string
   */
  public function get_email() {
      return $this->maker_email;
    }
}
