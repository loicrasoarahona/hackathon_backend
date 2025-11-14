<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\DialectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DialectRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['dialect:create']]),
        new GetCollection(normalizationContext: ['groups' => ['dialect:collection']]),
        new Post(denormalizationContext: ['groups' => ['dialect:create']]),
        new Put(),
        new Patch(),
        new Delete(),
    ]
)]
class Dialect
{
    #[Groups(['dialect:create', 'dialect:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['dialect:create', 'dialect:collection'])]
    #[ORM\OneToOne(inversedBy: 'dialect', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Province $province = null;

    #[Groups(['dialect:create', 'dialect:collection'])]
    /**
     * @var Collection<int, DialectPage>
     */
    #[ORM\OneToMany(targetEntity: DialectPage::class, mappedBy: 'dialect', cascade: ['persist'])]
    private Collection $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, DialectPage>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(DialectPage $page): static
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
            $page->setDialect($this);
        }

        return $this;
    }

    public function removePage(DialectPage $page): static
    {
        if ($this->pages->removeElement($page)) {
            // set the owning side to null (unless already changed)
            if ($page->getDialect() === $this) {
                $page->setDialect(null);
            }
        }

        return $this;
    }
}
