@servers(['production' => 'zack@zack6849.com'])

@setup
$repo = 'git@github.com:zack6849/website-laravel.git';
$appDir = '/home/zack/site';
$branch = 'master';

date_default_timezone_set('America/New_York');
$date = date('Y-m-d_H-i-s');

$builds = $appDir . '/sources';
$deployment = $builds . '/' . $date;

$serve = $appDir . '/source';
$env = $appDir . '/.env';
$storage = $appDir . '/storage';
@endsetup

@story('deploy')
git
install
live
@endstory

@task('git', ['on' => 'production'])
git clone -b {{ $branch }} "{{ $repo }}" {{ $deployment }}
@endtask

@task('install', ['on' => 'production'])
cd {{ $deployment }}

rm -rf {{ $deployment }}/storage

ln -nfs {{ $env }} {{ $deployment }}/.env

ln -nfs {{ $storage }} {{ $deployment }}/storage
yarn install
composer install --prefer-dist
yarn run production
php ./artisan migrate --force
@endtask

@task('live', ['on' => 'production'])
cd {{ $deployment }}

ln -nfs {{ $deployment }} {{ $serve }}
@endtask
