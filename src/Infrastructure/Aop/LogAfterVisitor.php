<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopLogAfter;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;

/**
 * @psalm-suppress all
 * @extends BaseMethodLogVisitor<AopLogAfter>
 */
class LogAfterVisitor extends BaseMethodLogVisitor
{
    public const ATTR = AopLogAfter::class;

    /**
     * @param AopLogAfter $attr
     */
    protected function processNode(ClassMethod $node, $attr): void
    {
        if (end($node->stmts) instanceof Node\Stmt\Return_) {
            $return = array_pop($node->stmts);
        } else {
            $return = null;
        }
        $node->stmts = array_merge($node->stmts, $this->getStmps($node, $attr, $return));
    }

    private function getStmps(ClassMethod $node, AopLogAfter $attr, ?Return_ $return): array
    {
        $classAttr = $this->getClassAttr();
        $context = $this->getContext($node);
        $logLevel = $attr->level ?: $classAttr->level;
        $message = var_export($classAttr->prefix.$attr->message, true);

        if ($return) {
            $r = $this->parser->parse('<?php $return = md5(\'\');');
            $r[0]->expr->expr = $return->expr;
            $context = '[\'return\'=> $return] + '.$context;
        } else {
            $r = [];
        }

        $code = "<?php \$this->{$classAttr->channel}Logger->{$logLevel}({$message}, {$context});";
        if ($return) {
            $code .= 'return $return;';
        }

        return array_merge($r, $this->parser->parse($code));
    }
}
