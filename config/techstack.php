<?php

// Tiers: 1 = daily/expert, 2 = comfortable but not interview-ready offhand, 3 = used in the past.
// Trend: 'up' = actively growing interest/skill, 'down' = fading from use, omitted = steady.

return [
    'categories' => [
        'Backend' => [
            ['name' => 'Laravel', 'image' => 'img/logos/laravel.svg', 'tier' => 1],
            ['name' => 'PHP', 'image' => 'img/logos/php.svg', 'tier' => 1],
            ['name' => 'Livewire', 'image' => 'img/logos/livewire.svg', 'tier' => 1],
            ['name' => 'Python', 'tier' => 2, 'trend' => 'up'],
            ['name' => 'Golang', 'tier' => 2, 'trend' => 'up'],
            ['name' => 'Symfony', 'tier' => 2],
            ['name' => 'WordPress', 'tier' => 2, 'trend' => 'down'],
            ['name' => 'WooCommerce', 'tier' => 2, 'trend' => 'down'],
            ['name' => 'Magento 2', 'tier' => 2, 'trend' => 'down'],
            ['name' => 'NestJS', 'tier' => 2],
            ['name' => 'MikroORM', 'tier' => 2],
            ['name' => 'Spring', 'tier' => 3, 'trend' => 'down'],
        ],
        'Frontend' => [
            ['name' => 'VueJS', 'image' => 'img/logos/vue.svg', 'tier' => 1],
            ['name' => 'JavaScript', 'image' => 'img/logos/javascript.svg', 'tier' => 1],
            ['name' => 'HTML 5', 'image' => 'img/logos/html5.svg', 'tier' => 1],
            ['name' => 'Bootstrap', 'image' => 'img/logos/bootstrap.svg', 'tier' => 1],
            ['name' => 'TypeScript', 'tier' => 2, 'trend' => 'up'],
            ['name' => 'TailwindCSS', 'tier' => 2, 'trend' => 'up'],
            ['name' => 'jQuery', 'tier' => 2, 'trend' => 'down'],
        ],
        'Infrastructure' => [
            ['name' => 'Linux', 'image' => 'img/logos/linux.svg', 'tier' => 1],
            ['name' => 'Docker', 'image' => 'img/logos/docker.svg', 'tier' => 1],
            ['name' => 'Apache Kafka', 'image' => 'img/logos/kafka.svg', 'tier' => 1],
            ['name' => 'DigitalOcean', 'image' => 'img/logos/digitalocean.svg', 'tier' => 1],
            ['name' => 'AWS', 'image' => 'img/logos/aws.svg', 'tier' => 1],
            ['name' => 'Kubernetes', 'image' => 'img/logos/kubernetes.svg', 'tier' => 1],
            ['name' => 'NGINX', 'image' => 'img/logos/nginx.svg', 'tier' => 1],
            ['name' => 'Grafana', 'image' => 'img/logos/grafana.svg', 'tier' => 1],
            ['name' => 'Apache Tomcat', 'tier' => 3, 'trend' => 'down'],
            ['name' => 'Apache', 'tier' => 3],
        ],
        'Databases' => [
            ['name' => 'MySQL', 'image' => 'img/logos/mysql.svg', 'tier' => 1],
            ['name' => 'Redis', 'image' => 'img/logos/redis.svg', 'tier' => 1],
            ['name' => 'MongoDB', 'tier' => 2, 'trend' => 'up'],
            ['name' => 'PostgreSQL', 'tier' => 2, 'trend' => 'up'],
        ],
        'Testing' => [
            ['name' => 'PHPUnit', 'image' => 'img/logos/phpunit.svg', 'tier' => 1],
            ['name' => 'Cypress', 'image' => 'img/logos/cypress.svg', 'tier' => 1],
            ['name' => 'PestPHP', 'image' => 'img/logos/pest.svg', 'tier' => 1],
            ['name' => 'Playwright', 'tier' => 2, 'trend' => 'up'],
            ['name' => 'Selenium', 'tier' => 3],
        ],
    ],
];
