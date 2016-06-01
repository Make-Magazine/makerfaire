<?php
    /**
     * Fake KLogger class.
     *
     * @package
     **/
    class KLogger {

      /**
       * Some constants relating to logging, used by GF.
       **/
      CONST EMERGENCY = 'EMERGENCY';
      CONST ALERT = 'ALERT';
      CONST CRITICAL = 'CRITICAL';
      CONST ERROR = 'ERROR';
      CONST WARNING = 'WARNING';
      CONST NOTICE = 'NOTICE';
      CONST INFO = 'INFO';
      CONST DEBUG = 'DEBUG';
    }
    /**
     * Kinda fake GFLogging class
     *
     * @package
     **/
    class GFLogging {

      /**
       * A version integer.
       *
       * @var int
       **/
      var $version;

      /**
   * @access @static
   *
   * @return null
   */
  static public function include_logger() {
    // Not much happens here
  }

  /**
   * Log to PHP error log
   *
   * @return null
   */
  static public function log_message($slug, $message, $debug_level) {
    //don't log debug messages
    //if($debug_level==KLogger::DEBUG){
    if ($debug_level == KLogger::ERROR) {
      error_log("GF LOG: $slug, $message, $debug_level");
    }
  }

}
