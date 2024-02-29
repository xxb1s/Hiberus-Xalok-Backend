<?php

namespace App\Entity;

use App\Repository\DriversRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DriversRepository::class)]
class Drivers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['list_drivers'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list_drivers'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list_drivers'])]
    private ?string $surname = null;

    #[ORM\Column(length: 1)]
    #[Groups(['list_drivers'])]
    private ?string $license = null;

    #[ORM\OneToMany(targetEntity: Trips::class, mappedBy: 'driver', orphanRemoval: true)]
    private Collection $trips;

    #[ORM\Column]
    #[Groups(['list_drivers'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['list_drivers'])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleted_at = null;

    public function __construct()
    {
        $this->trips = new ArrayCollection();
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(string $license): static
    {
        $this->license = $license;

        return $this;
    }

    /**
     * @return Collection<int, Trips>
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(Trips $trip): static
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->setDrivers($this);
        }

        return $this;
    }

    public function removeTrip(Trips $trip): static
    {
        if ($this->trips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getDrivers() === $this) {
                $trip->setDrivers(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }
}
