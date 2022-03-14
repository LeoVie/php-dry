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

build_image:
	composer install --no-dev
	rm -rf ./vendor/*/*/.git
	rm -f generated/*.php
	docker build -f docker/project/Dockerfile -t php-dry:latest . --no-cache
	rm -rf ./vendor
	composer install

queo_registry_login:
	docker login dockerhub.cloud.queo.org

build_and_push_image_queo_remote:
	composer install --no-dev
	rm -rf ./vendor/*/*/.git
	rm -f generated/*.php
	docker build -f docker/project/Dockerfile -t dockerhub.cloud.queo.org/queo.web/projects/php-cd-example/php-dry:latest . --no-cache
	docker push dockerhub.cloud.queo.org/queo.web/projects/php-cd-example/php-dry:latest
	rm -rf ./vendor
	composer install
