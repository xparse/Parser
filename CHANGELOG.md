# Changelog
All Notable changes to `Parser` will be documented in this file

## Planned changes
- ElementFinderFactory now accept use `Xparse\ElementFinder\ExpressionTranslator\ExpressionTranslatorInterface`
- Updated minimum required php version to 8.1
- Add `RecursiveParser` (merged from the [xparse/recursive-pagination](https://github.com/xparse/RecursivePagination) repository)
- Updated minimum required version for guzzlehttp/psr7 to 2.4.5

## 1.0.2 [2023-12-5]
- Minimum supported php version is now php 8.0

## 1.0.1 [2021-06-01]
- Return support of guzzlehttp/guzzle ^6.3

## 1.0.0 [2021-05-31]
- No breaking changes.

## 0.5.0-alpha [2021-04-29]
- Move to php 7.3
- Add php 8.0 support

## 0.4.1 [2019-05-14]
- #41 accept LinkConverter now accept ElementFinderInterface
- Update xparse/element-finder library

## 0.4.0 [2019-05-11]
### Changed
- BC. Upgrade to php 7.1
- BC. `ParserInterface` now returns `ElementFinderInterface`
- BC. `ElementFinderFactoryInterface` now returns `ElementFinderInterface`
- Upgrade `xparse/element-finder` library 

## 0.3.0 [2018-04-18]
### Changed
- BC. Make `LinkConverter` immutable. Now `LinkConverterInterface` return new `ElementFinder` Instance 
- Upgrade `xparse/element-finder` library 

## 0.2.4 [2018-01-28]

### Changed
- Upgrade `xparse/element-finder` library 


## 0.2.3 [2017-11-06]

### Changed
- Upgrade `xparse/element-finder` library 

## 0.2.2 [2017-09-07]

### Fixed
- Read content from the start while creating ElementFinder

## 0.2.1 [2017-09-06]

### Fixed
- fix bug in constructor with assigning class fields

## 0.2.0 [2017-09-05]

### Changed
- BC `Parser::post` signature was changed, `$body` parameter was dropped
- BC `Parser::setLastPage` method access level changed to private
- Upgraded to phpunit 6.3
- Parameters sequence was changed in `ElementFinderFactory::__construct`
- #30 Upgrade `guzzlehttp/guzzle` to 6.3

### Added
- strict type declaration
- `EncodingConverterInterface`, `ToUtfConverter` 
- `LinkConverterInterface`, `RelativeToAbsoluteLinkConverter`, 

### Removed
- BC Removed `LinkConverter::convertUrlsToAbsolute`
- BC Removed `HtmlEncodingConverter::convertToUtf`
- #28 BC Removed usage of xparse/parser-interface package

## 0.1.0-alpha.4 [2016-07-12]

### Added
- #22 Use response `X-GUZZLE-EFFECTIVE-URL` header to retrieve last url.    

## 0.1.0-alpha.3 [2016-06-02]

### Added
- #21 Enable `cookies` by default
- #20 Retrieve element finder factory from parser. Use `Parser::getElementFinderFactory`

### Fixed
- #19 Second parameter of the post method is `body`. Expect `string`, `resource` or `Psr\Http\Message\StreamInterface`

## 0.1.0-alpha.2 [2016-05-25]

### Added
- #18 Pass request options to `get` and `post` methods

### Fixed
- #16 Use `ElementFinderFactoryInterface` instead of `ElementFinderFactory`
