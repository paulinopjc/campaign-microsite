<?php

namespace App\Libraries;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(ROOTPATH . 'templates');
        $this->twig = new Environment($loader, [
            'cache' => WRITEPATH . 'cache/twig',
            'auto_reload' => true,
        ]);
    }

    public function render(string $template, array $data = []): string
    {
        return $this->twig->render($template . '.twig', $data);
    }
}