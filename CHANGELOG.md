# Changelog

#### 1.15.1

* Eliminate duplicate directory separators in path info *2015-03-31*


#### 1.15.0

* Allow test and suite definitions to be invoked with arguments via TestInterface::setDefinitionArguments and TestInterface::getDefinitionArguments *2015-02-24*


#### 1.14.0

* Added count accessors to TestResult *2015-02-15*


#### 1.13.0

* runner.end event now receives the run time as an argument *2015-02-12*


#### 1.12.0

* AbstractTest objects now include the file path associated with them via getFile() *2015-02-06*
* Command::getRunner() and Command::setRunner() added *2015-02-06*


#### 1.11.1

* Exception type hinting no longer required in test results and reporters *2015-01-31*


#### 1.11.0

* Configuration variables persisted to ENV *2015-01-22*


#### 1.10.0

* Added Context::clear() method *2015-01-17*


#### 1.9.0

* added Application accessors for Runner, Configuration and Environment *2015-01-06*
* added RunnerInterface *2015-01-06*
* peridot.configure is passed Application *2015-01-06*


#### 1.8.5

* Support hhvm 3.5 nightly autoload path in bin/peridot *2014-12-27*


#### 1.8.4

* update all scope refs to core scope *2014-11-16*


#### 1.8.3

* Adds Core Scope back as an extension of scope module *2014-11-16*


#### 1.8.2

* Pull scope into separate package *2014-11-16*


#### 1.8.1

* multiline exception messages indent each line *2014-11-11*


#### 1.8.0

* AbstractTest::forEachNodeBottomUp and forEachNodeTopDown traversal methods added to public API *2014-11-09*


#### 1.7.1

* ANSI sequences are now sub process friendly *2014-11-06*


#### 1.7.0

* Fail tests if exceptions thrown in tear downs *2014-11-05*


#### 1.6.1

* Fixed bug where manually specified config file was not being included *2014-11-03*


#### 1.6.0

* Silly dependency on ArrayObject removed *2014-11-03*
* Parent scope now correctly passed to children *2014-11-03*
* Setup and tear down functions execute in proper order *2014-11-03*
* Removed Scope::BEHAVIOR_ constants and code depending on them *2014-11-03*


#### 1.5.0

* Introduce scope behaviors *2014-11-02*
* Fix order of execution for setup and teardown functions *2014-11-02*


#### 1.4.0

* Added Suite::setTests method *2014-10-31*


#### 1.3.1

* Include DSLs only once *2014-10-31*


#### 1.3.0

* peridot.start event includes application as second argument *2014-10-30*


#### 1.2.3

* psr2 compliance use psr2 config in code sniffer *2014-10-30*


#### 1.2.2

* Added coveralls.io support *2014-10-30*
* Application::getInput() accepts optional argv *2014-10-30*


#### 1.2.1

* Got a rad 10.0 quality score on scrutinizer-ci *2014-10-29*
* Reduced complexity of Environment *2014-10-29*


#### 1.2.0

* Improved code coverage of core *2014-10-29*
* Added Suite::halt() to public API *2014-10-29*

#### 1.1.7

* Reduced complexity of console command *2014-10-29*


#### 1.1.5

* Reduced complexity of AbstractBaseReporter *2014-10-29*


#### 1.1.4

* Reduced complexity of Test *2014-10-29*


#### 1.1.3

* Improve build process *2014-10-29*


#### 1.1.2

* Reduced complexity of ReporterFactory *2014-10-29*


#### 1.1.1

* Reduced complexity of ConfigurationReader *2014-10-29*
* Various unused variable removals *2014-10-28*


#### 1.1.0

* Support nullable Test definitions - i.e auto pending tests *2014-10-28*

