# Contributing to this project

Any contribution to this project is appreciated, whether it is related to fixing bugs, suggestions or improvements. Feel free to take your part in the development!

However, you should follow the following simple guidelines for your contribution to be properly recived:

-   This project uses the [GitFlow branching model](http://nvie.com/posts/a-successful-git-branching-model/) for the process from development to release. Because of GitFlow contributions can only be accepted via pull requests on [Github](https://github.com/nplhse/cois).
-   Please keep in mind, that this project follows [SemVer v2.0.0](http://semver.org/).
-   You should make sure to follow the [PHP Standards Recommendations](http://www.php-fig.org/psr/) and the [Symfony best practices](http://symfony.com/doc/current/best_practices/index.html).
-   Also, you must agree to comply to the [Code of Conduct](CODE_OF_CONDUCT.md) of this porject.

## Setup of your dev environment

This project expects you to have local webserver and a locally installed MySQL/MariaDB instance, see installation part of the [README](README.md). It seamlessly integrates works with the [Symfony binary cli tool](https://github.com/symfony-cli/symfony-cli).

### Using Docker

If you'd like there is support for Docker which mainly includes the database, as we recommend using the symfony cli server with a local installation of PHP. There is also a `make build` command that builds the containers und starts them in detached mode.

### Run Tests

To be able to run the tests properly you need to execute `make test-database`. This command creates the testing database including the schema and all required fixtures.

If you have the need to re-populate the database with some fresh Fixtures you could either directly execute `bin/console doctrine:fixtures:load` or use `make reset-database` instead of `make reset` which resets the whole project.

When using these fixtures there are always several pre-configured Users by default:

| Username    | Password   | Description                         |
| ----------- | ---------- | ----------------------------------- |
| admin       | _password_ | **Admin** user with full privileges |
| foo         | _password_ | Default user                        |
| unknownUser | _password_ | User with **no** access             |
| expiredUser | _password_ | User with **expired** credentials   |

## Available make commands

| Command            | Description                                     |
| ------------------ | ----------------------------------------------- |
| help               | Outputs help screen                             |
| setup              | Setup the whole project                         |
| setup-dev          | Setup the project in dev environment            |
| install            | Install composer dependencies                   |
| setup-frontend     | Setup the frontend via yarn                     |
| setup-frontend-dev | Setup the frontend via yarn (dev environment)   |
| setup-database     | Setup the database backend                      |
| setup-fixtures     | Install the fixtures                            |
| reset-database     | Reset the whole database (caution!)             |
| test-database      | Setup the test database                         |
| start              | Build and start the containers                  |
| build              | Builds the Docker images                        |
| up                 | Start the docker hub in detached mode (no logs) |
| down               | Stop the docker hub                             |
| logs               | Show live logs                                  |
| checks             | Run static checks pipeline                      |
| ci                 | Run CI pipeline                                 |
| reset              | Reset pipeline for the whole project (caution!) |
| tests              | Run test pipeline                               |
