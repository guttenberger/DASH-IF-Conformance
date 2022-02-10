<?php
  include_once 'LowLatency_Initialization.php';

  class moduleDASHIF_LL extends moduleInterface {
    function __construct() {
      parent::__construct();
      $this->name = "DASH-IF Low Latency";

      ///\warn Remove global here
      global $low_latency_dashif_conformance;
      if ($low_latency_dashif_conformance){
        $this->enabled = true;
      }
    }

    /**
     *  \brief Checks whether 'DASH_LL_IOP' is found in the arguments, and enables this module accordingly
     */
    public function conditionalEnable($args){
      $this->enabled = false;
      foreach ($args as $arg){
        if ($arg == "DASH_LL_IOP"){
          $this->enabled = true;
        }
      }
    }

    public function hookMPD(){
      parent::hookMPD();

      global $session_dir, $mpd_xml_report;
      
      $this->validateProfiles();
      $this->validateServiceDescription();
      $this->validateUTCTiming();
      $this->validateLeapSecondInformation();

      
      $mpd_xml = simplexml_load_file($session_dir . '/' . $mpd_xml_report);
      $mpd_xml->dashif_ll = 'true';//NOTE this will be deprecated anyway
      $mpd_xml->asXml($session_dir . '/' . $mpd_xml_report);

      return 'true';
    }

    public function hookAdaptationSet(){
      parent::hookAdaptationSet();
      return low_latency_validate_cross();
    }


    private function validateProfiles() {include 'impl/validateProfiles.php';}
    private function validateServiceDescription() {include 'impl/validateServiceDescription.php';}
    private function validateUTCTiming(){include 'impl/validateUTCTiming.php';}
    private function validateLeapSecondInformation(){include 'impl/validateLeapSecondInformation.php';}
  } 

  $modules[] = new moduleDASHIF_LL();
?>
