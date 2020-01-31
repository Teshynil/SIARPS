<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Entity
 */
class Notification {

    const COLOR_BLUE = "BLUE";
    const COLOR_INDIGO = "INDIGO";
    const COLOR_PURPLE = "PURPLE";
    const COLOR_PINK = "PINK";
    const COLOR_RED = "RED";
    const COLOR_ORANGE = "ORANGE";
    const COLOR_YELLOW = "YELLOW";
    const COLOR_GREEN = "GREEN";
    const COLOR_TEAL = "TEAL";
    const COLOR_CYAN = "CYAN";
    const COLOR_WHITE = "WHITE";
    const COLOR_GRAY = "GRAY";
    const COLOR_GRAY_DARK = "GRAY_DARK";
    const COLOR_LIGHT_BLUE = "LIGHT_BLUE";
    const COLOR_PRIMARY = "PRIMARY";
    const COLOR_SECONDARY = "SECONDARY";
    const COLOR_SUCCESS = "SUCCESS";
    const COLOR_INFO = "INFO";
    const COLOR_WARNING = "WARNING";
    const COLOR_DANGER = "DANGER";
    const COLOR_LIGHT = "LIGHT";
    const COLOR_DARK = "DARK";
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="c_icon", type="string", length=32, nullable=true)
     */
    private $icon;

    /**
     * @var string|null
     *
     * @ORM\Column(name="c_color", type="string", length=32, nullable=true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="c_text", type="string", length=320)
     */
    private $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="c_creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="c_path", type="string", length=180, nullable=true)
     */
    private $path;

    /**
     * @var json|null
     *
     * @ORM\Column(name="c_parameters", type="json", nullable=true)
     */
    private $parameters;

    /**
     * @var bool
     *
     * @ORM\Column(name="c_read", type="boolean")
     */
    private $read;

    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function __construct($text, $color = null, $icon = null, $path = null, $params = null) {
        $this->color = $color;
        $this->icon = $icon;
        $this->text = $text;
        $this->path = $path;
        $this->params = $params;
        $this->creationDate = new \DateTime("now");
        $this->read = false;
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(string $icon): self {
        $this->icon = $icon;

        return $this;
    }

    public function getColor(): ?string {
        return $this->color;
    }

    public function setColor(string $color): self {
        $this->color = $color;

        return $this;
    }

    public function getText(): ?string {
        return $this->text;
    }

    public function setText(string $text): self {
        $this->text = $text;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getPath(): ?string {
        return $this->path;
    }

    public function setPath(string $path): self {
        $this->path = $path;

        return $this;
    }

    public function getParameters(): ?array {
        return $this->parameters;
    }

    public function setParameters(array $parameters): self {
        $this->parameters = $parameters;

        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;

        return $this;
    }

    public function getRead(): ?bool {
        return $this->read;
    }

    public function setRead(bool $read): self {
        $this->read = $read;

        return $this;
    }

}
