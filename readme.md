# Befeni Technical Tests Solutions

Name: Guillermo Maschwitz \
Email: guilledevel@gmail.com

## Intro

**Welcome aboard!**

This is my submission for the **Full Stack Developer (PHP/JS)** position.

Thanks for taking the time to review my application!

## Overview

This is the list of bullets that are covered in this deliverable

### Befeni Technical Test [Basic]

- Core problem
  - solution/src/html/index.php
  - solution/src/calculator.php
  - solution/data/input.txt
- Provide Unit Tests for your code
  - solution/src/tests/BasicTest.php
  - solution/data/*.txt
  - solution/src/composer.*
- Dockerize your solution so that your code can be run with a simple `docker-compose up` command
  - Makefile
  - solution/src/Dockerfile
  - solution/src/docker-compose.yml
  - solution/src/.dockerignore

### Befeni Technical Test [Advanced]

- Requirement #1
  - solution/src/model/repository/ShirtOrderRepository.php
  - solution/src/model/repository/AbstractRepository.php
  - solution/src/model/repository/IDataMapper.interface.php
  - solution/src/dbal/IDataSourceAdapter.interface.php
  - solution/src/dbal/InMemoryDataSourceAdapter.php
  - solution/src/utils/SingletonTrait.php
  - solution/src/model/entity/ShirtOrder.php (same as original)
- Requirement #2
  - solution/src/tests/AdvancedTest.php
- Requirement #3
- Extra: Used the same docker environment I made for the Basic test
- Extra: Automated tests for Requirement #3

### Side note

I'm in the process of writing my first technical blog. It might be useful to asses my proficiency in areas like frontend development or infrastructure as code. \
It lacks real content, but you can see how it is going at https://guille.cloud/ \
Its code is available for review at https://github.com/guille-mas/blog

------------------------------------------------------------

## Getting Started

I've put in place a GNU Make set of commands to ease tasks such as running and testing both solutions on its own docker runtime environment. You will need GNU Make or similar to ease those tasks. Linux or MacOS terminal should support it out of the box. In case you are using Windows, I recommend to use PowerShell terminal. But don't worry if you don't have Make in your computer; you can copy paste the commands inside Makefile or read the [Useful commands without GNUMake](#useful-commands-wo-make) section

### Initial setup

Run `make all` from the root of this repo. The command will perform the following actions:

- Build two custom docker images based on an ubuntu distro, with PHP 7.4 and Apache: `befeni/server:1` and `befeni/server:1-development` \
The only difference between both is the presence of PHPUnit on the development image.
- After both images are created, every PHPUnit test will be executed inside a container based on `befeni/server:1-development`

### Useful commands

- `make all` \
Build images and run automated tests
- `make start` \
Start a docker-compose development environment with your version of the code mounted inside of it.
- `make test` \
Run every PHPUnit test on the same exact environment build with `make build`. This means that even if you change the code in this project, your changes won't be reflected inside the image
- `make run` \
Run any command in a docker-compose environment from within the solution/src folder
- `make clean` \
Remove every docker container and docker image created for this project

<a name="useful-commands-wo-make"></a>

### Useful commands without GNUMake

#### Build

`docker build -t befeni/server:1 --target server-production ./solution`
`docker build -t befeni/server:1-development --target server-development ./solution`

#### Running a docker-compose development environment

`docker-compose -f ./solution/docker-compose.yml up --no-start` \
`docker-compose -f ./solution/docker-compose.yml run befeni_server composer install` \
`docker-compose -f ./solution/docker-compose.yml up`

#### Running inmutable disposable containers

Production version:\
`docker run --rm -p 80:80/tcp befeni/server:1`

Development version:\
`docker run --rm -p 80:80/tcp befeni/server:1-development`

#### Running PHPUnit tests

`docker run -t --rm befeni/server:1-development /var/www/vendor/bin/phpunit --colors /var/www/tests`

------------------------------------------------------------

### Final notes

#### Inmutable docker images

Dealing with mutable state it´s usually more challenging than writing code that doesn't deal with it. \
With that in mind I wrote both docker images as inmutable. Every requirement to run the app is provided at built time; no volumes need to be mounted to run or test them, to ease usage and maintainment at the expense of slower build times.

Development image only differs with the production version on the way `composer install` its executed. The first one includes dev required vendors (only PHPUnit to be more precise), while the production version run `composer install` with production flags and creates a class map to provide autoloading of vendors in O(1) order.

I choose `befeni/` namespace for tagging docker images. You could potentially upload those images to a befeni dockerhub account to ease others the task of trying this solution.

#### Low hanging fruit enhancements to the designed solution

In real life I usually choose to adopt well docummented, stable, existing open source solutions, than writing all these by myself, but if I should imagine that this is a real life project that should have been implemented by myself as a hard requirement, then this is a short list of enhancements I would suggest to add to the project roadmap. It's all opex related, but all the points are aiming at easing the maintainment of the app.

- Implement PSR-4 complain autoloading instead of using require_once everywhere
- Instead of defining the data repository as a singleton, a better solution would be to reuse an existing Dependency injection container and use that as a service provider for that kind of classes.
- Write database oriented implementations of IDataSourceAdapter interface by designing a DatabaseConfiguration class that could be injected to their constructors
- I would rewrite automated tests to not depend one on each other
