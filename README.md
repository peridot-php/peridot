![Peridot logo](https://raw.github.com/peridot-php/peridot/master/logo.png "Peridot logo")

[![Build Status](https://travis-ci.org/peridot-php/peridot.png)](https://travis-ci.org/peridot-php/peridot) [![HHVM Status](http://hhvm.h4cc.de/badge/peridot-php/peridot.svg)](http://hhvm.h4cc.de/package/peridot-php/peridot)
[![Build Status Windows](https://ci.appveyor.com/api/projects/status/ywtrf1v48nyhqsf5?svg=true)](https://ci.appveyor.com/project/brianium/peridot) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/peridot-php/peridot/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/peridot-php/peridot/?branch=master)
[![Packagist](https://img.shields.io/packagist/dt/peridot-php/peridot.svg?style=flat)](https://packagist.org/packages/peridot-php/peridot)

The highly extensible, highly enjoyable, PHP testing framework.

Read more at [peridot-php.github.io](http://peridot-php.github.io/) or head over to the [wiki](https://github.com/peridot-php/peridot/wiki).

##Building PHAR

Peridot's phar is built using [Box](https://github.com/box-project/). Once box is installed, the phar can be built using
the following command from the project directory:

```
box build
```

##Generating Peridot's documentation

Peridot API documentation is generated using [apigen](https://github.com/apigen/apigen). Once apigen is installed, run
the following command from the project directory:

```
apigen generate
```

This will output documentation to the docs/ directory.

##Running Peridot's tests

Peridot's test suite can be run using Peridot:

```
$ bin/peridot
```

And a sample of output:

![Peridot output sample](https://raw.github.com/peridot-php/peridot/master/output-sample.png "Peridot output sample")

##Release

We use [Robo](https://github.com/Codegyre/Robo) for releases.

```
robo release [version] [site-path]
```

## using assert for expectations

Peridot sets `ASSERT_CALLBACK` via [assert_options](http://php.net/manual/en/function.assert-options.php) in order to throw exceptions when using the native `assert` function. Peridot specs are considered passing if they don't throw an exception; if you are using `assert` for expectations and you find your specs are triggering false positives, you may need to update `zend.assertions` to `1` in your php.ini. This is set to `-1` by default in PHP 7+

If you aren't too keen on using `assert` for testing, there are a ton of assertion/expectation libraries out there. Anything that throws exceptions in response to a failed assertion will do. The [peridot-php](https://github.com/peridot-php) org has authored [Leo](https://github.com/peridot-php/leo), and this library offers a richer assertion vocabulary for testing.
