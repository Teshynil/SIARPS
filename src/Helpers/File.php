<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File as SysFile;

/**
 * Description of File
 *
 * @author Teshynil
 */
class File extends SysFile implements \JsonSerializable {

    private $path;
    private $name;
    private $mimeType;
    private $size;
    private $dimensions;
    private $valid = true;

    public function __construct($path, $name, $mimeType = null, $size = null) {
        $this->path = $path;
        $this->name = $name;
        $this->mimeType = $mimeType;
        $this->size = $size;
        parent::__construct($path);
        if ($mimeType == null) {
            $this->mimeType = $this->getMimeType();
        }
        if ($size == null) {
            $this->size = $this->getSize();
        }
    }

    public function download() {
        FileDownloader::Download($this);
    }

    public function getLink() {
        FileDownloader::Link($this);
    }

    public function getBase64() {
        return 'data:' . $this->getDBMimeType() . ';base64,' . base64_encode($this->openFile()->fread($this->getDBSize()));
    }

    public function exists(): ?bool {
        $filesystem = new Filesystem();
        return $filesystem->exists($this->getPath());
    }

    public function validate(): ?bool {
        if ($this->valid) {
            $this->valid = $this->getDBMimeType() == $this->getMimeType() && $this->getDBSize() == $this->getSize();
        }
        return $this->valid;
    }

    public function getDBPath(): ?string {
        return $this->path;
    }

    public function getDBName(): ?string {
        return $this->name;
    }

    public function getDBMimeType() {
        return $this->mimeType;
    }

    public function getDBSize() {
        return $this->size;
    }

    public function setDBPath($path) {
        $this->path = $path;
        return $this;
    }

    public function setDBName($name) {
        $this->name = $name;
        return $this;
    }

    public function setDBMimeType($mimeType) {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function setDBSize($size) {
        $this->size = $size;
        return $this;
    }

    public function jsonSerialize() {
        return [
            "path" => $this->getDBPath(),
            "name" => $this->getDBName(),
            "mime" => $this->getDBMimeType(),
            "size" => $this->getDBSize()
        ];
    }

}
