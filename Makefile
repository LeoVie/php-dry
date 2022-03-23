build_phpstan_image:
	cd docker && docker build . -f phpstan.Dockerfile -t php-dry/phpstan:latest && cd -

phpstan:
	docker run -v ${PWD}:/app --rm php-dry/phpstan:latest analyse -c /app/build/config/phpstan.neon

phpunit:
	composer phpunit

unit:
	composer phpunit -- --testsuite=unit

functional:
	composer phpunit -- --testsuite=functional

test: phpstan
	composer testall

psalm:
	composer psalm

infection:
	composer infection

infection-after-phpunit:
	composer infection-after-phpunit

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

build_and_push_image: build_image
	docker push leovie/php-dry:$(tag)
