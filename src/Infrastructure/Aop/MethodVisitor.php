<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\Aop;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserAbstract;

/** @psalm-suppress all */
class MethodVisitor extends NodeVisitorAbstract
{
    public function __construct(
        private \ReflectionClass $reflection,
        private ParserAbstract $parser,
    ) {
    }

    public function beforeTraverse(array $nodes)
    {
    }

    public function leaveNode(Node $node): int|null
    {
        if ($node instanceof ClassMethod) {
            if ($node->isFinal()) {
                return NodeTraverser::REMOVE_NODE;
            }
//            if($node->isPrivate()){
//                $node->flags = $node->flags ^ Class_::MODIFIER_PRIVATE ^ Class_::MODIFIER_PROTECTED;
//            }
            if (!$this->reflection->getMethod($node->name->name)->getAttributes(Aop::class, 2)) {
                return NodeTraverser::REMOVE_NODE;
            }
            $node->stmts = $this->getSmtps(
                $node->name->name,
                $node->params,
                noReturn: $node->returnType instanceof Identifier && 'void' === $node->returnType->name
            );

            return null;
        }

        return null;
    }

    /**
     * @param         $method
     * @param Param[] $args
     *
     * @return Node\Stmt[]
     */
    public function getSmtps(string $method, array $args, bool $noReturn = true): ?array
    {
        $args = join(
            ', ',
            array_map(
                fn(Param $arg) => ($arg->variadic ? '...' : '').'$'.$arg->var->name,
                $args
            )
        );
        $return = $noReturn ? '' : 'return';

        return $this->parser->parse("<?php $return parent::$method($args);");
    }

    protected function getPrivateFix(string $method)
    {
        (new \ReflectionClass($this->reflection->getName()))->getMethod($method)->setAccessible(true);

        return $this->parser->parse('<?php ;');
    }
}
