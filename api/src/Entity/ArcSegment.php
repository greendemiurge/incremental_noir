<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArcSegmentRepository")
 */
class ArcSegment
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
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $starts_at_percent_completed;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxElements;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartsAtPercentCompleted(): ?int
    {
        return $this->starts_at_percent_completed;
    }

    public function setStartsAtPercentCompleted(int $starts_at_percent_completed): self
    {
        $this->starts_at_percent_completed = $starts_at_percent_completed;

        return $this;
    }

    public function getMaxElements(): ?int
    {
        return $this->maxElements;
    }

    public function setMaxElements(int $maxElements): self
    {
        $this->maxElements = $maxElements;

        return $this;
    }
}
