<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopLog;
use App\Infrastructure\Aop\Attribute\AopLogAfter;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserAbstract;

class LogAfterVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private \ReflectionClass $reflection,
        private ParserAbstract $parser,
    ) {
    }

    public function leaveNode(Node $node): int|null
    {
        if ($node instanceof ClassMethod) {
            if (!$attr = $this->reflection->getMethod($node->name->name)->getAttributes(AopLogAfter::class)) {
                return null;
            }
            $attr = $attr[0]->newInstance();
            $classAttr = ($this->reflection->getAttributes(AopLog::class)[0] ?? null)?->newInstance() ?? new AopLog();
            if (end($node->stmts) instanceof Node\Stmt\Return_) {
                $return = array_pop($node->stmts);
            } else {
                $return = null;
            }
            $node->stmts = array_merge($node->stmts, $this->getStmps($node, $attr, $classAttr, $return));
        }

        return null;
    }

    private function getStmps(ClassMethod $node, AopLogAfter $attr, AopLog $classAttr, ?Return_ $return): array
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

        if ($return) {
            $r = $this->parser->parse('<?php $return = md5(\'\');');
            $r[0]->expr->expr = $return->expr;
            $context = '[\'return\'=> $return] + '.$context;
        } else {
            $r = [];
        }

        $code = "<?php \$this->aopLogger->{$logLevel}({$message}, {$context});";
        if ($return) {
            $code .= 'return $return;';
        }

        return array_merge($r, $this->parser->parse($code));
    }
}
