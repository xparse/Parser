#Changelog
All Notable changes to `Parser` will be documented in this file
##  [Unreleased]

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
