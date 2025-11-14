<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PostPhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostPhotoRepository::class)]
#[ApiResource]
class PostPhoto
{
    #[Groups(['post:write', 'post:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['post:write', 'post:collection'])]
    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\ManyToOne(inversedBy: 'photos', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[Groups(['post:write', 'post:collection'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Groups(['post:write', 'post:collection'])]
    /**
     * @var Collection<int, PostPhotoCategory>
     */
    #[ORM\ManyToMany(targetEntity: PostPhotoCategory::class, inversedBy: 'photos')]
    private Collection $categories;

    #[Groups(['post:write'])]
    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[Groups(['post:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $locationLabel = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, PostPhotoCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(PostPhotoCategory $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(PostPhotoCategory $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getLocationLabel(): ?string
    {
        return $this->locationLabel;
    }

    public function setLocationLabel(?string $locationLabel): static
    {
        $this->locationLabel = $locationLabel;

        return $this;
    }
}
