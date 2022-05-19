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
task('build_locally', function () {
    runLocally('yarn install');
    runLocally('yarn build');
    upload('./public/build', '{{release_path}}/public/.');
});

task('build_remote', function () {
    cd('{{release_path}}');
    run('yarn install');
    run('yarn build');
});

// Attach custom tasks to default workflow
before('deploy:symlink', 'database:migrate');

// Switch "build_locally" with "build_remote" if your setup requires
after('deploy:vendors', 'build_locally');

// On failure
after('deploy:failed', 'deploy:unlock');
after('deploy:failed', 'rollback');
