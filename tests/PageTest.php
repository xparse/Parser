<?php

  namespace Xparse\Parser\Test;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 1/9/15
   */
  class PageTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidEffectedUrl() {
      $page = $this->getPage();
      $page->setEffectedUrl(null);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidXpathGetPageByLink() {
      $page = $this->getPage();
      $page->setParser(new \Xparse\Parser\Parser());
      $page->fetchPageByLink(null);
    }


    /**
     * @expectedException \Exception
     */
    public function testFetchPageByUrlWithoutParser() {
      $page = $this->getPage();
      $page->fetchPageByLink('//a');
    }


    /**
     * @expectedException \Exception
     */
    public function testFetchPageByUrlWithoutValidHref() {
      $page = $this->getPage();
      $page->setParser(new \Xparse\Parser\Parser());
      $page->fetchPageByLink('//a');
    }


    /**
     * @return \Xparse\Parser\Page
     */
    protected function getPage() {
      $page = new \Xparse\Parser\Page("<html>123</html>");
      return $page;
    }

  }