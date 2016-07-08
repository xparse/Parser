# Parser

[![GitHub tag](https://img.shields.io/github/tag/xparse/Parser.svg?style=flat-square)](https://github.com/xparse/Parser/tags)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/xparse/Parser/master.svg?style=flat-square)](https://travis-ci.org/xparse/Parser)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/xparse/Parser.svg?style=flat-square)](https://scrutinizer-ci.com/g/xparse/Parser/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/xparse/Parser.svg?style=flat-square)](https://scrutinizer-ci.com/g/xparse/Parser)
[![Total Downloads](https://img.shields.io/packagist/dt/xparse/parser.svg?style=flat-square)](https://packagist.org/packages/xparse/parser)

Parser client

## Install

Via Composer

``` bash
$ composer require xparse/parser
```

## Usage

``` php
  $parser = new \Xparse\Parser\Parser();
  $title = $parser->get('http://funivan.com')->content('//*[@class="entry-title"]/a');
  print_r($title);
```
## Using with custom Middleware
If you are using custom Guzzle Middleware and it doesn't send real requests, in order to get last effective url you need to set it to response `X-GUZZLE-EFFECTIVE-URL` header manually.
 
Here is an example of `__invoke()` method in your custom Middleware

``` php
  public function __invoke(callable $handler) : \Closure {
    return function (RequestInterface $request, array $options) use ($handler) {

      # some code

      return $handler($request, $options)->then(function (ResponseInterface $response) use ($request) {

        $response = $response->withHeader('X-GUZZLE-EFFECTIVE-URL', $request->getUri());
        
        # some code

        return $response;
      });
    };
  }
```

## Testing

``` bash
    ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/xparse/Parser/blob/master/CONTRIBUTING.md) for details.

## Credits

- [funivan](https://github.com/funivan)
- [All Contributors](https://github.com/xparse/Parser/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
