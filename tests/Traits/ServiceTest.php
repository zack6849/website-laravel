<?php

namespace Tests\Traits;

trait ServiceTest
{

    public function setService($service): void
    {
        $this->service = resolve($service);
    }

    public function reloadService(): void
    {
        $this->service = resolve(get_class($this->service));
    }
}
