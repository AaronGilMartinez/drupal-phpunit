<?php

declare(strict_types=1);

namespace Drupal\Tests\rsvplist\Kernel;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @todo Add description.
 */
class TwigTemplateTest extends KernelTestBase {

  /**
   * @todo Add description.
   *
   * @dataProvider links
   */
  public function testLinkTemplate(array $expected_values, array $values): void {
    $this->assertLinkTemplate($expected_values, $values);
  }

  /**
   * Data provider for File defaults by type widget.
   *
   * @return \Generator
   *   The test data.
   */
  protected function links(): \Generator {
    yield [
        [
          'title' => 'Titulo',
          'attributes' => [
            'href' => 'https://www.google.es',
            'class' => 'test-class'
          ]
        ],
        [
          '#title' => 'Titulo',
          '#url' => 'https://www.google.es',
          '#attributes' => [
            'class' => [
              'test-class'
            ]
          ]
        ],
      ];
    yield [
      [
        'title' => '',
        'attributes' => [
          'href' => 'https://www.google.es',
        ]
      ],
      [
        '#title' => '',
        '#url' => 'https://www.google.es',
        '#attributes' => []
      ],
    ];
    yield [
      [
        'title' => 'Titulo',
        'attributes' => [
          'href' => 'https://www.google.es',
        ]
      ],
      [
        '#title' => 'Titulo',
        '#url' => 'https://www.google.es',
        '#attributes' => []
      ],
    ];
  }


  /**
   * Undocumented function
   *
   * @param array $expected_values
   * @param array $values
   * @return void
   */
  protected function assertLinkTemplate(array $expected_values, array $values) {
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');

    $template = [
      '#theme' => 'rsvplist_add_link',
    ] + $values;

    $html = $renderer->renderRoot($template);
    $crawler = new Crawler((string) $html);

    $link = $crawler->filter('a');
    $this->assertCount(1, $link);
    $this->assertEquals($expected_values['title'] ?? '', $link->text());

    foreach($expected_values['attributes'] as $k => $v) {
      $this->assertEquals($v, $link->attr($k));
    }
  }
}
