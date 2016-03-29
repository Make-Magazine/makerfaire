<?php
/**
 * Maker Model represents the Maker Entity including all methods and properties 
 * relevant to handling data management and profile.
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
  public function get_email($firstName,$lastName,$bio,$email,$phone,$twitter,$form_id,$guid,$photo,$website) {
      return $this->maker_email;
    }
    
  public function save_maker_profile( $entry_id,$firstName,$lastName,$bio,$email,$phone,$twitter,$form_id,$maker_id,$photo,$website,$type)
  {
    global $wpdb;

    $results = $wpdb->get_results($wpdb->prepare( 
      "
        SELECT maker_id FROM wp_mf_maker WHERE email=%s
      ", 
      $email 
    ) );
    if ($wpdb->num_rows != 0)
    {
      $maker_id = $results[0]->maker_id;
    }
    else
    {
      $maker_id     = createGUID($entry_id .'-'.$type);
    }
    $wp_mf_makersql = $wpdb->prepare( "INSERT INTO wp_mf_maker(lead_id, `First Name`, `Last Name`, `Bio`, `Email`, `phone`, "
      . " `TWITTER`,  `form_id`, `maker_id`, `Photo`, `website`) "
      . " VALUES (".$entry_id.", '".$firstName."','".$lastName."','".$bio."','".$email."', '".$phone."',"
      . " '".$twitter."', ".$form_id.",'".$maker_id."','".$photo."','".$website."')"
      . " ON DUPLICATE KEY UPDATE `First Name` = '".$firstName."', "
      . "`Last Name`= '".$firstName."', "
      . "`Bio`= '".$bio."', "
      . "`lead_id`= '".$entry_id."', "
      . "`Email`= '".$email."', "
      . "`phone`= '".$phone."', "
      . "`TWITTER`= '".$twitter."', "
      . "`form_id`= '".$form_id."', "
      . "`Photo`= '".$photo."', "
      . "`website`= '".$website."' "
      );
    $wpdb->get_results($wp_mf_makersql);
  }
}
