![Peridot logo](https://raw.github.com/peridot-php/peridot/master/logo.png "Peridot logo")

[![Build Status](https://travis-ci.org/peridot-php/peridot.png)](https://travis-ci.org/peridot-php/peridot) [![HHVM Status](http://hhvm.h4cc.de/badge/peridot-php/peridot.svg)](http://hhvm.h4cc.de/package/peridot-php/peridot)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/peridot-php/peridot/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/peridot-php/peridot/?branch=master) [![Coverage Status](https://coveralls.io/repos/peridot-php/peridot/badge.png?branch=master)](https://coveralls.io/r/peridot-php/peridot?branch=master)
[![Packagist](https://img.shields.io/packagist/dt/peridot-php/peridot.svg?style=flat)](https://packagist.org/packages/peridot-php/peridot)

The highly extensible, highly enjoyable, PHP testing framework.

Read more at [peridot-php.github.io](http://peridot-php.github.io/) or head over to the [wiki](https://github.com/peridot-php/peridot/wiki).

##Building PHAR

Peridot's phar is built using [Box](http://box-project.org/). Once box is installed, the phar can be built using
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
