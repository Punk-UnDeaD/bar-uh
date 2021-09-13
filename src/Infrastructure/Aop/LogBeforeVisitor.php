<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopLogBefore;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * @extends BaseMethodLogVisitor<AopLogBefore>
 * @psalm-suppress all
 */
class LogBeforeVisitor extends BaseMethodLogVisitor
{
    public const ATTR = AopLogBefore::class;

    /**
     * @param AopLogBefore $attr
     */
    protected function processNode(ClassMethod $node, $attr): void
    {
        $node->stmts = array_merge($this->getStmps($node, $attr), $node->stmts);
    }

    private function getStmps(ClassMethod $node, AopLogBefore $attr): array
    {
        $context = $this->getContext($node);
        $classAttr = $this->getClassAttr();
        $logLevel = $attr->level ?: $classAttr->level;
        $message = var_export($classAttr->prefix.$attr->message, true);

        return $this->parser->parse("<?php \$this->{$classAttr->channel}Logger->$logLevel($message, $context);");
    }
}
