<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopLog;
use App\Infrastructure\Aop\Attribute\AopLogClass;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserAbstract;

/** @psalm-suppress all */
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
            if ($attr = $this->reflection->getAttributes(AopLogClass::class)) {
                $attr = $attr[0];
                $attr = $attr->newInstance();
            } else {
                foreach ($this->reflection->getMethods() as $method) {
                    if ($method->getAttributes(AopLog::class, 2)) {
                        $attr = new AopLogClass();
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

    private function getStmps(AopLogClass $attr): array
    {
        return $this->parser->parse(
            <<<PHP
<?php class tmpClass {
    #[\Symfony\Contracts\Service\Attribute\Required] public \Psr\Log\LoggerInterface \${$attr->channel}Logger;
}
PHP
        );
    }
}
