RUN = docker compose run --rm -T app

.PHONY: install test lint fix

install:
	docker compose build
	$(RUN) composer install

test:
	$(RUN) vendor/bin/phpunit

lint:
	$(RUN) vendor/bin/phpstan analyse --no-progress
	$(RUN) vendor/bin/php-cs-fixer check
	$(RUN) vendor/bin/deptrac analyse --no-progress

fix:
	$(RUN) vendor/bin/php-cs-fixer fix
