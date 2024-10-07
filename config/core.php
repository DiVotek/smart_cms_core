<?php

$prefix = 'smart_cms_';

return [
    'database_table_prefix' => 'smart_cms_',

    'main_language' => $prefix . 'main_lang',
    'is_multi_lang' => $prefix . 'is_multi_lang',

    'template' => $prefix . 'template',

    'design' => [
        'header' => $prefix . 'header_design',
        'footer' => $prefix . 'footer_design',
    ],

    // core
    'branding' => [
        'logo' => $prefix . 'branding_logo',
        'footer_logo' => $prefix . 'branding_footer_logo',
        'favicon' => $prefix . 'branding_favicon',
        'socials' => $prefix . 'branding_socials',
    ],

    'header_logo' => $prefix . 'header_logo',
    'footer_logo' => $prefix . 'footer_logo',
    'favicon' => $prefix . 'favicon',

    // contacts
    'company_name' => $prefix . 'company_name',
    'company_slogan' => $prefix . 'company_slogan',
    'company_description' => $prefix . 'company_description',
    'country' => $prefix . 'country',
    'company_info' => $prefix . 'company_info',
    'socials' => $prefix . 'socials',

    // seo
    'gtm' => $prefix . 'gtm',
    'title_mod' => $prefix . 'title_prefix',
    'description_mod' => $prefix . 'title_suffix',
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

    'scroll_top' => [
        'margin' => $prefix . 'scroll_top_margin',
        'position' => $prefix . 'scroll_top_position',
    ],

    'static_page_template' => $prefix . 'static_page_template',

    'mail' => [
        'admin_emails' => $prefix . 'mail_admin_emails',
        'host' => $prefix . 'mail_host',
        'port' => $prefix . 'mail_port',
        'username' => $prefix . 'mail_username',
        'password' => $prefix . 'mail_password',
        'encryption' => $prefix . 'mail_encryption',
        'from' => $prefix . 'mail_from',
        'name' => $prefix . 'mail_name',
    ],
];
