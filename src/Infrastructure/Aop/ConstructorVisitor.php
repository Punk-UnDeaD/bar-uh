<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class ConstructorVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node): int|null
    {
        if ($node instanceof ClassMethod && '__construct' === $node->name->name) {
            return NodeTraverser::REMOVE_NODE;
        }

        return null;
    }
}
