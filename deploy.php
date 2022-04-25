<?php

namespace Deployer;

require 'recipe/symfony.php';

// Config
set('repository', 'https://github.com/nplhse/cois.git');
set('keep_releases', 4);
set('writable_mode', 'chmod');
set('env', [
    'APP_ENV' => 'prod',
]);

// Directories
add('shared_dirs', [
    'var/log',
    'var/storage',
    ]);
add('writable_dirs', [
    'var',
    'var/cache',
    'var/log',
    'var/storage',
]);

// Hosts
import('hosts.yaml');

// Customized tasks
task('build', function () {
    cd('{{release_path}}');
    run('yarn install');
    run('yarn build');
});

before('deploy:symlink', 'database:migrate');

after('deploy:vendors', 'build');

// On failure
after('deploy:failed', 'deploy:unlock');
