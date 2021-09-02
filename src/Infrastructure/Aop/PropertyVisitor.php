<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class PropertyVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node): int|null
    {
        if ($node instanceof Property) {
            return NodeTraverser::REMOVE_NODE;
        }

        return null;
    }
}
