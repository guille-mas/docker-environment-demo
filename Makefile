
build:
# php images build
	docker build -t befeni/server:1 --target server-production ./solution
	docker build -t befeni/server:1-development --target server-development ./solution

start:
	docker-compose -f ./solution/docker-compose.yml up

test:
	docker run --rm befeni/server:1-development /var/www/vendor/bin/phpunit /var/www/tests/*.php

run:
	@read -p "Write a command to run inside your docker environment: " command; \
	docker-compose run blog sh -c "$$command"

