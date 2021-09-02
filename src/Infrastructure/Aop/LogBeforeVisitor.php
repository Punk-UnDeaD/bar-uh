<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopLog;
use App\Infrastructure\Aop\Attribute\AopLogBefore;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserAbstract;

class LogBeforeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private \ReflectionClass $reflection,
        private ParserAbstract $parser,
    ) {
    }

    public function leaveNode(Node $node): int|null
    {
        if ($node instanceof ClassMethod) {
            if (!$attr = $this->reflection->getMethod($node->name->name)->getAttributes(AopLogBefore::class)) {
                return null;
            }
            $attr = $attr[0]->newInstance();
            $classAttr = ($this->reflection->getAttributes(AopLog::class)[0] ?? null)?->newInstance() ?? new AopLog();
            $node->stmts = array_merge($this->getStmps($node, $attr, $classAttr), $node->stmts);
        }

        return null;
    }

    private function getStmps(ClassMethod $node, AopLogBefore $attr, AopLog $classAttr): array
    {
        $context = join(
            ', ',
            array_map(
                fn(Param $param) => "'".$param->var->name."' => ".'$'.$param->var->name,
                $node->params
            )
        );
        $context = "[$context]";
        $logLevel = $attr->level ?: $classAttr->level;
        $message = var_export($classAttr->prefix.$attr->message, true);

        return $this->parser->parse(
            <<<PHP
<?php
    \$this->aopLogger->$logLevel($message, $context);

PHP
        );
    }
}
