<?php
  namespace Xparse\Parser;

  /**
   *
   * @package Xparse\Parser
   */
  class Page extends \Xparse\ElementFinder\ElementFinder {

    protected $effectedUrl = null;

    /**
     * @return null
     */
    public function getEffectedUrl() {
      return $this->effectedUrl;
    }

    /**
     * @param null $effectedUrl
     * @return $this
     */
    public function setEffectedUrl($effectedUrl) {
      $this->effectedUrl = $effectedUrl;
      return $this;
    }


    /**
     * @param $xpath
     * @param array $data
     */
    public function submitForm($xpath, $data = []) {
      
    }

  }
