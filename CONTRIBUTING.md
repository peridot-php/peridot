#Contributing
We dig open source. If you want to help out, we would certainly welcome contributions large or small.

##Reporting Bugs
Issues can be reported on the [issue tracker](https://github.com/peridot-php/peridot/issues). When reporting,  examples that allow contributors to reproduce the issue are always appreciated. Even better, include failing test.

##Requesting Features
We would love to hear ideas for making Peridot better. Feel free to create a "feature" issue on the [issue tracker](https://github.com/peridot-php/peridot/issues).

If you see an existing feature request that you like, please chime in to the conversation so other contributors can see how many people are interested in a particular feature.

##Contributing code
We stick to [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standards. All of our pull requests get run through [Scrutinizer](https://scrutinizer-ci.com/g/peridot-php/peridot/?branch=master), and we are interested in keeping Peridot's quality score at 10.0. We think this tool will help keep Peridot clean and lean so contributors have as an easier time moving about the codebase. 

We will consider code merge worthy if it keeps the Scrutinizer score at a 10.0.

###Steps for contributing
1. Fork the [repository](https://github.com/peridot-php/peridot) on GitHub
2. Add tests for your feature or bug
3. Add the code for your feature or bug
4. Include documentation for things being added to the public API of Peridot (in the form of doc block comments)
5. Send a [pull request](https://help.github.com/articles/creating-a-pull-request)

Peridot was built test first using itself, so please include tests as Peridot tests :)

##Contributing to the site
The main [Peridot site](http://peridot-php.github.io/) lives on [GitHub](https://github.com/peridot-php/peridot-php.github.io).

If you have suggestions for documentation or ideas for making the site more useful, please feel free to open pull requests there.

##Testing Peridot
Peridot tests can be run using Peridot. Your cloned repository should have a `bin` directory containing a `peridot` executable.

Before running tests, you will need to install the few dependencies Peridot has via [Composer](https://getcomposer.org/):

```
$ composer install
```

You can then run the tests like so:

```
$ bin/peridot specs/
```

You can generate a code coverage report by passing the `CODE_COVERAGE` environment variable when running tests. You can generate an HTML or clover style coverage report.

To output an HTML report to `tmp/report`:

```
$ CODE_COVERAGE=html bin/peridot specs/
```

or to output a clover report to `build/logs/clover.xml`:

```
$ CODE_COVERAGE=clover bin/peridot specs/
```

Our coverage target is 90%+
