<?php

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('all models are classes')
    ->expect('Src\Models')
    ->toBeClasses();

arch('all traits are traits')
    ->expect('Src\Traits')
    ->toBeTraits();

arch('all models extends base model')->expect('Src\Models')->toExtend('SmartCms\Core\Models\BaseModel');

arch('all commands extends base command')
    ->expect('Src\Commands')
    ->toExtend('Illuminate\Console\Command');
