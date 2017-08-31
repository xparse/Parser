<?php

  declare(strict_types=1);

  namespace Xparse\Parser;

  use Psr\Http\Message\ResponseInterface;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\Helper\StringHelper;
  use Xparse\ExpressionTranslator\ExpressionTranslatorInterface;
  use Xparse\ExpressionTranslator\XpathExpression;
  use Xparse\Parser\Helper\EncodingConverterInterface;
  use Xparse\Parser\Helper\HtmlEncodingConverter;
  use Xparse\Parser\Helper\LinkConverter;
  use Xparse\Parser\Helper\LinkConverterInterface;

  class ElementFinderFactory implements ElementFinderFactoryInterface {

    /**
     * @var LinkConverterInterface
     */
    private $linkConverter;

    /**
     * @var EncodingConverterInterface
     */
    private $encodingConverter;

    /**
     * @var ExpressionTranslatorInterface
     */
    private $expressionTranslator;

    /**
     * @param LinkConverterInterface|null $linkConverter
     * @param EncodingConverterInterface|null $encodingConverter
     * @param ExpressionTranslatorInterface|null $expressionTranslator
     */
    public function __construct(
      LinkConverterInterface $linkConverter = null,
      EncodingConverterInterface $encodingConverter = null,
      ExpressionTranslatorInterface $expressionTranslator = null
    ) {
      if ($linkConverter === null) {
        $this->linkConverter = new LinkConverter();
      }
      if ($encodingConverter === null) {
        $this->encodingConverter = new HtmlEncodingConverter();
      }
      $this->expressionTranslator = new XpathExpression();
    }

    /**
     * @inheritdoc
     */
    public function create(ResponseInterface $response, string $affectedUrl = '') : ElementFinder {
      $html = StringHelper::safeEncodeStr($response->getBody()->getContents());
      $html = $this->encodingConverter->convertToUtf($html, $response->getHeaderLine('content-type'));
      $elementFinder = new ElementFinder($html, null, $this->expressionTranslator);

      if ($affectedUrl !== null) {
        $this->linkConverter->relativeToAbsolute($elementFinder, $affectedUrl);
      }

      return $elementFinder;
    }

  }