<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Facade;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        Facade::setFacadeApplication($app);
        $app->make(ConsoleKernel::class)->bootstrap();
        return $app;
    }
}
