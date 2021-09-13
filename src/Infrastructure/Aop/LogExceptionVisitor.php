<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopLogException;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * @psalm-suppress all
 * @extends BaseMethodLogVisitor<AopLogException>
 */
class LogExceptionVisitor extends BaseMethodLogVisitor
{
    public const ATTR = AopLogException::class;

    /**
     * @param AopLogException $attr
     */
    protected function processNode(ClassMethod $node, $attr): void
    {
        $stmts = $this->getStmps($node, $attr);
        $stmts[0]->stmts = $node->stmts;
        $node->stmts = $stmts;
    }

    private function getStmps(ClassMethod $node, AopLogException $attr): array
    {
        $context = $this->getContext($node);
        $classAttr = $this->getClassAttr();
        $logLevel = $attr->level ?: $classAttr->level;
        $message = var_export($classAttr->prefix.$attr->message, true);

        return $this->parser->parse(
            <<<PHP
<?php  
  try {return;}
  catch(\Throwable \$exception){
   \$this->{$classAttr->channel}Logger->$logLevel($message, ['exception' => \$exception] + $context);
   throw \$exception;
  }
PHP
        );
    }
}
