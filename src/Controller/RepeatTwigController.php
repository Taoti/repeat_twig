<?php

namespace Drupal\repeat_twig\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * It use a custom theme to show how use the twig extensions defined here.
 */
class RepeatTwigController extends ControllerBase {

  /**
   * Show examples.
   */
  public function examples() {
    return [
      '#theme' => 'repeat_twig_examples',
    ];
  }

}
