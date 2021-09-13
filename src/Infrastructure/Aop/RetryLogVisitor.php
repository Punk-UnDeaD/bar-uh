<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopRetryLog;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * @psalm-suppress all
 * @extends BaseMethodLogVisitor<AopRetryLog>
 */
class RetryLogVisitor extends BaseMethodLogVisitor
{
    public const ATTR = AopRetryLog::class;

    protected function processNode(ClassMethod $node, $attr): void
    {
        $stmts = $this->getStmps($node, $attr);
        $hasReturn = end($node->stmts) instanceof Node\Stmt\Return_;
        $stmts[0]->stmts[0]->stmts = array_merge($node->stmts, $hasReturn ? [] : $stmts[0]->stmts[0]->stmts);
        $node->stmts = $stmts;
    }

    private function getStmps(ClassMethod $node, AopRetryLog $attr): array
    {
        $context = $this->getContext($node);
        $classAttr = $this->getClassAttr();
        $logLevel = $attr->level ?: $classAttr->level;
        $message = var_export($classAttr->prefix.$attr->message, true);
        $usleep = $attr->msleep * 1000;

        return $this->parser->parse(
            <<<PHP
<?php  
    for(\$attempt = 1;;\$attempt++){
        try {return;}
        catch(\Throwable \$e){
            if(\$attempt === {$attr->count}) throw \$e;
            usleep($usleep + mt_rand(-100, 100));
            \$this->{$classAttr->channel}Logger->$logLevel($message, ['attempt' => \$attempt] + $context);
        }
    }  
PHP
        );
    }
}
