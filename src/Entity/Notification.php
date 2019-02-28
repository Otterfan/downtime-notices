<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NotificationView", mappedBy="notification", orphanRemoval=true)
     */
    private $views;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Priority", inversedBy="notifications")
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NoteType", inversedBy="notifications")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Application", inversedBy="notifications")
     */
    private $application;

    public function __construct()
    {
        $this->views = new ArrayCollection();
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

        return !isset($this->finish) || $this->finish > $now;
    }

    public function isPending(): bool
    {
        return $this->start > $this->now();
    }

    private function now(): \DateTime
    {
        return new \DateTime('now', new \DateTimeZone('America/New_York '));
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

    public function publicView(): array
    {
        return [
            'id'          => $this->id,
            'text'        => $this->text,
            'priority'    => $this->getPriority() ? $this->getPriority()->getLevel() : 3,
            'type'        => $this->getType() ? $this->getType()->getName() : null,
            'application' => $this->getApplication() ? $this->getApplication()->getName() : null,
            'start'       => $this->getStartString(),
            'className'   => $this->isActive() ? 'fullcalendar-active-note' : '',
            'end'         => $this->getFinishString()
        ];
    }

    public function calendarFeed(string $route_base): array
    {
        $base = $this->publicView();

        $base['title'] = $base['text'];
        unset($base['text']);

        $base['url'] = "$route_base/{$this->id}";
        $base['color'] = 'white';

        if ($base['end'] === null) {
            $base['end'] = $base['start'];
        }

        return $base;
    }

    /**
     * @return Collection|NotificationView[]
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(NotificationView $view): self
    {
        if (!$this->views->contains($view)) {
            $this->views[] = $view;
            $view->setNotification($this);
        }

        return $this;
    }

    public function removeView(NotificationView $view): self
    {
        if ($this->views->contains($view)) {
            $this->views->removeElement($view);
            // set the owning side to null (unless already changed)
            if ($view->getNotification() === $this) {
                $view->setNotification(null);
            }
        }

        return $this;
    }

    public function getPriority(): ?Priority
    {
        return $this->priority;
    }

    public function setPriority(?Priority $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getType(): ?NoteType
    {
        return $this->type;
    }

    public function setType(?NoteType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

}
