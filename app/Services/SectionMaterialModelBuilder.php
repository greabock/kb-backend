<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Material;
use App\Models\Section;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;

class SectionMaterialModelBuilder
{
    private Repository $cache;

    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke(string $className)
    {
        $this->load($className);
    }

    public function load(string $section): void
    {
        eval($this->get($section));
    }

    public function remember(?Section $section): string
    {
        if (!$section) {
            return '';
        }

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

        $class->addProperty('casts')
            ->setProtected()
            ->setValue($section->getFieldCasts());

        $class->addProperty('sectionId')
            ->setPublic()
            ->setStatic()
            ->setValue($section->id);

        return (string)$class;
    }

    private function get(string $section): string
    {
        return $this->cache->get($section, fn() => $this->remember(
            Section::where('class_name', $section)->first()
        ));
    }
}
