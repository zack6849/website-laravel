<x-base-page title="Index" noheader="true">
    <div class="flex container mx-auto mb-6">
        <div class="hidden lg:visible lg:block mr-10">
            <img src="{{asset('img/profile.jpg')}}" class="rounded-full w-48">
        </div>
        <div>
            <h1 class="font-light text-5xl p2">Hello There</h1>
            <p class="pt-2">I'm Zack, I'm a {{Carbon\Carbon::create(1997, 7 , 24)->diffInYears(Carbon\Carbon::now())}} year old Full-Stack Web Developer, Radio Enthusiast and Hobbyist Photographer from the Tampa Bay Area.</p>
            <p>I like to solve problems and take pictures. This is my website / portfolio / dumping ground.</p>
            <p><b>Nice to meet you.</b></p>
            <div class="flex flex-wrap flex-row items-center justify-around my-6 ">
                <a target="_blank" href="https://github.com/zack6849"><div class="bg-purple-600 rounded p-2 text-white leading-none shadow mr-1 mt-2"> <i class="fab fa-github"></i> GitHub</div></a>
                <a target="_blank" href="https://stackoverflow.com/users/1932789/zack6849"><div class="bg-orange-600 rounded p-2 text-white leading-none shadow mr-1 mt-2"><i class="fab fa-stack-overflow"></i> StackOverflow</div></a>
                <a target="_blank" href="https://keybase.io/zack6849"><div class="bg-blue-500 rounded p-2 text-white leading-none shadow mr-1 mt-2"><i class="fab fa-keybase"></i> Keybase</div></a>
                <a target="_blank" href="https://www.linkedin.com/in/zack6849/"><div class="bg-blue-800 rounded p-2 text-white leading-none shadow mr-1 mt-2"><i class="fab fa-linkedin"></i> LinkedIn</div></a>
            </div>
        </div>
    </div>
    <hr>
    <div class="container-fluid  mt-5 py-5" id="technologies">
        <div class="container mx-auto">
            <div class="flex flex-wrap justify-around">
                <technology name="PHP" image="{{asset('img/logos/language/php.svg')}}"></technology>
                <technology name="Laravel" image="{{asset('img/logos/platform/laravel.svg')}}"></technology>
                <technology name="Linux" image="{{asset('img/logos/os/linux-tux.svg')}}"></technology>
                <technology name="MySQL" image="{{asset('img/logos/technology/mysql.svg')}}"></technology>
                <technology name="Docker" image="{{asset('img/logos/technology/docker.svg')}}"></technology>
                <technology name="Magento 2 & 1" image="{{asset('img/logos/platform/magento-2.svg')}}"></technology>
                <technology name="WordPress" image="{{asset('img/logos/platform/wordpress-blue.svg')}}"></technology>
                <technology name="WooCommerce" image="{{asset('img/logos/platform/woocommerce-1.svg')}}"></technology>
                <technology name="Java" image="{{asset('img/logos/language/java.svg')}}"></technology>
            </div>
        </div>
    </div>
    <div class="container mx-auto my-5">
        <div class="mt-10 mb-6">
            <h3 class="text-3xl font-light">Open Source Projects</h3>
            <p>Pretty much everything I do nowadays is for a company and not public, but here's a small collection of things I maintain or created at one point in time, in no particular order.</p>
        </div>
       <div class="flex flex-wrap justify-between">
            <project-card title="YAPI (Yet Another PHP Image)" image="{{asset('img/undraw_developer_activity_re_39tg.svg')}}">
                <p>A local docker PHP development image providing (mostly) everything you need* for a basic web project</p>
                <p class="pt-2">
                    Features:
                <ul class="list-disc pl-4">
                    <li>NGINX</li>
                    <li>PHP-FPM (with xdebug)</li>
                    <li>MariaDB</li>
                    <li>Redis</li>
                    <li><a href="https://github.com/mailhog/MailHog" target="_blank">MailHog</a></li>
                    <li>Selenium (if your application needs browser automation.)</li>
                </ul>
                </p>
                <template slot="footer">
                    <div class="flex flex-row-reverse">
                        <a href="https://github.com/zack6849/yapi"><i class="fab fa-github"></i></a>
                    </div>
                </template>
            </project-card>
            <project-card title="ShipStation PHP Wrapper" image={{asset('img/logistics_x4dc.svg')}}>
                <p>An updated (and (sort of) maintained) fork of <a href="https://github.com/michaelbonds/ship-station">michaelbonds/ship-station</a></p>
                <p>Designed to programmatically access the <a href="https://www.shipstation.com/docs/api/" target="_blank">ShipStation REST API</a> via PHP with Guzzle</p>
                <template slot="footer">
                    <div class="flex flex-row-reverse">
                        <a href="https://github.com/zack6849/ship-station"><i class="fab fa-github"></i></a>
                        <a href="https://packagist.org/packages/zack6849/ship-station"><i class="fa fa-box"></i></a>
                    </div>
                </template>
            </project-card>
            <project-card title="AlphabotV2" image={{asset('img/Artificial_intelligence_oyxx.svg')}}>
                An extensible Java IRC bot with a node-based permissions system, meant for basic channel administration and utilities, featuring a full plugin system with runtime classloading
                <template slot="footer">
                    <div class="flex flex-row-reverse">
                        <a href="https://github.com/zack6849/AlphabotV2"><i class="fab fa-github"></i></a>
                    </div>
                </template>
            </project-card>
        </div>
    </div>
</x-base-page>

