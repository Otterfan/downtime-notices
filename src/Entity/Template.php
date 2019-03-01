<?php

namespace App\Entity;

use App\Form\ApplicationType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TemplateRepository")
 */
class Template
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Priority", inversedBy="templates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NoteType", inversedBy="templates")
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Application", mappedBy="template", cascade={"persist", "remove"})
     */
    private $application;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $name;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
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

    public function __toString()
    {
        return $this->name;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        // set (or unset) the owning side of the relation if necessary
        $newTemplate = $application === null ? null : $this;
        if ($newTemplate !== $application->getTemplate()) {
            $application->setTemplate($newTemplate);
        }

        return $this;
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
}
