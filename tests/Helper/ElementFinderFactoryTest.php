<?php

  namespace Test\Xparse\Parser\Helper;


  use GuzzleHttp\Psr7\Response;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\Parser\Helper\ElementFinderFactory;

  class ElementFinderFactoryTest extends \PHPUnit_Framework_TestCase
  {

    public function testHtmlWithoutCharset()
    {
      $response = new Response(
        200,
        [],
        "<html></html>"
      );

      $page = ElementFinderFactory::create($response);

      $this->assertInstanceOf(ElementFinder::class, $page);
    }

    public function testNotSupportedCharset()
    {
      $response = new Response(
        200,
        [],
        iconv('UTF-8', "WINDOWS-1251", '
          <html>
            <head>
              <meta http-equiv="Content-Type" content="text/html; charset=test-asdfd-as225" />
            </head>
            <body>Текст текст text</body>
          <html>')
      );

      $page = ElementFinderFactory::create($response);
      $pageBodyText = $page->html('//body')->getFirst();
      $this->assertInstanceOf(ElementFinder::class, $page);
      $this->assertEquals('text', trim($pageBodyText));
    }

    public function testSupportedCharsets()
    {
      $bodyText = 'Текст текст text';
      $responses = [
        new Response(
          200,
          [],
          iconv('UTF-8', "WINDOWS-1251", '
            <html>
              <head>
                <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
              </head>
              <body>' . $bodyText . '</body>
            <html>')
        ),
        new Response(
          200,
          [],
          iconv('UTF-8', "iso-8859-5", '
            <html>
              <head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-5" />
              </head>
              <body>' . $bodyText . '</body>
            <html>')
        ),
        new Response(
          200,
          [],
          iconv('UTF-8', "CP932", '
            <html>
              <head>
                <meta http-equiv="Content-Type" content="text/html; charset=CP932" />
              </head>
              <body>' . $bodyText . '</body>
            <html>')
        ),
        new Response(
          200,
          [],
          '<html>
            <head>
              <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            </head>
            <body>' . $bodyText . '</body>
          <html>'
        ),
        new Response(
          200,
          [],
          '<html>
            <head>
              <meta http-equiv="Content-Type" content="text/html; charset=utf8" />
            </head>
            <body>' . $bodyText . '</body>
          <html>'
        )
      ];

      foreach ($responses as $response) {
        $page = ElementFinderFactory::create($response);
        $pageBodyText = $page->html('//body')->getFirst();

        $this->assertInstanceOf(ElementFinder::class, $page);
        $this->assertEquals($bodyText, $pageBodyText);
      }
    }

    public function testDifferentCharsetStyles()
    {
      $bodyText = 'Текст текст text';
      $responses = [
        new Response(
          200,
          [],
          iconv('UTF-8', "WINDOWS-1251", '
            <html>
              <head>
                <meta charset="windows-1251">
              </head>
              <body>' . $bodyText . '</body>
            <html>')
        ),
        new Response(
          200,
          [],
          iconv('UTF-8', "iso-8859-5", '
            <html>
              <head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-5" />
              </head>
              <body>' . $bodyText . '</body>
            <html>')
        ),
        new Response(
          200,
          ['content-type' => 'text/html; charset=utf-8'],
          '<html>
            <head>
              <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-5" />
            </head>
            <body>' . $bodyText . '</body>
          <html>'
        ),
        new Response(
          200,
          ['content-type' => 'text/html; charset=windows-1251'],
          iconv('UTF-8', "WINDOWS-1251", '
            <html>
              <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
              </head>
              <body>' . $bodyText . '</body>
            <html>')
        )
      ];

      foreach ($responses as $response) {
        $page = ElementFinderFactory::create($response);
        $pageBodyText = $page->html('//body')->getFirst();

        $this->assertInstanceOf(ElementFinder::class, $page);
        $this->assertEquals($bodyText, $pageBodyText);
      }
    }
  }
