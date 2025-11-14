<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\PostPhotoCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ORM\Entity(repositoryClass: PostPhotoCategoryRepository::class)]
#[ApiFilter(BooleanFilter::class, properties: ['isDisplayable'])]
#[ApiResource]
class PostPhotoCategory
{
    #[Groups(['post:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['post:collection'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, PostPhoto>
     */
    #[ORM\ManyToMany(targetEntity: PostPhoto::class, mappedBy: 'categories')]
    private Collection $photos;

    #[ORM\Column]
    private ?bool $isDisplayable = null;

    #[ORM\Column(length: 255)]
    private ?string $nameFr = null;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, PostPhoto>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(PostPhoto $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->addCategory($this);
        }

        return $this;
    }

    public function removePhoto(PostPhoto $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            $photo->removeCategory($this);
        }

        return $this;
    }

    public function isDisplayable(): ?bool
    {
        return $this->isDisplayable;
    }

    public function setIsDisplayable(bool $isDisplayable): static
    {
        $this->isDisplayable = $isDisplayable;

        return $this;
    }

    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }

    public function setNameFr(string $nameFr): static
    {
        $this->nameFr = $nameFr;

        return $this;
    }
}
