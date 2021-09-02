<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\Aop;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserAbstract;

class MethodVisitor extends NodeVisitorAbstract
{
    public function __construct(private \ReflectionClass $reflection)
    {
    }

    public function leaveNode(Node $node): Node|int|null
    {
        if ($node instanceof ClassMethod) {
            if ($node->isPrivate() || $node->isFinal()) {
                return NodeTraverser::REMOVE_NODE;
            }
            if (!$this->reflection->getMethod($node->name->name)->getAttributes(Aop::class, 2)) {
                return NodeTraverser::REMOVE_NODE;
            }
            $node->stmts = $this->getSmtps(
                $node->name->name,
                $node->params,
                noReturn: 'void' === $node->returnType->name
            );

            return $node;
        }

        return null;
    }

    /**
     * @param         $method
     * @param Param[] $args
     *
     * @return \PhpParser\Node\Stmt[]|null
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

        return $this->getParser()->parse(
            <<<PHP
<?php $return parent::$method($args); 
PHP
        );
    }

    public function getParser(): ParserAbstract
    {
        $lexer = new Lexer\Emulative(
            [
                'usedAttributes' => [
                    'comments',
                    'startLine', 'endLine',
                    'startTokenPos', 'endTokenPos',
                ],
            ]
        );

        return new Parser\Php7($lexer);
    }
}
