<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new \ApiPlatform\Metadata\Post(
            denormalizationContext: ['groups' => ['place:create']]
        ),
        new Put(),
        new Patch(),
        new Delete()
    ]
)]
class Place
{
    #[Groups(['place:create'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['place:create'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['place:create'])]
    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[Groups(['place:create'])]
    #[ORM\ManyToOne(inversedBy: 'places')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[Groups(['place:create'])]
    /**
     * @var Collection<int, PlacePhoto>
     */
    #[ORM\OneToMany(targetEntity: PlacePhoto::class, mappedBy: 'place', cascade: ['persist'])]
    private Collection $photos;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'place')]
    private Collection $posts;

    /**
     * @var Collection<int, PlaceEvent>
     */
    #[ORM\OneToMany(targetEntity: PlaceEvent::class, mappedBy: 'place')]
    private Collection $placeEvents;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->placeEvents = new ArrayCollection();
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, PlacePhoto>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(PlacePhoto $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setPlace($this);
        }

        return $this;
    }

    public function removePhoto(PlacePhoto $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getPlace() === $this) {
                $photo->setPlace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setPlace($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getPlace() === $this) {
                $post->setPlace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlaceEvent>
     */
    public function getPlaceEvents(): Collection
    {
        return $this->placeEvents;
    }

    public function addPlaceEvent(PlaceEvent $placeEvent): static
    {
        if (!$this->placeEvents->contains($placeEvent)) {
            $this->placeEvents->add($placeEvent);
            $placeEvent->setPlace($this);
        }

        return $this;
    }

    public function removePlaceEvent(PlaceEvent $placeEvent): static
    {
        if ($this->placeEvents->removeElement($placeEvent)) {
            // set the owning side to null (unless already changed)
            if ($placeEvent->getPlace() === $this) {
                $placeEvent->setPlace(null);
            }
        }

        return $this;
    }
}
