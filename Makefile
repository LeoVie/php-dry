build_phpstan_image:
	cd docker && docker build . -f phpstan.Dockerfile -t php-clone-detection/phpstan:latest && cd -

phpstan:
	docker run -v ${PWD}:/app --rm php-clone-detection/phpstan:latest analyse -c /app/build/config/phpstan.neon

phpunit:
	composer phpunit

test: phpstan
	composer testall

build_image:
	docker build -f docker/project/Dockerfile -t php-cd:latest .

queo_registry_login:
	docker login dockerhub.cloud.queo.org

build_and_push_image_queo_remote:
	composer install --no-dev
	rm -rf ./vendor/*/*/.git
	docker build -f docker/project/Dockerfile -t dockerhub.cloud.queo.org/queo.web/projects/php-cd-example/php-cd:latest . --no-cache
	docker push dockerhub.cloud.queo.org/queo.web/projects/php-cd-example/php-cd:latest
	rm -rf ./vendor
	composer install
