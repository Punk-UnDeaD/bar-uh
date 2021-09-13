<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopRetry;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * @psalm-suppress all
 * @extends BaseMethodVisitor<AopRetry>
 */
class RetryVisitor extends BaseMethodVisitor
{
    public const ATTR = AopRetry::class;

    protected function processNode(ClassMethod $node, $attr): void
    {
        $stmts = $this->getStmps($attr);
        $hasReturn = end($node->stmts) instanceof Node\Stmt\Return_;
        $stmts[0]->stmts[0]->stmts = array_merge($node->stmts, $hasReturn ? [] : $stmts[0]->stmts[0]->stmts);
        $node->stmts = $stmts;
    }

    /**
     * @return Node\Stmt[]
     */
    private function getStmps(AopRetry $attr): array
    {
        $usleep = $attr->msleep * 1000;

        return $this->parser->parse(
            <<<PHP
<?php 
    for(\$attempt = 0;;\$attempt++){
        try {return;}
        catch(\Throwable \$e) {
            if(\$attempt === {$attr->count}) throw \$e;
        }
        usleep($usleep + mt_rand(-100, 100));
    }
PHP
        );
    }
}
