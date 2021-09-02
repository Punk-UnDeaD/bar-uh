<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class ConstVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node): int|null
    {
        if ($node instanceof ClassConst) {
            return NodeTraverser::REMOVE_NODE;
        }

        return null;
    }
}
