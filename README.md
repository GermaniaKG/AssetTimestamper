# Germania KG · AssetTimestamper

**File modification timestamps for your website assets.**

[![Packagist](https://img.shields.io/packagist/v/germania-kg/asset-timestamper.svg?style=flat)](https://packagist.org/packages/germania-kg/asset-timestamper)
[![PHP version](https://img.shields.io/packagist/php-v/germania-kg/asset-timestamper.svg)](https://packagist.org/packages/germania-kg/asset-timestamper)
[![Build Status](https://img.shields.io/travis/GermaniaKG/AssetTimestamper.svg?label=Travis%20CI)](https://travis-ci.org/GermaniaKG/AssetTimestamper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GermaniaKG/AssetTimestamper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/AssetTimestamper/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/GermaniaKG/AssetTimestamper/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/AssetTimestamper/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/GermaniaKG/AssetTimestamper/badges/build.png?b=master)](https://scrutinizer-ci.com/g/GermaniaKG/AssetTimestamper/build-status/master)




This Callable looks for a given asset file (e.g. CSS, JS usually) in a base directory,
extracts its [modification time](http://php.net/manual/en/function.filemtime.php)
and returns a modified asset path that contains that very timestamp.

**For remote files,** no timestamps will be added. AssetTimestamper will check for *host* entry in PHP's *parse_url's* result array.

See Chris Coyier's article [“Strategies for Cache-Busting CSS”](https://css-tricks.com/strategies-for-cache-busting-css), chapter [“Changing File Name“](https://css-tricks.com/strategies-for-cache-busting-css/#article-header-id-2). Please note — As Chris Coyier [points out](https://css-tricks.com/strategies-for-cache-busting-css/#article-header-id-3), this technique may slow down server response. Using this technique for *only some files* should be fine, be aware to not over-use it.


## Installation with Composer

```bash
$ composer require germania-kg/asset-timestamper
```

Alternatively, add this package directly to your *composer.json:*

```json
"require": {
    "germania-kg/asset-timestamper": "^2.0"
}
```
## Upgrade from v1

In v1, a **FileException** was thrown if a given asset did not exist. As of version 2, the original asset file name will be returned. If you have not seen this *FileException* until now, you will have to do nothing. All others have to remove *FileException* catch blocks.

**Deprecation warning:** The *FileException* class is still available with this package and will be removed in Release Version 3. Discuss at [issue #1][i1].



## Usage

You do not need to have a leading directory separator slash, as it will internally be “glued in” if neccessary. However, the result will have (or miss) the slash, depending on how you pass in the asset file name.

```php
<?php
use Germania\AssetTimestamper\AssetTimestamper;

// Instantiation
$at = new AssetTimestamper;

// Both are equal
echo $at( '/dist/styles.css' );
echo $at->__invoke( '/dist/styles.css' );

//even those, missing leading slash:
echo $at( 'dist/styles.css' );
echo $at->__invoke( 'dist/styles.css' );


// Outputs something like:
"/dist/styles/styles.20160522140459.css"
```

### Using different base paths

You can define a custom directory where AssetTimestamper should look for the assets. This is useful when your assets are located in a directory accessed via another (sub-)domain. Let's say your project directory looks like this:

```
# Your current work dir, usually "www" subdomain
/var/www/project/htdocs

# Static files on a "static" subdomain
/var/www/project/static
/var/www/project/static/dist/styles.css
```

### Examples for PHP and HTML:

Use AssetTimestamper with another base directory:

```php
<?php
// Instantiation
$at = new AssetTimestamper( "/var/www/project/static" );

echo '<link rel="stylesheet" type="text/css" href="//static.test.com'
     . $at( '/dist/styles.css' ) . '">';
```

HTML Output:

```html
<link rel="stylesheet" type="text/css" href="//static.test.com/dist/styles.20160522140459.css">
```

### Simple Twig Example

```php
echo $twig->render("website.tpl", [
	'stylesheets' => [
		$at( 'dist/styles.css' ),
		$at( 'dist/widget.css' )
	]
]);
```

## Alternative Twig Integration: Filter

Since AssetTimestamper is invokable and a Callable, it can be easily used as a [Twig SimpleFilter:](http://twig.sensiolabs.org/doc/advanced.html#filters)

```php
<?php
$at     = new AssetTimestamper( "/var/www/project/static" ),
$filter = new Twig_SimpleFilter('add_timestamp', $at),

$twig->addFilter( $filter );
```

So rendering a website like this:

```php
echo $twig->render("website.tpl", [
	'stylesheets' => [
		'dist/styles.css',
		'dist/widget.css'
	]
]);
```

…using this Twig template …

```twig
{% for css in stylesheets %}
<link rel="stylesheet" href="//static.test.com/{{ css|add_timestamp }}">
{% endfor %}
```

… will lead to this output:

```html
<link rel="stylesheet" href="//static.test.com/dist/styles.20160419100233.css">
<link rel="stylesheet" href="//static.test.com/dist/widget.20160413152259.css">
```



## .htaccess

Since browsers will request the modified file name (which in fact does not exist, at least with this name), you will have to rewrite the URL in your .htaccess, like so:

```
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)\.(\d+)\.(js|css)$ $1.$3 [L]
```

See Chris Coyier's article [“Strategies for Cache-Busting CSS”](https://css-tricks.com/strategies-for-cache-busting-css), chapter [“Changing File Name“](https://css-tricks.com/strategies-for-cache-busting-css/#article-header-id-2), and Stefano's [comment on this.](https://css-tricks.com/strategies-for-cache-busting-css/#comment-1596418)




## Issues

No issues known so far, feel free to create one at [issues page.][i0]

[i0]: https://github.com/GermaniaKG/AssetTimestamper/issues 
[i1]: https://github.com/GermaniaKG/AssetTimestamper/issues/1 


## Development

```bash
$ git clone https://github.com/GermaniaKG/AssetTimestamper.git
$ cd AssetTimestamper
$ composer install
```

## Unit tests

Either copy `phpunit.xml.dist` to `phpunit.xml` and adapt to your needs, or leave as is. Run [PhpUnit](https://phpunit.de/) test or composer scripts like this:

```bash
$ composer test
# or
$ vendor/bin/phpunit
```

