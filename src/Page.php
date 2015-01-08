<?php
  namespace Xparse\Parser;

  /**
   *
   * @package Xparse\Parser
   */
  class Page extends \Xparse\ElementFinder\ElementFinder {

    /**
     * Last effected url
     * @var null|string
     */
    protected $effectedUrl = null;

    /**
     * @var null|\Xparse\ParserInterface\ParserInterface
     */
    protected $parser = null;

    /**
     * @param \Xparse\ParserInterface\ParserInterface $parser
     * @return $this
     */
    public function setParser(\Xparse\ParserInterface\ParserInterface $parser) {
      $this->parser = $parser;
      return $this;
    }

    /**
     * @return \Xparse\ParserInterface\ParserInterface
     */
    public function getParser() {
      return $this->parser;
    }

    /**
     * @param string $effectedUrl
     * @return $this
     */
    public function setEffectedUrl($effectedUrl) {

      if (!is_string($effectedUrl)) {
        throw new \InvalidArgumentException("Expect string. " . gettype($effectedUrl) . ' given');
      }

      $this->effectedUrl = $effectedUrl;
      return $this;
    }


    /**
     * @return string|null
     */
    public function getEffectedUrl() {
      return $this->effectedUrl;
    }


    /**
     * Convert relative links to absolute
     * This function also convert action attribute link in forms
     */
    public function convertRelativeLinks() {
      if (empty($this->effectedUrl)) {
        throw new \Exception('Empty effected url');
      }
      //@todo convert links
    }


    /**
     * @param string $xpath
     * @param array $data
     * @throws \Exception
     */
    public function submitForm($xpath, $data = []) {
      if (!is_string($xpath)) {
        throw new \InvalidArgumentException("Expect xpath expression string. " . gettype($xpath) . ' given');
      }
      if (!is_array($data)) {
        throw new \InvalidArgumentException("Expect data is array. " . gettype($data) . ' given');
      }

      $actionHref = $this->attribute($xpath . '/@href')->getFirst();
      if (empty($actionHref)) {
        throw new \Exception('Empty form action. Possible invalid xpath expression');
      }
      //@todo
    }

    /**
     * Send get request and return new page
     * @param string $xpath
     * @throws \Exception
     */
    public function getPageByLink($xpath) {
      if (!is_string($xpath)) {
        throw new \InvalidArgumentException("Expect string. " . gettype($xpath) . ' given');
      }

      $href = $this->attribute($xpath)->getFirst();
      if (empty($href)) {
        throw new \Exception('Empty href link. Possible invalid xpath expression');
      }
      //@todo get page via parser

    }

  }
