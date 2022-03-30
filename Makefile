.PHONY: setup_ci_images
setup_ci_images: build_phpstan_image build_phpunit_image build_psalm_image build_infection_image

.PHONY: test
test: phpstan psalm phpunit infection

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
