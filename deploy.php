<?php

namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/webpack_encore.php';

// Base configuration
set('repository', 'https://github.com/nplhse/cois.git');
set('keep_releases', 3);
set('writable_mode', 'chmod');
set('env', [
    'APP_ENV' => 'prod',
]);

// Set directories for symfony
add('shared_dirs', [
    'var/log',
    'var/storage',
    ]);

add('writable_dirs', [
    'var',
    'var/cache',
    'var/log',
    'var/sessions',
    'var/storage',
]);

/*
 * For security reasons we're importing a local hosts.yaml file that includes
 * hostnames etc. for our deployments.
 * For details see: https://deployer.org/docs/7.x/hosts#yaml-inventory
 */
import('hosts.yaml');

/*
 * Because deployment might fail when installing the yarn dependencies and the
 * building all the webpack encore stuff we're using a customized task that is
 * performing a local build followed by an upload of the "/public/build"
 * directory. If you don't need this special setup you could replace all of
 * this by the following code:
 *
 *      after('deploy:vendors', 'yarn:install');
 *      after('deploy:vendors', 'webpack_encore:build');
 */

// Customized tasks
task('build_locally', function () {
    runLocally('yarn install');
    runLocally('yarn build');
    upload('./public/', '{{release_path}}/public/.');
});

// Attach custom tasks to default workflow
after('deploy:vendors', 'build_locally');

// Attach tasks from recipes& contrib to default workflow
before('deploy:symlink', 'database:migrate');
