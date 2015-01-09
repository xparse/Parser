<?

  namespace Xparse\Parser\Test;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 1/9/15
   */
  class PageTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidEffectedUrl() {
      $page = new \Xparse\Parser\Page();
      $page->setEffectedUrl(null);
    }

  }