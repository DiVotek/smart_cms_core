<?php

$prefix = 'smart_cms_';

return [
    'database_table_prefix' => $prefix,

    'main_language' => $prefix . 'main_lang',
    'is_multi_lang' => $prefix . 'is_multi_lang',
    'additional_languages' => $prefix . 'additional_langs',
    'front_languages' => $prefix . 'front_langs',

    'template' => $prefix . 'template',

    'branding' => [
        'logo' => $prefix . 'branding_logo',
        'favicon' => $prefix . 'branding_favicon',
        'socials' => $prefix . 'branding_socials',
        'company_name' => $prefix . 'branding_company_name',
    ],

    // contacts
    'company_name' => $prefix . 'company_name',
    'company_slogan' => $prefix . 'company_slogan',
    'company_description' => $prefix . 'company_description',
    'country' => $prefix . 'country',
    'company_info' => [
        'phones' => $prefix . 'company_info_phones',
        'addresses' => $prefix . 'company_info_addresses',
        'emails' => $prefix . 'company_info_emails',
    ],
    'socials' => $prefix . 'socials',

    // seo
    'gtm' => $prefix . 'gtm',
    'title' => [
        'prefix' => $prefix . 'title_prefix',
        'suffix' => $prefix . 'title_suffix',
    ],
    'description' => [
        'prefix' => $prefix . 'description_prefix',
        'suffix' => $prefix . 'description_suffix',
    ],
    'styles' => $prefix . 'styles',
    'custom_meta' => $prefix . 'custom_meta',
    'custom_scripts' => $prefix . 'custom_scripts',
    'indexation' => $prefix . 'indexation',
    'meta_og' => $prefix . 'meta_og',
    'meta_twitter' => $prefix . 'meta_twitter',

    'menu' => [
        'header' => $prefix . 'header_menu',
        'footer' => $prefix . 'footer_menu',
    ],

    'static_page_template' => $prefix . 'static_page_template',

    'mail' => [
        'provider' => $prefix . 'mail_provider',
        'admin_emails' => $prefix . 'mail_admin_emails',
        'host' => $prefix . 'mail_host',
        'port' => $prefix . 'mail_port',
        'username' => $prefix . 'mail_username',
        'password' => $prefix . 'mail_password',
        'encryption' => $prefix . 'mail_encryption',
        'from' => $prefix . 'mail_from',
        'name' => $prefix . 'mail_name',
    ],
    'telegram' => [
        'token' => $prefix . 'telegram_token',
        'bot_username' => $prefix . 'telegram_bot_username',
    ],
    'theme' => $prefix . 'theme',
    'header' => $prefix . 'header',
    'footer' => $prefix . 'footer',
    'default_variables' => $prefix . 'default_variables',

    'og_type' => $prefix . 'og_type',
    'og_image' => $prefix . 'og_image',
    'version' => $prefix . 'version',
    'kit' => [
        'prefix' => '',
        'components' => [
            \SmartCms\Core\Components\Heading::class => 'heading',
            \SmartCms\Core\Components\Description::class => 'description',
            \SmartCms\Core\Components\Form::class => 'form',
            \SmartCms\Core\Components\Image::class => 'image',
            \SmartCms\Core\Components\Link::class => 'link',
            \SmartCms\Core\Components\Notifications::class => 'notifications',
            \SmartCms\Core\Components\Date::class => 'date',
            \SmartCms\Core\Components\Modal::class => 'modal',
            \SmartCms\Core\Components\Slide::class => 'slide',
            \SmartCms\Core\Components\Slider::class => 'slider',
        ],
    ],
    'resize' => [
        'enabled' => $prefix . 'resize_enabled',
        'two_sides' => $prefix . 'resize_two_sides',
        'autoscale' => $prefix . 'resize_autoscale',
        'crop' => $prefix . 'resize_crop',
    ],
    'no_image' => $prefix . 'no_image',
    'layouts' => [
        'header' => $prefix . 'header',
        'footer' => $prefix . 'footer',
    ],
    'system' => [
        'debug' => $prefix . 'debug',
        'maintenance' => $prefix . 'maintenance',
    ],
];
