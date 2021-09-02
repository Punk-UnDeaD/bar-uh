<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopLog;
use App\Infrastructure\Aop\Attribute\AopLogMethod;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserAbstract;

class LogClassVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private \ReflectionClass $reflection,
        private ParserAbstract $parser
    ) {
    }

    public function leaveNode(Node $node): int|null
    {
        if ($node instanceof Class_) {
            if ($attr = $this->reflection->getAttributes(AopLog::class)) {
                $attr = $attr[0];
                $attr = $attr->newInstance();
            } else {
                foreach ($this->reflection->getMethods() as $method) {
                    if ($method->getAttributes(AopLogMethod::class, 2)) {
                        $attr = new AopLog();
                        break;
                    }
                }
            }
            if (!$attr) {
                return null;
            }
            $node->stmts = array_merge($this->getStmps($attr)[0]->stmts, $node->stmts);
        }

        return null;
    }

    private function getStmps(AopLog $attr): array
    {
        return $this->parser->parse(
            <<<PHP
<?php class tmpClass {
    #[\Symfony\Contracts\Service\Attribute\Required] public \Psr\Log\LoggerInterface \$aopLogger;
}
PHP
        );
    }
}
