@servers(['production' => 'zack@zack6849.com'])

@setup
$repo = 'https://github.com/zack6849/website-laravel.git';
$appDir = '/srv/www/zack6849.com';
$branch = 'master';

date_default_timezone_set('America/New_York');
$date = date('Y-m-d_H-i-s');

$builds = $appDir . '/sources';
$deployment = $builds . '/' . $date;

$serve = $appDir . '/source';
$env = $appDir . '/.env';
$storage = $appDir . '/storage';
$use_fpm = true;
$fpm_service = 'php7.3-fpm';
@endsetup

@story('deploy')
	git
	install
	live
    @if ($use_fpm)
        restart-fpm
    @endif
@endstory

@task('git', ['on' => 'production'])
    echo "Cloning..."
	git clone -b {{ $branch }} "{{ $repo }}" {{ $deployment }}
@endtask

@task('install', ['on' => 'production'])
echo "Installing new version"
cd {{ $deployment }}
rm -rf {{ $deployment }}/storage
ln -nfs {{ $env }} {{ $deployment }}/.env
ln -nfs {{ $storage }} {{ $deployment }}/storage
echo "Installing NPM dependencies"
npm install
echo "Installing PHP dependencies"
composer install --no-interaction --quiet --no-dev --prefer-dist --optimize-autoloader
echo "Compiling SCSS & JS"
npm run production
echo "Running migrations"
php ./artisan migrate --force
@endtask

@task('live', ['on' => 'production'])
echo "Making current deployment live"
cd {{ $deployment }}
ln -nfs {{ $deployment }} {{ $serve }}
echo "Clearing caches"
php artisan view:clear --quiet
php artisan cache:clear --quiet
php artisan config:clear --quiet
echo "Deployment complete!"
@endtask

@task('restart-fpm', ['on' => 'production', 'confirm' => 'Restart PHP-FPM?'])
    sudo service {{$fpm_service}} restart
@endtask


@task('debug', ['on' => 'production'])
echo $PATH
echo $SHELL
whatis composer
whereis composer
@endtask
