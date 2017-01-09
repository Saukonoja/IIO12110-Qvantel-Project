<?php






namespace Drupal\cassie;


use Drupal\Core\Controller\ControllerBase;


class CassieController extends ControllerBase {
  public function content() {
    return array(
        '#markup' => '' . t('Hello there!') . '',
    );
  }
}



