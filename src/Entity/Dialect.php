<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DialectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DialectRepository::class)]
#[ApiResource]
class Dialect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'dialect', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Province $province = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(Province $province): static
    {
        $this->province = $province;

        return $this;
    }
}
