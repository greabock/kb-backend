<?php

declare(strict_types=1);

namespace App\Services\Search;

use Exception;
use ScoutElastic\IndexConfigurator;
use ScoutElastic\Searchable as BaseSearchable;

/**
 * Trait Searchable
 * @package App\Services\Search
 */
trait Searchable
{
    use BaseSearchable;

    /**
     * Get the index configurator.
     *
     * @return IndexConfigurator
     * @throws \Exception
     */
    public function getIndexConfigurator()
    {
        static $indexConfigurator;

        if (! $indexConfigurator) {
            if (! isset($this->indexConfigurator) || empty($this->indexConfigurator)) {
                throw new Exception(sprintf(
                    'An index configurator for the %s model is not specified.',
                    __CLASS__
                ));
            }

            $indexConfiguratorClass = $this->indexConfigurator;
            $indexConfigurator = new $indexConfiguratorClass(static::class);
        }

        return $indexConfigurator;
    }
}
