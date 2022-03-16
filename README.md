# COIS - Collaborative IVENA statistics

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT) [![Continuous Integration](https://github.com/nplhse/cois/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/nplhse/cois/actions/workflows/continuous-integration.yml)

# Requirements

-   Webserver (Apache, Nginx, LiteSpeed, IIS, etc.) with PHP 8.0 or higher and MySQL 8.0 as database

# Installation

## From GitHub

1. Launch a **terminal** or **console** and navigate to the webroot folder. Clone this repository from [https://github.com/nplhse/cois]() to a folder in the webroot of your server, e.g. `~/webroot/cois`.

    ```
    $ cd ~/webroot
    $ git clone https://github.com/nplhse/cois.git
    ```

2. Install the tool and it`s dependencies by using **composer** and:

    ```
    $ cd ~/webroot/cois
    $ composer install
    $ composer setup
    ```

3. You are ready to go, just open the site with your favorite browser!

### Dev Environment

If you have the need to populate the database with some Fixtures for development you could either directly execute `bin/console doctrine:fixtures:load` or use `composer setup-dev` instead of `composer setup`.

# Contributing

Any contribution to this project is appreciated, whether it is related to fixing bugs, suggestions or improvements. Feel free to take your part in the development of COIS! 

However you should follow some simple guidelines which you can find in the [CONTRIBUTION](CONTRIBUTION.md) file. Also, you must agree to the [Code of Conduct](CODE_OF_CONDUCT.md).

# License

See [LICENSE](LICENSE.md).
