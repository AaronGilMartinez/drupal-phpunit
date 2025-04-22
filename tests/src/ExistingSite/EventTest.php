<?php

namespace Drupal\Test\drupal_phpunit\ExistingSite;

use weitzman\DrupalTestTraits\ExistingSiteBase;

class EventTest extends ExistingSiteBase {


  /**
   * Undocumented function
   *
   * @return void
   */
  public function testAccess() {
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);
  }
}
