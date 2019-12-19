<?php

namespace App\Form\Requests;

use App\Entity\Group;
use App\Entity\Template;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class EditTemplateViewRequest {

    /**
     * @NotBlank()
     * @Choice(choices={"letter","legal","ledger","A5","A4","A3","B5","B4","JIS-B5","JIS-B4"})
     * @var string
     */
    public $size;

    /**
     * @NotBlank()
     * @Choice(choices={"portrait","landscape"})
     * @var string 
     */
    public $orientation;

    /**
     * @NotBlank()
     * @Range(min=0)
     * @var float
     *
     */
    public $top;

    /**
     * @NotBlank()
     * @Range(min=0)
     * @var float
     *
     */
    public $left;

    /**
     * @NotBlank()
     * @Range(min=0)
     * @var float
     *
     */
    public $right;

    /**
     * @NotBlank()
     * @Range(min=0)
     * @var float
     *
     */
    public $bottom;

    /**
     * @NotBlank()
     * @Range(min=0)
     * @var float
     *
     */
    public $header;

    /**
     * @NotBlank()
     * @Range(min=0)
     * @var float
     *
     */
    public $footer;

    /**
     * @NotBlank()
     * @var string
     * */
    public $template;

    /**
     *
     * @var Template 
     */
    private $entity;

    
    public function fillEntity(Template $template): self {
        $this->entity = $template;
        $settings = $template->getSetting("settings");
        $this->size = $settings['size']['name'] ?? "";
        $this->orientation = $settings['orientation'] ?? "";
        $this->header = $settings['margin']['header'] ?? "";
        $this->top = $settings['margin']['top'] ?? "";
        $this->left = $settings['margin']['left'] ?? "";
        $this->right = $settings['margin']['right'] ?? "";
        $this->bottom = $settings['margin']['bottom'] ?? "";
        $this->footer = $settings['margin']['footer'] ?? "";
        $this->template = $template->getFile()->readFile();
        return $this;
    }

    public function createEntity() {
        $template = $this->entity;
        $settings = $template->getSetting("settings");
        $settings['size']['name'] = $this->size;
        switch ($this->size) {
            case "letter":
                $settings['size']['height'] = 27.94;
                $settings['size']['width'] = 21.59;
                break;
            case "legal":
                $settings['size']['height'] = 35.6;
                $settings['size']['width'] = 21.6;
                break;
            case "ledger":
                $settings['size']['height'] = 43.2;
                $settings['size']['width'] = 27.9;
                break;
            case "A5":
                $settings['size']['height'] = 21.0;
                $settings['size']['width'] = 14.8;
                break;
            case "A4":
                $settings['size']['height'] = 29.7;
                $settings['size']['width'] = 21.0;
                break;
            case "A3":
                $settings['size']['height'] = 42.0;
                $settings['size']['width'] = 29.7;
                break;
            case "B5":
                $settings['size']['height'] = 25.0;
                $settings['size']['width'] = 17.6;
                break;
            case "B4":
                $settings['size']['height'] = 35.3;
                $settings['size']['width'] = 25.0;
                break;
            case "JIS-B5":
                $settings['size']['height'] = 25.7;
                $settings['size']['width'] = 18.2;
                break;
            case "JIS-B4":
                $settings['size']['height'] = 36.4;
                $settings['size']['width'] = 25.7;
                break;

            default:
                $settings['size']['height'] = 27.94;
                $settings['size']['width'] = 21.59;
                break;
        }
        $settings['orientation'] = $this->orientation;
        $settings['margin']['header'] = $this->header;
        $settings['margin']['top'] = $this->top;
        $settings['margin']['left'] = $this->left;
        $settings['margin']['right'] = $this->right;
        $settings['margin']['bottom'] = $this->bottom;
        $settings['margin']['footer'] = $this->footer;
        $file=$template->getFile()->getPath();
        file_put_contents($file, $template);
        $template->getFile()->update();
        return $template;
    }

}
