<?php

namespace Drupal\rsvplist\tests\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

class RSVPFormBlockTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'rsvplist'
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests block with AJAX link to form.
   *
   * @return void
   */
  public function testForm(): void {
    // Place block.

    // Click link to open the form.

    // Test form.

    // Check saved data.
  }

}
