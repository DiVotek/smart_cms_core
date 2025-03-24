<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use SmartCms\Core\Traits\HasStatus;

/**
 * Class Language
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the language.
 * @property string $slug The slug of the language.
 * @property string $locale The locale code of the language.
 * @property bool $status The status of the language.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \Illuminate\Database\Eloquent\Collection|\SmartCms\Core\Models\Seo[] $seos The SEO entries for this language.
 * @property-read \Illuminate\Database\Eloquent\Collection|\SmartCms\Core\Models\Translation[] $translations The translations for this language.
 */
class Language extends BaseModel
{
    use HasFactory;
    use HasStatus;

    protected $guarded = [];

    public const LANGUAGES = [
        'English' => [
            'name' => 'English',
            'slug' => 'en',
            'locale' => 'en_US',
            'status' => '1',
        ],
        'Русский' => [
            'name' => 'Русский',
            'slug' => 'ru',
            'locale' => 'ru_RU',
            'status' => '1',
        ],
        'Українська' => [
            'name' => 'Українська',
            'slug' => 'uk',
            'locale' => 'uk_UA',
            'status' => '1',
        ],
        'Spanish' => [
            'name' => 'Spanish',
            'slug' => 'es',
            'locale' => 'es_ES',
            'status' => '1',
        ],
        'French' => [
            'name' => 'French',
            'slug' => 'fr',
            'locale' => 'fr_FR',
            'status' => '1',
        ],
        'German' => [
            'name' => 'German',
            'slug' => 'de',
            'locale' => 'de_DE',
            'status' => '1',
        ],
        'Poland' => [
            'name' => 'Poland',
            'slug' => 'pl',
            'locale' => 'pl_PL',
            'status' => '1',
        ],
    ];
}
