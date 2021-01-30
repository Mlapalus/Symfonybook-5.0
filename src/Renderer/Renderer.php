<?php 

namespace App\Renderer;

use Twig\Environment;

class Renderer {

  private $render;

  public function __construct(Environment $twig)
  {
    $this->render = $twig;
  }

  public function render(string $view, array $options) : string
  {
    return $this->render->render($view, $options);
  }

  public function addGlobal(string $name, $value)
  {
    $this->render->addGlobal($name,$value);
  }
}