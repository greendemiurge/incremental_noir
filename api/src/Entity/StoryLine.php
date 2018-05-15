<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoryLineRepository")
 */
class StoryLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $line;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StoryThread", inversedBy="storyLines")
     */
    private $storyThread;

    public function getId()
    {
        return $this->id;
    }

    public function getLine(): ?string
    {
        return $this->line;
    }

    public function setLine(string $line): self
    {
        $this->line = $line;

        return $this;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(?string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getStoryThread(): ?StoryThread
    {
        return $this->storyThread;
    }

    public function setStoryThread(?StoryThread $storyThread): self
    {
        $this->storyThread = $storyThread;

        return $this;
    }
}
