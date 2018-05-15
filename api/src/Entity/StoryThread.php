<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoryThreadRepository")
 */
class StoryThread
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $leaseExpiration;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ArcSegment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $currentArcSegment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StoryLine", mappedBy="storyThread")
     */
    private $storyLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Element", mappedBy="storyThread")
     */
    private $element;

    /**
     * @ORM\Column(type="integer")
     */
    private $targetNumberOfLines;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isComplete;

    public function __construct()
    {
        $this->storyLines = new ArrayCollection();
        $this->element = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLeaseExpiration(): ?\DateTimeInterface
    {
        return $this->leaseExpiration;
    }

    public function setLeaseExpiration(?\DateTimeInterface $leaseExpiration): self
    {
        $this->leaseExpiration = $leaseExpiration;

        return $this;
    }

    public function getCurrentArcSegment(): ?ArcSegment
    {
        return $this->currentArcSegment;
    }

    public function setCurrentArcSegment(?ArcSegment $currentArcSegment): self
    {
        $this->currentArcSegment = $currentArcSegment;

        return $this;
    }

    /**
     * @return Collection|StoryLine[]
     */
    public function getStoryLines(): Collection
    {
        return $this->storyLines;
    }

    public function addStoryLine(StoryLine $storyLine): self
    {
        if (!$this->storyLines->contains($storyLine)) {
            $this->storyLines[] = $storyLine;
            $storyLine->setStoryThread($this);
        }

        return $this;
    }

    public function removeStoryLine(StoryLine $storyLine): self
    {
        if ($this->storyLines->contains($storyLine)) {
            $this->storyLines->removeElement($storyLine);
            // set the owning side to null (unless already changed)
            if ($storyLine->getStoryThread() === $this) {
                $storyLine->setStoryThread(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Element[]
     */
    public function getElement(): Collection
    {
        return $this->element;
    }

    public function addElement(Element $element): self
    {
        if (!$this->element->contains($element)) {
            $this->element[] = $element;
            $element->setStoryThread($this);
        }

        return $this;
    }

    public function removeElement(Element $element): self
    {
        if ($this->element->contains($element)) {
            $this->element->removeElement($element);
            // set the owning side to null (unless already changed)
            if ($element->getStoryThread() === $this) {
                $element->setStoryThread(null);
            }
        }

        return $this;
    }

    public function getTargetNumberOfLines(): ?int
    {
        return $this->targetNumberOfLines;
    }

    public function setTargetNumberOfLines(int $targetNumberOfLines): self
    {
        $this->targetNumberOfLines = $targetNumberOfLines;

        return $this;
    }

    public function getIsComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setIsComplete(bool $isComplete): self
    {
        $this->isComplete = $isComplete;

        return $this;
    }
}
