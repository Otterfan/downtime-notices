<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BestBetRepository")
 */
class BestBet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $text;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="date")
     */
    private $created;

    /**
     * @ORM\Column(type="date")
     */
    private ?\DateTimeInterface $updated;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $image;

    /**
     * @ORM\Column(type="string", length=30, nullable=false, options={"default":"other"})
     */
    private string $source_type;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private ?string $source_identifier;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false})
     */
    private ?bool $needs_update;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BestBetTerm", mappedBy="bestBet", cascade={"persist"})
     */
    private $terms;

    const SOURCE_TYPES = ['azlist', 'faq', 'other'];

    public function __construct()
    {
        $this->terms = new ArrayCollection();
        $this->setUpdated(new \DateTime('now'));
        if ($this->getCreated() === null) {
            $this->setCreated(new \DateTime('now'));
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getNeedsUpdate(): ?bool
    {
        return $this->needs_update;
    }

    public function setNeedsUpdate(?bool $needs_update): void
    {
        $this->needs_update = $needs_update;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceType(): string
    {
        return $this->source_type;
    }

    /**
     * @param string $source_type
     */
    public function setSourceType(string $source_type): void
    {
        if (!in_array($source_type, self::SOURCE_TYPES, true)) {
            throw new \InvalidArgumentException("\$source_type must be one of '" . implode("', '", self::SOURCE_TYPES) . "' : $source_type given");
        }
        $this->source_type = $source_type;
    }

    /**
     * @return ?string
     */
    public function getSourceIdentifier(): ?string
    {
        return $this->source_identifier;
    }

    /**
     * @param ?string $source_identifier
     */
    public function setSourceIdentifier(?string $source_identifier): void
    {
        $this->source_identifier = $source_identifier;
    }


    /**
     * @return Collection|BestBetTerm[]
     */
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    public function addTerm(BestBetTerm $term): self
    {
        if (!$this->terms->contains($term)) {
            $this->terms[] = $term;
            $term->setBestBet($this);
        }

        return $this;
    }

    public function removeTerm(BestBetTerm $term): self
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            // set the owning side to null (unless already changed)
            if ($term->getBestBet() === $this) {
                $term->setBestBet(null);
            }
        }

        return $this;
    }

    public function sourceURL(): ?string
    {
        if ($this->source_type === 'azlist') {
            return "https://bc.libapps.com/libguides/az.php?action=0&section=2&id={$this->source_identifier}";
        } elseif ($this->source_type === 'faq') {
            return "https://answers.bc.edu/faq/{$this->source_identifier}";
        }
        return null;
    }
}
