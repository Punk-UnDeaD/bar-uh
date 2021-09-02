<?php

declare(strict_types=1);

namespace App\Media\Entity;

use App\Infrastructure\Entity\Field\TagTrait;
use App\Media\Repository\VideoRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class), ORM\HasLifecycleCallbacks]
class Video
{
    use Field\IdTrait;
    use Field\DateTrait;
    use TagTrait;

    #[ORM\Column(type: 'string')]
    private string $uri;

    #[ORM\Column(type: 'string')]
    private string $title;

    public function __construct(string $id, string $title, string $uri, ?DateTimeImmutable $date = null)
    {
        $this->id = $id;
        $this->uri = $uri;
        $this->date = $date ?? (new DateTimeImmutable());
        $this->title = $title;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getRatio(): ?string
    {
        /** @var ?string $ratio */
        $ratio = $this->data['ratio'] ?? null;

        return $ratio;
    }

    public function setRatio(string $ratio): self
    {
        $this->data['ratio'] = $ratio;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
