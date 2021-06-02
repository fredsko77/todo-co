<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TaskExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return [
            new TwigFunction('type', [$this, 'type'], ['is_safe' => ['html']]),
            new TwigFunction('gettype', [$this, 'gettype'], ['is_safe' => ['html']]),
        ];
    }

    public function type($variable, string $type = "str")
    {
        if ($type === "str") {
            return (string) $variable;
        }
        if ($type === "arr") {
            return (array) $variable;
        }
        if ($type === "bool") {
            return (bool) $variable;
        }
        if ($type === "int") {
            return (int) $variable;
        }
        if ($type === "obj") {
            return (object) $variable;
        }

        return (string) $variable;
    }

    public function gettype($var)
    {
        return gettype($var);
    }

}
