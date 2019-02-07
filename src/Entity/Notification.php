<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
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
    private $text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finish;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
     */
    private $poster;

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

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getFinish(): ?\DateTimeInterface
    {
        return $this->finish;
    }

    public function setFinish(?\DateTimeInterface $finish): self
    {
        $this->finish = $finish;

        return $this;
    }

    public function getPoster(): ?User
    {
        return $this->poster;
    }

    public function setPoster(?User $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getStartString()
    {
        return $this->start->format('Y-m-d H:i:s');
    }

    public function getFinishString()
    {
        return isset($this->finish) ? $this->finish->format('Y-m-d H:i:s') : null;
    }

    public function deactivate(): void
    {
        $this->finish = $this->now();
    }

    public function activate(): void
    {
        $this->finish = null;
        $this->start = $this->now();
    }

    public function isActive(): bool
    {
        $now = $this->now();

        if ($now < $this->start) {
            return false;
        }

        return ! isset($this->finish) || $this->finish > $now;
    }

    public function isPending(): bool
    {
        return $this->start > $this->now();
    }

    private function now(): \DateTime
    {
        return new \DateTime('now', new \DateTimeZone('America/New_York '));
    }

}
