<?

  namespace Tests\Xparse\Helper;

  use Xparse\ElementFinder\ElementFinder;
  use Xparse\Parser\Helper\LinkConverter;

  /**
   *
   * @package Tests\Xparse\Helper
   */
  class LinkConverterTest extends \PHPUnit_Framework_TestCase {

    /**
     * @return array
     */
    public function getConvertRelativeUrlToAbsolute() {
      return array(
        array(
          'html' => '<a href="/tt">a</a>',
          'expect' => '<a href="http://funivan.com/tt">a</a>',
          'url' => 'http://funivan.com/userName',
        ),
        array(
          'html' => '<a href="javascript:custom()">a</a>',
          'expect' => '<a href="javascript:custom()">a</a>',
          'url' => 'http://funivan.com/userName',
        ),

        array(
          'html' => '<img src="/hello.jpg"/>',
          'expect' => '<img src="http://funivan.com/hello.jpg"/>',
          'url' => 'http://funivan.com/userName',
        ),

        array(
          'html' => '<img src="hello.jpg"/>',
          'expect' => '<img src="http://funivan.com/userName/hello.jpg"/>',
          'url' => 'http://funivan.com/userName/',
        ),
        array(
          'html' => '<form action="contacts.html"><a>1</a></form>',
          'expect' => '<form action="http://funivan.com/contacts.html"><a>1</a></form>',
          'url' => 'http://funivan.com/index.html',
        ),
        array(
          'html' => '<form action="contacts.html"><a>1</a1></form>',
          'expect' => '<form action="http://funivan.com/contacts.html"><a>1</a></form>',
          'url' => 'http://funivan.com/index.html?user=123',
        ),

      );

    }

    /**
     * @dataProvider getConvertRelativeUrlToAbsolute
     * @param string $html
     * @param string $url
     * @param string $expect
     */
    public function testConvertRelativeUrlToAbsolute($html, $expect, $url) {

      $page = new ElementFinder($html);

      LinkConverter::convertUrlsToAbsolute($page, $url);

      $body = $page->html('//body')->getFirst();
      $this->assertEquals($expect, $body);
    }

  }
