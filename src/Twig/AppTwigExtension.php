<?php

namespace App\Twig;

use App\Entity\Properties;
use App\Entity\Version;
use App\Helpers\FormFieldResolver;
use App\Security\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppTwigExtension extends AbstractExtension {

    private $router;
    private $permissionService;
    private $em;

    public function __construct(UrlGeneratorInterface $router, PermissionService $permissionService, EntityManagerInterface $em) {
        $this->router = $router;
        $this->permissionService = $permissionService;
        $this->em = $em;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('resource', [$this, 'generateResourcePath']),
            new TwigFunction('hasRead', [$this->permissionService, 'hasRead']),
            new TwigFunction('hasWrite', [$this->permissionService, 'hasWrite']),
            new TwigFunction('hasLock', [$this->permissionService, 'hasLock']),
            new TwigFunction('hasDelete', [$this->permissionService, 'hasDelete']),
            new TwigFunction('class', [$this, 'getClass']),
            new TwigFunction('twigSyntax', [$this, 'getSyntaxFromType']),
            new TwigFunction('globales', [$this, 'getGlobalProject']),
            new TwigFunction('data', [$this, 'getData']),
        ];
    }

    public function getFilters() {
        return [
            new TwigFilter('evaluate', [$this, 'evaluate'], [
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => ['evaluate' => true]]
            ),
            new TwigFilter('data', [$this, 'getData'], [
                'needs_environment' => true,
                'needs_context' => true,
                'is_safe' => ['data' => true]]
            ),
            new TwigFilter('groupBy', [$this, 'groupBy'], [
                'needs_environment' => false,
                'needs_context' => false,
                'is_safe' => ['groupBy' => true]]
            )
        ];
    }

    public function groupBy($projects, $discriminator) {
        $groupedProjects = [];
        if (is_array($projects)) {
            foreach ($projects as $project) {
                if ($project instanceof \App\Entity\Project) {
                    $data = $this->getData($project->getSummary()->getCurrentLocked());
                    $key = null;
                    if (isset($data[$discriminator])) {
                        $key = $data[$discriminator];
                    }
                    $groupedProjects[$key][] = $project;
                }
            }
            return $groupedProjects;
        }
    }

    public function getGlobalProject() {
        $project = $this->em->getRepository(\App\Entity\Setting::class)->getValue("globalProject");
        $data = null;
        if ($project instanceof \App\Entity\Project) {
            if ($project->getSummary() instanceof Version) {
                $data = $this->getData($project->getSummary()->getCurrentLocked());
            }
        }
        return $data;
    }

    public function getData($version) {
        if ($version instanceof Version) {
            $data = $this->em->getRepository(Version::class)->getData($version);
            return $data['fields'];
        }
        return null;
    }

    public function getSyntaxFromType($field) {
        if (is_array($field)) {
            return FormFieldResolver::resolveFieldToSyntax($field);
        }
    }

    public function generateResourcePath($value, $download = false) {
        if ($value instanceof Properties) {
            $id = $value->getId();
        } else if ($value instanceof string) {
            $id = $value;
        } else {
            throw new RuntimeError("El valor de la funcion resource debe ser un objeto de la base de datos o una cadena pero es: " + strval($value));
        }
        return $this->router->generate("resource", ["method" => $download ? "download" : "view", "id" => $id]);
    }

    public function evaluate(Environment $environment, $context, $string, $withReplace = true) {
        if ($withReplace) {
            $string = \preg_replace('/{{\s*?(.*?)\s*?}}/si', '{{ ($1)|default("{{ $1 }}"|raw) }}', $string);
        }
        $loader = $environment->getLoader();
        $environment->disableStrictVariables();
        try {
            $parsed = $this->parseString($environment, $context, $string);

            $environment->setLoader($loader);
        } catch (SyntaxError $exc) {
            return null;
        }
        $environment->enableStrictVariables();
        return $parsed;
    }

    public function getClass($object) {
        return (new ReflectionClass($object))->getShortName();
    }

    protected function parseString(Environment $environment, $context, $string) {
        $string = $environment->createTemplate($string);
        return $environment->render($string, $context);
    }

}
