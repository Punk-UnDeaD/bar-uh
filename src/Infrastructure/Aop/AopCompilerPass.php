<?php

declare(strict_types=1);

namespace App\Infrastructure\Aop;

use App\Infrastructure\Aop\Attribute\Aop;
use PhpParser\Node;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter;

class AopCompilerPass implements CompilerPassInterface
{
    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container)
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
        /** @psalm-suppress PossiblyNullOperand */
        $dir = $container->getParameter('kernel.cache_dir').\DIRECTORY_SEPARATOR.'Aop';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $parser = new Parser\Php7($lexer);

        foreach ($container->getDefinitions() as $definition) {
            try {
                /** @var class-string $class */
                if (class_exists($class = $definition->getClass() ?? '')) {
                    $reflection = new \ReflectionClass($class);
                    if ($reflection->getAttributes(Aop::class, 2) && ($file = $reflection->getFileName())) {
                        $code = file_get_contents($file) ?: '';
                        /** @var Node[] $smtps */
                        $smtps = $parser->parse($code);
                        $oldTokens = $lexer->getTokens();
                        $traverser = new NodeTraverser();
                        $traverser->addVisitor(new ClassVisitor($className = 'Aop'.md5($file.time())));
                        $traverser->addVisitor(new ConstVisitor());
                        $traverser->addVisitor(new PropertyVisitor());
                        $traverser->addVisitor(new ConstructorVisitor());
                        $traverser->addVisitor(new MethodVisitor($reflection, $parser));
                        $traverser->addVisitor(new CacheResultVisitor($reflection, $parser, $container->getDefinition('services_resetter')));
                        $traverser->addVisitor(new LogClassVisitor($reflection, $parser));
                        $traverser->addVisitor(new LogAfterVisitor($reflection, $parser));
                        $traverser->addVisitor(new RetryVisitor($reflection, $parser));
                        $traverser->addVisitor(new RetryLogVisitor($reflection, $parser));
                        $traverser->addVisitor(new LogBeforeVisitor($reflection, $parser));
                        $traverser->addVisitor(new LogExceptionVisitor($reflection, $parser));
                        $printer = new PrettyPrinter\Standard();
                        $newStmts = $traverser->traverse($smtps);
                        $newCode = $printer->printFormatPreserving($newStmts, $smtps, $oldTokens);
                        $file = $dir.\DIRECTORY_SEPARATOR.$className.'.php';
                        file_put_contents($file, $newCode);
                        /** @psalm-suppress UnresolvableInclude */
                        require_once $file;
                        $definition->setFile($file);
                        $definition->setClass($reflection->getNamespaceName().'\\'.$className);
                    }
                }
            } catch (\Throwable $e) {
//                echo $class.' '.$e->getMessage()."\n";
            }
        }
    }
}
