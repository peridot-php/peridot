ifeq ($(TRAVIS_PHP_VERSION), nightly)
coverage:
	echo "Skipping coverage for nightly"
	exit 0
else
coverage:
ifndef CODE_COVERAGE
	$(error CODE_COVERAGE is undefined)
endif

ifeq ($(TRAVIS_PHP_VERSION), $(filter $(TRAVIS_PHP_VERSION), 7.0 7.1))
	phpdbg --version
	phpdbg -qrr bin/peridot specs
else
	bin/peridot specs
endif
endif
