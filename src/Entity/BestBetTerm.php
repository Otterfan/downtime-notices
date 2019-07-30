<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BestBetTermRepository")
 */
class BestBetTerm
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BestBet", inversedBy="terms")
     */
    private $bestBet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getBestBet(): ?BestBet
    {
        return $this->bestBet;
    }

    public function setBestBet(?BestBet $bestBet): self
    {
        $this->bestBet = $bestBet;

        return $this;
    }

    public function __toString()
    {
        return $this->value;
    }
}
