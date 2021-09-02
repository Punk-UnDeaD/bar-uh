<?php

declare(strict_types=1);

namespace App\Infrastructure\Psalm;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use Psalm\Codebase;
use Psalm\FileSource;
use Psalm\Plugin\Hook\AfterClassLikeVisitInterface;
use Psalm\Storage\ClassLikeStorage;
use Symfony\Contracts\Service\Attribute\Required;

class RequiredPropertyHandler implements AfterClassLikeVisitInterface
{
    public static function afterClassLikeVisit(
        ClassLike $stmt,
        ClassLikeStorage $storage,
        FileSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = []
    ) {
        if (!$stmt instanceof Class_) {
            return;
        }
        $reflection = null;
        foreach ($storage->properties as $name => $property) {
            if (!empty($storage->initialized_properties[$name])) {
                continue;
            }
            foreach ($property->attributes as $attribute) {
                if (Required::class === $attribute->fq_class_name) {
                    $storage->initialized_properties[$name] = true;
                    continue 2;
                }
            }
            /** @phpstan-var class-string $class */
            $class = $storage->name;
            if (!class_exists($class)) {
                return;
            }
            $reflection = $reflection ?? new \ReflectionClass($class);
            if ($reflection->hasProperty($name)) {
                $reflectionProperty = $reflection->getProperty($name);
                $docCommend = $reflectionProperty->getDocComment();
                if ($docCommend && false !== strpos(strtoupper($docCommend), '@REQUIRED')) {
                    $storage->initialized_properties[$name] = true;
                }
            }
        }
    }
}
