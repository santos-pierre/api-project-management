<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

uses()->group('api-project')->in('Feature/Projects');

uses()->group('api-project-task')->in('Feature/Tasks');
