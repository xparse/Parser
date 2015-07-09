<?php
  namespace Xparse\Parser;

  use GuzzleHttp\Psr7\Uri;
  use Xparse\ElementFinder\Helper;
  use Xparse\Parser\Helper\LinkConverter;
  use Xparse\ParserInterface\ParserInterface;

  /**
   *
   * @package Xparse\Parser
   */
  class Page extends \Xparse\ElementFinder\ElementFinder {

    /**
     * Last effected url
     *
     * @var null|string
     */
    protected $effectedUrl = null;

    /**
     * @var null|ParserInterface
     */
    protected $parser = null;

    /**
     * @param ParserInterface $parser
     * @return $this
     */
    public function setParser(ParserInterface $parser) {
      $this->parser = $parser;
      return $this;
    }

    /**
     * @return ParserInterface
     */
    public function getParser() {
      return $this->parser;
    }

    /**
     * @param string $effectedUrl
     * @return $this
     */
    public function setEffectedUrl($effectedUrl) {

      if (!is_string($effectedUrl) or empty($effectedUrl)) {
        throw new \InvalidArgumentException("Expect not empty string. " . gettype($effectedUrl) . ' given');
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
     *
     * @return $this
     * @throws \Exception
     */
    public function convertRelativeLinks() {

      if (empty($this->effectedUrl)) {
        throw new \Exception('Empty effected url');
      }

      LinkConverter::convertUrlsToAbsolute($this, $this->effectedUrl);


      return $this;
    }

    /**
     * @param string $xpath
     * @param array $data
     * @return \Xparse\ElementFinder\ElementFinder
     * @throws \Exception
     */
    public function submitForm($xpath, array $data = array()) {

      if (!is_string($xpath)) {
        throw new \InvalidArgumentException("Expect xpath expression string. " . gettype($xpath) . ' given');
      }

      $parser = $this->getParser();

      if (empty($parser)) {
        throw new \Exception("Empty parser object. Cant fetch page.");
      }

      $actionHref = $this->attribute($xpath . '/@href')->getFirst();
      if (empty($actionHref)) {
        throw new \Exception('Empty form action. Possible invalid xpath expression');
      }

      $action = $this->attribute($xpath . '/@method')->getFirst();
      $action = strtolower($action);

      $action = empty($action) ? 'GET' : strtoupper($action);

      if (!in_array($action, array('POST', 'GET'))) {
        throw new \Exception('Invalid form method. Expect only get or post. Instead ' . $action . ' given');
      }

      $data = array_merge(Helper::getDefaultFormData($this, $xpath), $data);


      if ($action == 'POST') {
        return $parser->post($actionHref, $data);
      }

      $uri = new Uri($actionHref);
      $uri = $uri->withQuery(implode('&', $data));
      return $parser->get($uri);
    }

    /**
     * Fetch url by xpath and get page with this url
     *
     * @param string $xpath
     * @return \Xparse\ElementFinder\ElementFinder
     * @throws \Exception
     */
    public function fetchPageByLink($xpath) {

      if (!is_string($xpath)) {
        throw new \InvalidArgumentException("Expect string. " . gettype($xpath) . ' given');
      }

      $parser = $this->getParser();

      if (empty($parser)) {
        throw new \Exception("Empty parser object. Cant fetch page.");
      }


      $href = $this->attribute($xpath)->getFirst();

      if (empty($href)) {
        throw new \Exception('Empty href link. Possible invalid xpath expression:' . $xpath);
      }

      return $parser->get($href);
    }

  }
