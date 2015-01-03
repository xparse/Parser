<?php
  namespace Xparse\Parser;

  /**
   *
   * @package Xparse\Parser
   */
  class Page extends \Xparse\ElementFinder\ElementFinder {

    /**
     * Last effectd url
     */
    protected $effectedUrl = null;

    /**
     * @return string|null
     */
    public function getEffectedUrl() {
      return $this->effectedUrl;
    }

    /**
     * @param string $effectedUrl
     * @return $this
     */
    public function setEffectedUrl($effectedUrl) {
      $this->effectedUrl = $effectedUrl;
      return $this;
    }


    /**
     * @param string $xpath
     * @param array $data
     */
    public function submitForm($xpath, $data = []) {
      
    }

  }
