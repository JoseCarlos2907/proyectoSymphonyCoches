<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $model = null;


    #[ORM\Column]
    private ?string $manufacturer = null;


    #[ORM\Column]
    private ?string $features = null;


    #[ORM\Column]
    private ?string $price = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function getFeatures(): ?string
    {
        return $this->features;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function setManufacturer(string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function setFeatures(string $features): static
    {
        $this->features = $features;
        return $this;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }
}
