<?php

return [
   'database_table_name' => 'settings',

   'cache_key' => 'smart_cms_settings_',

   'database_table_prefix' => 'smart_cms_',

   'mailer' => [
       'host' => 'mail_host',
       'port' => 'mail_port',
       'username' => 'mail_username',
       'password' => 'mail_password',
       'encryption' => 'mail_encryption',
       'from' => 'mail_from',
       'name' => 'mail_name',
   ],

   'main_language' => 'main_lang',
   'is_multi_lang' => 'is_multi_lang',

   'template' => 'template',
   'design' => [
       'header' => 'header_design',
       'footer' => 'footer_design',
   ],

   'company_name' => 'company_name',
   'header_logo' => 'header_logo',
   'footer_logo' => 'footer_logo',
   'favicon' => 'favicon',
   'socials' => 'socials',
   'company_info' => 'company_info',
   'company_slogan' => 'company_slogan',
   'company_description' => 'company_description',
   'country' => 'country',

   'gtm' => 'gtm',
   'title_mod' => 'title_prefix',
   'description_mod' => 'title_suffix',
   'styles' => 'styles',
   'custom_meta' => 'custom_meta',
   'custom_scripts' => 'custom_scripts',
   'indexation' => 'indexation',
   'meta_og' => 'meta_og',
   'meta_twitter' => 'meta_twitter',

   'main_currency' => 'main_currency',
   'front_currency' => 'front_currency',
   'price_round' => 'price_round',

   'menu' => [
       'header' => 'header_menu',
       'footer' => 'footer_menu',
   ],

   'scroll_top' => [
       'margin' => 'scroll_top_margin',
       'position' => 'scroll_top_position',
   ],
];
