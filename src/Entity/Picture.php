<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 * @Vich\Uploadable()
 */
class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture_title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture_url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture_description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPictureTitle(): ?string
    {
        return $this->picture_title;
    }

    public function setPictureTitle(string $picture_title): self
    {
        $this->picture_title = $picture_title;

        return $this;
    }

    public function getPictureUrl(): ?string
    {
        return $this->picture_url;
    }

    public function setPictureUrl(string $picture_url): self
    {
        $this->picture_url = $picture_url;

        return $this;
    }

    public function getPictureDescription(): ?string
    {
        return $this->picture_description;
    }

    public function setPictureDescription(?string $picture_description): self
    {
        $this->picture_description = $picture_description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return  $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
