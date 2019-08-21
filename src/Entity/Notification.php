<?php

namespace App\Entity;

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

    private $id;
    private $icon;
    private $color;
    private $text;
    private $creationDate;
    private $path;
    private $parameters = [];
    private $user;

    public function __construct($text, $color = null, $icon = null, $path = null, $params = null) {
        $this->color = $color;
        $this->icon = $icon;
        $this->text = $text;
        $this->path = $path;
        $this->params = $params;
        $this->creationDate = new \DateTime("now");
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

}
