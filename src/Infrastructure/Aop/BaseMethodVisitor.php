<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserAbstract;

/**
 * @psalm-suppress all
 * @template T of object
 */
abstract class BaseMethodVisitor extends NodeVisitorAbstract
{
    public const ATTR = '';

    public function __construct(
        protected \ReflectionClass $reflection,
        protected ParserAbstract $parser,
    ) {
    }

    public function leaveNode(Node $node): void
    {
        if (!$node instanceof ClassMethod) {
            return;
        }
        if ($attr = $this->reflection->getMethod($node->name->name)->getAttributes(static::ATTR)) {
            /** @var T $attr */
            $attr = $attr[0]->newInstance();
            $this->processNode($node, $attr);
        }
    }

    /**
     * @param T $attr
     */
    abstract protected function processNode(ClassMethod $node, $attr): void;
}
