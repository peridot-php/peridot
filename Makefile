test:
	php --version
	bin/peridot

coverage:
	phpdbg --version
	CODE_COVERAGE=html phpdbg -qrr -d memory_limit=512M bin/peridot

open-coverage:
	open tmp/report/index.html

travis:
ifeq ($(TRAVIS_PHP_VERSION), nightly)
	bin/peridot
else
ifeq ($(TRAVIS_PHP_VERSION), $(filter $(TRAVIS_PHP_VERSION), 7.0 7.1))
	phpdbg --version
	CODE_COVERAGE=clover phpdbg -qrr -d memory_limit=512M bin/peridot
else
	php --version
	CODE_COVERAGE=clover bin/peridot
endif
endif

.PHONY: test coverage open-coverage travis
