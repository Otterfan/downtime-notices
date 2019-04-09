<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uptime_robot_code;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Template", inversedBy="application", cascade={"persist", "remove"})
     */
    private $template;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="application")
     */
    private $notifications;

    /**
     * @ORM\Column(type="boolean")
     */
    private $automatic = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $on_status_page;

    /**
     * @ORM\Column(type="text")
     */
    private $public_description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string")
     */
    private $public_name;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getUptimeRobotCode(): ?string
    {
        return $this->uptime_robot_code;
    }

    public function setUptimeRobotCode(?string $uptime_robot_code): self
    {
        $this->uptime_robot_code = $uptime_robot_code;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setApplication($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getApplication() === $this) {
                $notification->setApplication(null);
            }
        }

        return $this;
    }

    public function getAutomatic(): ?bool
    {
        return $this->automatic;
    }

    public function setAutomatic(bool $automatic): self
    {
        $this->automatic = $automatic;

        return $this;
    }

    public function getOnStatusPage(): ?bool
    {
        return $this->on_status_page;
    }

    public function setOnStatusPage(bool $on_status_page): self
    {
        $this->on_status_page = $on_status_page;

        return $this;
    }

    public function getPublicDescription(): ?string
    {
        return $this->public_description;
    }

    public function setPublicDescription(string $public_description): self
    {
        $this->public_description = $public_description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getPublicName(): ?string
    {
        return $this->public_name;
    }

    public function setPublicName(string $public_name): self
    {
        $this->public_name = $public_name;

        return $this;
    }
}
