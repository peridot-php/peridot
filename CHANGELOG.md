# Peridot Changelog

## 1.19.0 (2017-01-19)

- **[NEW]** Introduced focused specs for powerful test isolation ([#199],
  [#204], [#197], [#194], [#188], [#185], [#181])
- **[NEW]** Support for multiple reporters ([#202], [#186])
- **[IMPROVED]** Stack trace output now excludes irrelevant information ([#203],
  [#170])
- **[FIXED]** Fixed error handler signature ([#198] - thanks [@YuraLukashik])
- **[FIXED]** Fixed `--configuration` command line option parsing ([#201],
  [#171])

[#170]: https://github.com/peridot-php/peridot/issues/170
[#171]: https://github.com/peridot-php/peridot/issues/171
[#181]: https://github.com/peridot-php/peridot/issues/181
[#185]: https://github.com/peridot-php/peridot/issues/185
[#186]: https://github.com/peridot-php/peridot/issues/186
[#188]: https://github.com/peridot-php/peridot/pull/188
[#194]: https://github.com/peridot-php/peridot/pull/194
[#197]: https://github.com/peridot-php/peridot/pull/197
[#198]: https://github.com/peridot-php/peridot/pull/198
[#199]: https://github.com/peridot-php/peridot/pull/199
[#201]: https://github.com/peridot-php/peridot/pull/201
[#202]: https://github.com/peridot-php/peridot/pull/202
[#203]: https://github.com/peridot-php/peridot/pull/203
[#204]: https://github.com/peridot-php/peridot/pull/204

## 1.18.1 (2016-04-23)

- Symfony console version constraint fixed

## 1.18.0 (2016-03-14)

- Tests now fail when an error occurs - not just exceptions.

## 1.17.0 (2016-02-23)

- Suite type hints on `TestInterface` instead of `AbstractTest`

## 1.16.0 (2015-10-28)

- Add `--force-color` option (thanks [@vicary])

## 1.15.2 (2015-06-01)

- Fixed strict standards warning (thanks [@mrkrstphr])

## 1.15.1 (2015-03-31)

- Eliminate duplicate directory separators in path info

## 1.15.0 (2015-02-24)

- Allow test and suite definitions to be invoked with arguments via
  `TestInterface::setDefinitionArguments` and
  `TestInterface::getDefinitionArguments`

## 1.14.0 (2015-02-15)

- Added count accessors to `TestResult`

## 1.13.0 (2015-02-12)

- `runner.end` event now receives the run time as an argument

## 1.12.0 (2015-02-06)

- `AbstractTest` objects now include the file path associated with them via
  `getFile()`
- `Command::getRunner()` and `Command::setRunner()` added

## 1.11.1 (2015-01-31)

- Exception type hinting no longer required in test results and reporters

## 1.11.0 (2015-01-22)

- Configuration variables persisted to ENV

## 1.10.0 (2015-01-17)

- The `Context` class now supports a `clear` method to reset loaded suites.

## 1.9.0 (2015-01-06)

- `RunnerInterface` added for custom Runners to implement
- The `Application` class added methods `setRunner`, `getRunner`,
  `getEnvironment`, `getConfiguration`, and `setConfiguration`
- The `peridot.configure` event is passed an additional argument that is the
  `Application` instance

## 1.8.5 (2014-12-27)

- Support HHVM 3.5 nightly autoload path in `bin/peridot`

## 1.8.4 (2014-11-16)

- Update all scope refs to core scope

## 1.8.3 (2014-11-16)

- Adds Core Scope back as an extension of scope module

## 1.8.2 (2014-11-16)

- Pull scope into separate package

## 1.8.1 (2014-11-11)

- Multi-line exception messages indent each line

## 1.8.0 (2014-11-09)

- `AbstractTest::forEachNodeBottomUp` and `forEachNodeTopDown` traversal methods
  added to public API

## 1.7.1 (2014-11-06)

- ANSI sequences are now sub process friendly

## 1.7.0 (2014-11-05)

- Fail tests if exceptions thrown in tear downs

## 1.6.1 (2014-11-03)

- Fixed bug where manually specified config file was not being included

## 1.6.0 (2014-11-03)

- Silly dependency on `ArrayObject` removed
- Parent scope now correctly passed to children
- Setup and tear down functions execute in proper order
- Removed `Scope::BEHAVIOR_` constants and code depending on them

## 1.5.0 (2014-11-02)

- Introduce scope behaviors
- Fix order of execution for setup and tear-down functions

## 1.4.0 (2014-10-31)

- Added `Suite::setTests` method

## 1.3.1 (2014-10-31)

- Include DSLs only once

## 1.3.0 (2014-10-30)

- `peridot.start` event includes application as second argument

## 1.2.3 (2014-10-30)

- PSR-2 compliance use PSR-2 config in code sniffer

## 1.2.2 (2014-10-30)

- Added coveralls.io support
- `Application::getInput()` accepts optional argv

## 1.2.1 (2014-10-29)

- Got a rad 10.0 quality score on Scrutinizer CI
- Reduced complexity of Environment

## 1.2.0 (2014-10-29)

- Improved code coverage of core
- Added `Suite::halt()` to public API

## 1.1.7 (2014-10-29)

- Reduced complexity of console command

## 1.1.6 (2014-10-29)

- [`¯\_(ツ)_/¯`](https://github.com/peridot-php/peridot/compare/367f62a...1.1.6)

## 1.1.5 (2014-10-29)

- Reduced complexity of `AbstractBaseReporter`

## 1.1.4 (2014-10-29)

- Reduced complexity of `Test`

## 1.1.3 (2014-10-29)

- Improve build process

## 1.1.2 (2014-10-29)

- Reduced complexity of `ReporterFactory`

## 1.1.1 (2014-10-29)

- Reduced complexity of `ConfigurationReader`
- Various unused variable removals

## 1.1.0 (2014-10-28)

- Support nullable `Test` definitions - i.e auto pending tests

<!-- Contributors -->

[@mrkrstphr]: https://github.com/mrkrstphr
[@vicary]: https://github.com/vicary
[@YuraLukashik]: https://github.com/YuraLukashik
