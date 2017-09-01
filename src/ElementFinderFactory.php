<?php

  declare(strict_types=1);

  namespace Xparse\Parser;

  use Psr\Http\Message\ResponseInterface;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\Helper\StringHelper;
  use Xparse\ExpressionTranslator\ExpressionTranslatorInterface;
  use Xparse\ExpressionTranslator\XpathExpression;
  use Xparse\Parser\Helper\EncodingConverterInterface;
  use Xparse\Parser\Helper\LinkConverterInterface;
  use Xparse\Parser\Helper\RelativeToAbsoluteLinkConverter;
  use Xparse\Parser\Helper\ToUtfConverter;

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
     * @param ExpressionTranslatorInterface|null $expressionTranslator
     * @param LinkConverterInterface|null $linkConverter
     * @param EncodingConverterInterface|null $encodingConverter
     */
    public function __construct(
      ExpressionTranslatorInterface $expressionTranslator = null,
      LinkConverterInterface $linkConverter = null,
      EncodingConverterInterface $encodingConverter = null
    ) {
      if ($expressionTranslator === null) {
        $this->expressionTranslator = new XpathExpression();
      }
      if ($linkConverter === null) {
        $this->linkConverter = new RelativeToAbsoluteLinkConverter();
      }
      if ($encodingConverter === null) {
        $this->encodingConverter = new ToUtfConverter();
      }
    }

    /**
     * @inheritdoc
     */
    public function create(ResponseInterface $response, string $affectedUrl = '') : ElementFinder {
      $html = StringHelper::safeEncodeStr($response->getBody()->getContents());
      $html = $this->encodingConverter->convert($html, $response->getHeaderLine('content-type'));
      $elementFinder = new ElementFinder($html, null, $this->expressionTranslator);

      if ($affectedUrl !== null) {
        $this->linkConverter->convert($elementFinder, $affectedUrl);
      }

      return $elementFinder;
    }

  }