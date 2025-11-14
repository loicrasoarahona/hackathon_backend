<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DialectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, DialectPage>
     */
    #[ORM\OneToMany(targetEntity: DialectPage::class, mappedBy: 'dialect')]
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
