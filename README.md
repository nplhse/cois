# COIS - Collaborative IVENA statistics

[![Continuous Integration](https://github.com/nplhse/cois/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/nplhse/cois/actions/workflows/continuous-integration.yml) [![Test Coverage](https://api.codeclimate.com/v1/badges/42c306c963c6a04bd2ea/test_coverage)](https://codeclimate.com/github/nplhse/cois/test_coverage) [![Maintainability](https://api.codeclimate.com/v1/badges/42c306c963c6a04bd2ea/maintainability)](https://codeclimate.com/github/nplhse/cois/maintainability)

# Requirements

-   Webserver (Apache, Nginx, LiteSpeed, IIS, etc.) with PHP 8.1 or higher and MySQL 8.0 (or MariaDB) as database

# Setup

This project expects you to have local webserver and a locally installed MySQL/MariaDB instance. See [Setup of your dev environment](CONTRIBUTING.md#setup-of-your-dev-environment) for more detailed information.

## Install from GitHub

1. Launch a **terminal** or **console** and navigate to the webroot folder. Clone this repository from [https://github.com/nplhse/cois]() to a folder in the webroot of your server, e.g. `~/webroot/cois`.

    ```
    $ cd ~/webroot
    $ git clone https://github.com/nplhse/cois.git
    ```

2. Install the tool and it`s dependencies by using **make**:

    ```
    $ cd ~/webroot/cois
    $ make setup
    ```

3. You are ready to go, just open the site with your favorite browser!

### Using Docker

This project includes support for Docker which mainly includes the database, as we recommend using the [Symfony binary cli server](https://github.com/symfony-cli/symfony-cli) with a local installation of PHP. There is a `make build` command that builds the containers und starts them in detached mode. More about the `make` setup can be found at [available make commands](CONTRIBUTING.md#available-make-commands).

# Contributing

Any contribution to this project is appreciated, whether it is related to fixing bugs, suggestions or improvements. Feel free to take your part in the development of COIS!

However you should follow some simple guidelines which you can find in the [CONTRIBUTING](CONTRIBUTING.md) file. Also, you must agree to the [Code of Conduct](CODE_OF_CONDUCT.md).

# License

See [LICENSE](LICENSE.md).
