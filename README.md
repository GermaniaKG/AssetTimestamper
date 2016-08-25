# AssetTimestamper

**File modification timestamps for your website assets.**


This Callable looks for a given asset file (e.g. CSS, JS usually) in a base directory,
extracts its [modification time](http://php.net/manual/en/function.filemtime.php)
and returns a modified asset path that contains that very timestamp.


See Chris Coyier's article [“Strategies for Cache-Busting CSS”](https://css-tricks.com/strategies-for-cache-busting-css), chapter [“Changing File Name“](https://css-tricks.com/strategies-for-cache-busting-css/#article-header-id-2). Please note — As Chris Coyier [points out](https://css-tricks.com/strategies-for-cache-busting-css/#article-header-id-3), this technique may slow down server response. Using this technique for *only some files* should be fine, be aware to not over-use it.

##Installation

```bash
composer require germania-kg/asset-timestamper
```

Alternatively, add this package directly to your *composer.json:*

```json
"require": {
    "germania-kg/asset-timestamper": "^1.0"
}
```
##Usage

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

###Using different base paths

You can define a custom directory where AssetTimestamper should look for the assets. This is useful when your assets are located in a directory accessed via another (sub-)domain. Let's say your project directory looks like this:

```
# Your current work dir, usually "www" subdomain
/var/www/project/htdocs

# Static files on a "static" subdomain
/var/www/project/static
/var/www/project/static/dist/styles.css
```

**Examples for PHP and HTML:**

```php
<?php
// Instantiation
$at = new AssetTimestamper( "/var/www/project/static" );
echo $at( '/dist/styles.css' );
```

```html
<link rel="stylesheet" type="text/css" href="//static.test.com/dist/styles.20160522140459.css">
```




##.htaccess

Since browsers will request the modified file name (which in fact does not exist, at least with this name), you will have to rewrite the URL in your .htaccess, like so:

```
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.+)\.(d+)\.(js|css)$ $1.$3 [L]
```

See Chris Coyier's article [“Strategies for Cache-Busting CSS”](https://css-tricks.com/strategies-for-cache-busting-css), chapter [“Changing File Name“](https://css-tricks.com/strategies-for-cache-busting-css/#article-header-id-2), and Stefano's [comment on this.](https://css-tricks.com/strategies-for-cache-busting-css/#comment-1596418)


##Development and Testing

- Develop using `develop` branch, using Git Flow. 
- For testing, dive into repo directory, install PhpUnit and run `phpunit`.
