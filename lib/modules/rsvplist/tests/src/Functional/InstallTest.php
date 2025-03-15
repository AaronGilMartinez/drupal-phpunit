<?php

namespace Drupal\rsvplist\tests\Functional;

use Drupal\Tests\BrowserTestBase;

class InstallTest extends BrowserTestBase {

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
   * Tests module installation.
   *
   * @return void
   */
  public function testInstall(): void {
    $this->drupalGet('<front>');
    $this->assertSession()->statusCodeEquals(200);
  }

}
