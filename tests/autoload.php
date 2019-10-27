<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../vendor/autoload.php';

if (!class_exists(TestCase::class)) {
    class_alias(\PHPUnit\Framework\TestCase::class, TestCase::class);
}
