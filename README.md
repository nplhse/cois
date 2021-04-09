COIS - Collaborative IVENA statistics
=====================================

# Requirements

- Webserver (Apache, Nginx, LiteSpeed, IIS, etc.) with PHP 8.0 or higher and MySQL 8.0 as database

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

# Contributing
Any contribution to this project is appreciated, whether it is related to fixing bugs, suggestions or improvements. Feel free to take your part in the development of COIS!

However you should follow some simple guidelines which you can find in the [CONTRIBUTING](CONTRIBUTING.md) file.

# License
See [LICENSE](LICENSE.md).
