#peridot [![Build Status](https://travis-ci.org/peridot-php/peridot.png?branch=develop)](https://travis-ci.org/peridot-php/peridot?branch=develop)

BDD for PHP!

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
bin/peridot specs/
```
