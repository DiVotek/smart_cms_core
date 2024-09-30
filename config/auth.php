<?php

use SmartCms\Core\Models\Admin;

return [
   'guards' => [
      'admin' => [
         'driver' => 'session',
         'provider' => 'admin',
      ],
   ],
   'providers' => [
      'admin' => [
         'driver' => 'eloquent',
         'model' => Admin::class,
      ],
   ],
];
