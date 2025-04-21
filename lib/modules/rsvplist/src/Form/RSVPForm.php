<?php

/**
 * @file
 * A form to collect an email address for RSVP details.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxFormHelperTrait;
use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\DependencyInjection\AutowireTrait;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\rsvplist\Exception\RsvpListException;
use Drupal\rsvplist\ListManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class RSVPForm extends FormBase {

  use AjaxHelperTrait;
  use AjaxFormHelperTrait;
  use AutowireTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  public function __construct(
    protected AccountProxyInterface $currentUser,
    #[Autowire(service: 'rsvplist.list_manager')]
    protected ListManagerInterface $listManager,
    protected EmailValidatorInterface $email_validator,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $node = NULL) {
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email address'),
      '#size' => 25,
      '#description' => t("We will send updates to the email address you provide."),
      '#required' => TRUE,
    ];

    $form['nid'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#title' => t('Node'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];

    // Module library.
    $form['#attached']['library'][] = 'core/drupal.ajax';

    if ($this->isAjax()) {
      // Due to https://www.drupal.org/node/2897377 we have to declare a fixed
      // ID for the form.
      // Since only one modal can be opened at the time, we can rely on the
      // form ID as HTML ID.
      // @todo Remove this workaround once https://www.drupal.org/node/2897377
      //   is fixed.
      $form['#id'] = Html::getId($form_state->getBuildInfo()['form_id']);
      $form['submit']['#ajax'] = ['callback' => '::ajaxSubmit'];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if ( !($this->email_validator->isValid($value)) ) {
      $form_state->setErrorByName(
        'email',
        $this->t('It appears that %mail is not a valid email. Please try again',['%mail' => $value])
    );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      // Begin Phase 1: initiate variables to save.

      // Get current user ID.
      $uid = $this->currentUser->id();
      // Obtain values as entered into the Form.
      $nid = $form_state->getValue('nid');
      $mail = $form_state->getValue('email');

      $this->listManager->add($nid, $uid, $mail);

      // Provide the form submitter a nice message.
      $this->messenger()->addMessage(
        t('Thank you for your RSVP, you are on the list for the event!')
      );
      // End Phase 3
    }
    catch (RsvpListException $e) {
      $this->messenger()->addError(
        t('Unable to save RSVP settings at this time due to database error.
           Please try again.')
      );
    }

    $form_state->setRedirectUrl(Url::fromRoute('<front>'));
  }

  /**
   * {@inheritdoc}
   */
  protected function successfulAjaxSubmit(array $form, FormStateInterface $form_state)
  {
    // We need to retrieve the redirect URL set in ::formSubmit(), but
    // the getRedirect() method will return false if redirects are disabled.
    // Form redirects are normally disabled during AJAX requests by the form
    // builder.
    // @see \Drupal\Core\Form\FormBuilder::buildForm()
    $is_redirect_disabled = $form_state->isRedirectDisabled();
    $form_state->disableRedirect(FALSE);
    $redirect = $form_state->getRedirect();
    $form_state->disableRedirect($is_redirect_disabled);
    $response = new AjaxResponse();
    $url = new RedirectCommand($redirect->toString());

    return $response->addCommand($url);
  }
}
