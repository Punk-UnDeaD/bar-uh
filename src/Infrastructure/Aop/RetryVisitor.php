<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopRetry;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserAbstract;

class RetryVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private \ReflectionClass $reflection,
        private ParserAbstract $parser,
    ) {
    }

    public function leaveNode(Node $node): int|null
    {
        if ($node instanceof ClassMethod) {
            if (!$attr = $this->reflection->getMethod($node->name->name)->getAttributes(AopRetry::class)) {
                return null;
            }
            $attr = $attr[0]->newInstance();
            $stmts = $this->getStmps($attr);
            $hasReturn = end($node->stmts) instanceof Node\Stmt\Return_;
            $stmts[1]->stmts[0]->stmts = array_merge($node->stmts, $hasReturn ? [] : $stmts[1]->stmts[0]->stmts);
            $node->stmts = $stmts;
        }

        return null;
    }

    private function getStmps(AopRetry $attr): array
    {
        return $this->parser->parse(
            "<?php \$count = {$attr->count};while(\$count--){try {return;}catch(\Throwable \$e){ }usleep({$attr->usleep});}throw \$e;"
        );
    }
}
