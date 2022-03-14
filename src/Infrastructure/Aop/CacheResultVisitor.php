<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\AopCacheResult;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\ParserAbstract;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @psalm-suppress all
 * @extends BaseMethodVisitor<AopCacheResult>
 */
class CacheResultVisitor extends BaseMethodVisitor
{
    public const ATTR = AopCacheResult::class;

    private Definition $definition;

    public function __construct(
        \ReflectionClass $reflection,
        ParserAbstract $parser,
        Definition $definition
    ) {
        $this->reflection = $reflection;
        $this->parser = $parser;
        $this->definition = $definition;
    }

    public function leaveNode(Node $node): void
    {
        if ($node instanceof Node\Stmt\Class_) {
            foreach ($this->reflection->getMethods() as $method) {
                if ($method->getAttributes(AopCacheResult::class)) {
                    $node->stmts = array_merge(
                        $this->getProperty()[0]->stmts,
                        $node->stmts,
                        $this->getMethod()[0]->stmts
                    );
                    if (!$this->definition->isAbstract()) {
                        $this->definition->addTag('kernel.reset', ['method' => 'aopReset']);
                    }

                    return;
                }
            }
        } else {
            parent::leaveNode($node);
        }
    }

    /**
     * @return Node\Stmt[]
     */
    protected function getProperty(): array
    {
        return $this->parser->parse('<?php class tmpClass {private $aopCache = [];}');
    }

    protected function getMethod()
    {
        return $this->parser->parse(
            '<?php 
         class tmpClass {
            public function aopReset()
            {
                $this->aopCache=[];
            }
         }'
        );
    }

    protected function processNode(ClassMethod $node, $attr): void
    {
        $return = $node->stmts[0];
        $newReturn = $this->getReturn($node->name->name, $node->params);
        $newReturn[0]->expr->right->expr = $return->expr;
        $node->stmts = $newReturn;
    }

    protected function getReturn($method, $params): array
    {
        $params = join(array_map(fn(Param $param) => '[$'.$param->var->name.']', $params));

        return $this->parser->parse(
            "<?php return \$this->aopCache['{$method}']$params ??
            (\$this->aopCache['{$method}']$params = parent::$method());"
        );
    }
}
