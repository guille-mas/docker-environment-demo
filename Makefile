
build:
# php images build
	docker build -t befeni/server:1 --target server-production ./solution
	docker build -t befeni/server:1-development --target server-development ./solution

start:
	docker-compose -f ./solution/docker-compose.yml up --no-start
	docker-compose -f ./solution/docker-compose.yml run befeni_server composer install
	docker-compose -f ./solution/docker-compose.yml up

# remove development environment container and docker images
clean:
	docker-compose -f ./solution/docker-compose.yml down
	docker-compose -f ./solution/docker-compose.yml rm
	docker image rm befeni/server:1 befeni/server:1-development

# run phpunit inside built image
test:
	docker run -t --rm befeni/server:1-development /var/www/vendor/bin/phpunit --colors /var/www/tests

# run phpunit on live container
test-live:
	docker-compose -f ./solution/docker-compose.yml run befeni_server /var/www/vendor/bin/phpunit --colors /var/www/tests

run:
	@read -p "Write a command to run inside your docker environment: " command; \
	docker-compose -f ./solution/docker-compose.yml run befeni_server sh -c "$$command"

