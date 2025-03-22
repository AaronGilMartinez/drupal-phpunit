<?php

declare(strict_types=1);

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a rsvp form block.
 */
#[Block(
  id: 'rsvplist_rsvp_form',
  admin_label: new TranslatableMarkup('RSVP Form'),
  category: new TranslatableMarkup('Custom'),
)]
final class RsvpFormBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly FormBuilderInterface $formBuilder,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // https://www.drupal.org/docs/develop/drupal-apis/ajax-api/ajax-dialog-boxes#s-link-to-a-modal-in-a-render-array
    return [
      '#type' => 'link',
      '#title' => $this->t('Add to node RSVP list.'),
      '#url' => Url::fromRoute('rsvplist.add'),
      '#ajax' => [
        'dialogType' => 'modal',
        'dialog' => ['height' => 400, 'width' => 700],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIf($account->hasPermission('view rsvplist'));
  }

}
