<?php

declare(strict_types=1);

namespace App\Infrastructure\Psalm;

use PhpParser\Node\Stmt\Class_;
use Psalm\Plugin\EventHandler\AfterClassLikeVisitInterface;
use Psalm\Plugin\EventHandler\Event\AfterClassLikeVisitEvent;
use Symfony\Contracts\Service\Attribute\Required;

class RequiredPropertyHandler implements AfterClassLikeVisitInterface
{
    public static function afterClassLikeVisit(
        AfterClassLikeVisitEvent $event
    ) {
        $stmt = $event->getStmt();
        $storage = $event->getStorage();

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
                if ($docCommend && str_contains(strtoupper($docCommend), '@REQUIRED')) {
                    $storage->initialized_properties[$name] = true;
                }
            }
        }
    }
}
