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

  /**
   *
   */
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
        $expressionTranslator = new XpathExpression();
      }
      if ($linkConverter === null) {
        $linkConverter = new RelativeToAbsoluteLinkConverter();
      }
      if ($encodingConverter === null) {
        $encodingConverter = new ToUtfConverter();
      }
      $this->expressionTranslator = $expressionTranslator;
      $this->linkConverter = $linkConverter;
      $this->encodingConverter = $encodingConverter;
    }


    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function create(ResponseInterface $response, string $affectedUrl = ''): ElementFinder {
      $html = StringHelper::safeEncodeStr((string) $response->getBody());
      $html = $this->encodingConverter->convert($html, $response->getHeaderLine('content-type'));
      $elementFinder = new ElementFinder($html, null, $this->expressionTranslator);
      if ($affectedUrl !== '') {
        $elementFinder = $this->linkConverter->convert($elementFinder, $affectedUrl);
      }
      return $elementFinder;
    }

  }