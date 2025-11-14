<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DialectPageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DialectPageRepository::class)]
#[ApiResource]
class DialectPage
{
    #[Groups(['dialect:create', 'dialect:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['dialect:create', 'dialect:collection'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'pages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dialect $dialect = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDialect(): ?Dialect
    {
        return $this->dialect;
    }

    public function setDialect(?Dialect $dialect): static
    {
        $this->dialect = $dialect;

        return $this;
    }
}
