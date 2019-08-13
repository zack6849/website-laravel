@servers(['production' => 'zack@zack6849.com'])

@setup
$repo = 'https://github.com/zack6849/website-laravel.git';
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

@story('test')
debug
@endstory()

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
npm install
composer install --prefer-dist
npm run production
php ./artisan migrate --force
echo "Deployment complete!"
echo "You may need to restart PHP-FPM if it's being used"
@endtask

@task('live', ['on' => 'production'])
cd {{ $deployment }}

ln -nfs {{ $deployment }} {{ $serve }}
@endtask


@task('debug', ['on' => 'production'])
echo $PATH
echo $SHELL
whatis composer
whereis composer
@endtask
