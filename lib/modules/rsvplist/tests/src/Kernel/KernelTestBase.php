<?php

declare(strict_types=1);

namespace Drupal\Tests\rsvplist\Kernel;

use Drupal\KernelTests\KernelTestBase as CoreKernelTestBase;

/**
 * @todo Add description.
 */
abstract class KernelTestBase extends CoreKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'rsvplist',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installSchema('rsvplist', ['rsvplist']);
  }
}
