<?php

namespace App\Services\Search;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class MaterialIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
        'filter' => [
            'ru_stop' => ['type' => 'stop', 'stopwords' => '_russian_'],
            'ru_stemmer' => ['type' => 'stemmer', 'language' => 'russian'],
        ],
        'analyzer' => [
            'default' => [
                'type' => 'custom',
                'char_filter' => ['html_strip'],
                'tokenizer' => 'standard',
                'filter' => ['lowercase', 'stop', 'ru_stop', 'ru_stemmer'],
            ],
        ],
    ];

    public function __construct(string $class)
    {
        $this->name = $class::$sectionId;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return config('scout.prefix') . $this->name;
    }
}
