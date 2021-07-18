<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Material;
use App\Models\Section;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class SectionMaterialModelBuilder
{
    private Repository $cache;

    private Collection $sections;

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke($className)
    {
        $sections = $this->sections ?? $this->sections = Section::all()->keyBy('class_name');

        if ($section = $sections->get($className)) {
            $this->load($section);
        }
    }

    public function load(Section $section): void
    {
        eval($this->get($section));
    }

    public function remember(Section $section): string
    {
        $this->cache->forever($section->class_name, $result = $this->build($section));

        return $result;
    }

    public function build(Section $section): string
    {
        $className = $section->class_name;
        $class = new ClassType($className);

        $class->addExtend(Material::class);

        $class->addProperty('table')
            ->setProtected()
            ->setValue($section->tableName);

        $class->addProperty('fillable')
            ->setProtected()
            ->setValue(['name', ...$section->plainFieldKeys()]);

        $class->addProperty('sectionId')
            ->setProtected()
            ->setStatic()
            ->setValue($section->id);

        return (string)$class;
    }

    private function get(Section $section): string
    {
        return $this->cache->get($section->class_name, fn() => $this->remember($section));
    }
}
