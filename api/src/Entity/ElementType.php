<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ElementTypeRepository")
 */
class ElementType
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
    private $instructions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Element", mappedBy="elementType")
     */
    private $elements;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isUserSelectable;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

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

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(string $instructions): self
    {
        $this->instructions = $instructions;

        return $this;
    }

    /**
     * @return Collection|Element[]
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements[] = $element;
            $element->setElementType($this);
        }

        return $this;
    }

    public function removeElement(Element $element): self
    {
        if ($this->elements->contains($element)) {
            $this->elements->removeElement($element);
            // set the owning side to null (unless already changed)
            if ($element->getElementType() === $this) {
                $element->setElementType(null);
            }
        }

        return $this;
    }

    public function getIsUserSelectable(): ?bool
    {
        return $this->isUserSelectable;
    }

    public function setIsUserSelectable(bool $isUserSelectable): self
    {
        $this->isUserSelectable = $isUserSelectable;

        return $this;
    }
}
