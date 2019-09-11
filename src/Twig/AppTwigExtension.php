<?php

namespace App\Twig;

use App\Entity\Properties;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig_Error_Runtime;

class AppTwigExtension extends AbstractExtension {

    private $router;

    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('resource', [$this, 'generateResourcePath']),
        ];
    }

    public function generateResourcePath($value, $download = false) {
        if ($value instanceof Properties) {
            $id = $value->getId();
        } else if ($value instanceof string) {
            $id = $value;
        } else {
            throw new Twig_Error_Runtime("El valor de la funcion resource debe ser un objeto de la base de datos o una cadena pero es: " + strval($value));
        }
        return $this->router->generate("resource", ["method" => $download ? "download" : "view", "id" => $id]);
    }

}
