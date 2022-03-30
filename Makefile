.PHONY: setup_dev_environment
setup_dev_environment:
	make setup_env env=dev
	make setup_ci_images
	make install

.PHONY: setup_test_environment
setup_test_environment:
	make setup_env env=test
	make setup_ci_images
	make install

.PHONY: setup_ci_images
setup_ci_images: build_composer_image build_php-cs-fixer_image build_phpstan_image build_phpunit_image build_psalm_image build_infection_image

.PHONY: build_composer_image
build_composer_image:
	cd docker && docker build . -f composer.Dockerfile -t php-dry/composer:latest && cd -

.PHONY: composer
composer:
ifndef command
	$(error command is not set)
endif
	docker run -v $(shell pwd):/app php-dry/composer:latest $(command)

.PHONY: install
install:
	make composer command="install"

.PHONY: setup_env
setup_env:
ifndef env
	$(error env is not set)
endif
	cp .env.$(env) .env

.PHONY: test
test: phpstan psalm phpunit infection

.PHONY: build_php-cs-fixer_image
build_php-cs-fixer_image:
	cd docker && docker build . -f php-cs-fixer.Dockerfile -t php-dry/php-cs-fixer:latest && cd -

.PHONY: php-cs-fixer
php-cs-fixer:
	docker run -v ${PWD}:/app --rm php-dry/php-cs-fixer:latest fix --config /app/build/config/.php-cs-fixer.php

.PHONY: build_phpstan_image
build_phpstan_image:
	cd docker && docker build . -f phpstan.Dockerfile -t php-dry/phpstan:latest && cd -

.PHONY: phpstan
phpstan:
	docker run -v ${PWD}:/app --rm php-dry/phpstan:latest analyse -c /app/build/config/phpstan.neon

.PHONY: build_phpunit_image
build_phpunit_image:
	cd docker && docker build . -f phpunit.Dockerfile -t php-dry/phpunit:latest && cd -

.PHONY: phpunit
phpunit:
	docker run -v ${PWD}:/app --rm php-dry/phpunit:latest

.PHONY: unit
unit:
	docker run -v ${PWD}:/app --rm php-dry/phpunit:latest -- --testsuite=unit

.PHONY: functional
functional:
	docker run -v ${PWD}:/app --rm php-dry/phpunit:latest -- --testsuite=functional

.PHONY: build_psalm_image
build_psalm_image:
	cd docker && docker build . -f psalm.Dockerfile -t php-dry/psalm:latest && cd -

.PHONY: psalm
psalm:
	docker run -v ${PWD}:/app --rm php-dry/psalm:latest

.PHONY: build_infection_image
build_infection_image:
	cd docker && docker build . -f infection.Dockerfile -t php-dry/infection:latest && cd -

.PHONY: infection
infection:
	docker run -v ${PWD}:/app --rm php-dry/infection:latest

.PHONY: build_image
build_image:
ifndef tag
	$(error tag is not set)
endif
	composer install --no-dev
	rm -rf ./vendor/*/*/.git
	rm -f generated/*.php
	docker build -f docker/project/Dockerfile -t leovie/php-dry:$(tag) . --no-cache
	rm -rf ./vendor
	composer install

.PHONY: build_and_push_image
build_and_push_image: build_image
	docker push leovie/php-dry:$(tag)
