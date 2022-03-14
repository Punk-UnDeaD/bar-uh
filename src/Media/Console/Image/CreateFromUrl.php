<?php

declare(strict_types=1);

namespace App\Media\Console\Image;

use App\Media\Entity\Image;
use App\Media\UseCase\Image\CreateFromUrl\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as CliCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(
    name: 'media:image:createFromUrl',
    description: 'Create image from url.',
)]
class CreateFromUrl extends CliCommand
{
    #[Required]
    public MessageBusInterface $bus;

    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @phpstan-var string $url */
        $url = $input->getArgument('url');
        $env = $this->bus->dispatch(new Command($url));
        /** @var mixed $res */
        $res = $env->last(HandledStamp::class)?->getResult();
        if (!$res instanceof Image) {
            $output->write('something wrong');

            return CliCommand::INVALID;
        }
        $output->writeln("Image `{$res->getId()}` saved.");

        return CliCommand::SUCCESS;
    }
}
