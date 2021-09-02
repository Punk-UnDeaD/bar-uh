<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopCacheResult;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserAbstract;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Lexer;
use PhpParser\Parser;

class CacheResultVisitor extends NodeVisitorAbstract
{
    public function __construct(private \ReflectionClass $reflection)
    {
    }

    public function leaveNode(Node $node): Node|int|null
    {
        if ($node instanceof Node\Stmt\Class_) {
            foreach ($this->reflection->getMethods() as $method) {
                if ($method->getAttributes(AopCacheResult::class)) {
                    $node->stmts = array_merge($this->getProperty()[0]->stmts, $node->stmts);

                    return $node;
                }
            }

            return $node;
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            if (!$this->reflection->getMethod($node->name->name)->getAttributes(AopCacheResult::class)) {
                return null;
            }
            $return = $node->stmts[0];
            $newReturn = $this->getReturn($node->name->name, $node->params);
            $newReturn[0]->expr->right->expr = $return->expr;
            $node->stmts = $newReturn;
        }

        return null;
    }

    /**
     * @return Node\Stmt[]
     */
    protected function getProperty(): array
    {
        return $this->getParser()->parse('<?php class tmpClass {private $aopCache = [];}');
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

    protected function getReturn($method, $params): array
    {
        $params = join(array_map(fn(Param $param) => '[$'.$param->var->name.']', $params));

        return $this->getParser()->parse(
            "<?php return \$this->aopCache['{$method}']$params ??
            (\$this->aopCache['{$method}']$params = parent::$method());"
        );
    }
}
