<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopLogClass;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * @psalm-suppress all
 * @template TT
 * @extends BaseMethodVisitor<TT>
 */
abstract class BaseMethodLogVisitor extends BaseMethodVisitor
{
    protected function getContext(ClassMethod $node): string
    {
        $context = join(
            ', ',
            array_map(
                fn(Param $param) => "'".$param->var->name."' => ".'$'.$param->var->name,
                $node->params
            )
        );

        return "[$context]";
    }

    protected function getClassAttr(): AopLogClass
    {
        return ($this->reflection->getAttributes(AopLogClass::class)[0] ?? null)?->newInstance() ??
            new AopLogClass();
    }
}
