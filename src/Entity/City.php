<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiFilter(SearchFilter::class, properties: ['province' => 'exact', 'name' => 'partial'])]
#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['city:collection']]),
        new Get(normalizationContext: ['groups' => ['city:collection']]),
        new \ApiPlatform\Metadata\Post(denormalizationContext: ['groups' => ['city:collection']]),
        new Put(),
        new Patch(),
        new Delete(),
    ]
)]
class City
{
    #[Groups(['city:collection', 'place:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['city:collection', 'place:collection'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['city:collection', 'place:collection'])]
    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[Groups(['city:collection'])]
    #[ORM\ManyToOne(inversedBy: 'cities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Province $province = null;

    /**
     * @var Collection<int, Place>
     */
    #[ORM\OneToMany(targetEntity: Place::class, mappedBy: 'city')]
    private Collection $places;

    #[Groups(['city:collection'])]
    /**
     * @var Collection<int, CityPhoto>
     */
    #[ORM\OneToMany(targetEntity: CityPhoto::class, mappedBy: 'city', cascade: ['persist'])]
    private Collection $photos;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'city')]
    private Collection $posts;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'city')]
    private Collection $products;

    /**
     * @var Collection<int, Culture>
     */
    #[ORM\ManyToMany(targetEntity: Culture::class, mappedBy: 'cities')]
    private Collection $cultures;

    /**
     * @var Collection<int, PlaceEvent>
     */
    #[ORM\OneToMany(targetEntity: PlaceEvent::class, mappedBy: 'city')]
    private Collection $placeEvents;

    #[Groups(['city:collection'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->places = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->cultures = new ArrayCollection();
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

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(?Province $province): static
    {
        $this->province = $province;

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): static
    {
        if (!$this->places->contains($place)) {
            $this->places->add($place);
            $place->setCity($this);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getCity() === $this) {
                $place->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CityPhoto>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(CityPhoto $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setCity($this);
        }

        return $this;
    }

    public function removePhoto(CityPhoto $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getCity() === $this) {
                $photo->setCity(null);
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
            $post->setCity($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCity() === $this) {
                $post->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addCity($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            $product->removeCity($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Culture>
     */
    public function getCultures(): Collection
    {
        return $this->cultures;
    }

    public function addCulture(Culture $culture): static
    {
        if (!$this->cultures->contains($culture)) {
            $this->cultures->add($culture);
            $culture->addCity($this);
        }

        return $this;
    }

    public function removeCulture(Culture $culture): static
    {
        if ($this->cultures->removeElement($culture)) {
            $culture->removeCity($this);
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
            $placeEvent->setCity($this);
        }

        return $this;
    }

    public function removePlaceEvent(PlaceEvent $placeEvent): static
    {
        if ($this->placeEvents->removeElement($placeEvent)) {
            // set the owning side to null (unless already changed)
            if ($placeEvent->getCity() === $this) {
                $placeEvent->setCity(null);
            }
        }

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
}
