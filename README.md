![Peridot logo](https://raw.github.com/peridot-php/peridot/master/logo.png "Peridot logo")

[![Packagist Version](https://img.shields.io/packagist/v/peridot-php/peridot.svg?style=flat-square "Packagist Version")](https://packagist.org/packages/peridot-php/peridot)
[![Build Status](https://img.shields.io/travis/peridot-php/peridot/master.svg?style=flat-square "Build Status")](https://travis-ci.org/peridot-php/peridot)
[![HHVM Build Status](https://img.shields.io/badge/hhvm-tested-brightgreen.svg?style=flat-square "HHVM Build Status")](https://travis-ci.org/peridot-php/peridot)
[![Windows Build Status](https://img.shields.io/appveyor/ci/peridot/peridot/master.svg?label=windows&style=flat-square "Windows Build Status")](https://ci.appveyor.com/project/peridot/peridot)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/peridot-php/peridot.svg?style=flat-square "Scrutinizer Code Quality")](https://scrutinizer-ci.com/g/peridot-php/peridot/?branch=master)
[![Codecov Coverage](https://img.shields.io/codecov/c/github/peridot-php/peridot/master.svg?style=flat-square "Codecov Coverage")](https://codecov.io/gh/peridot-php/peridot)
[![Gitter Chat](https://img.shields.io/gitter/room/peridot-php/lobby.svg?style=flat-square "Gitter Chat")](https://gitter.im/peridot-php/lobby)

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
