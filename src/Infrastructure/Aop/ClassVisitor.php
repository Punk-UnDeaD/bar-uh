<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ClassVisitor extends NodeVisitorAbstract
{
    public function __construct(private $class)
    {
    }

    public function leaveNode(Node $node): Node|int|null
    {
        if ($node instanceof Node\Stmt\Class_) {
            $node->extends = new Node\Identifier(
                $node->name->name,
                $node->extends ? $node->extends->getAttributes() : []
            );
            $node->name = new Node\Identifier(
                $this->class,
                $node->name ? $node->name->getAttributes() : []
            );

            return $node;
        }

        return null;
    }
}