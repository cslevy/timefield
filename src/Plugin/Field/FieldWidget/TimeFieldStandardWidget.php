<?php /**
 * @file
 * Contains \Drupal\timefield\Plugin\Field\FieldWidget\TimeField.
 */

namespace Drupal\timefield\Plugin\Field\FieldWidget;

use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldWidget(
 *  id = "timefield_standard_widget",
 *  label = @Translation("Timefield"),
 *  field_types = {"timefield"}
 * )
 */
class TimeFieldStandardWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
      $dsc_key     = '#description';
      $default_dsc = t('Password will remain unchanged if left blank.');
      $description = (empty($element[$dsc_key])) ? $default_dsc : $element[$dsc_key];
      $weight      = (isset($element['#weight'])) ? $element['#weight'] : 0;
    $form_state->fieldDefinition = $this->fieldDefinition;

      $element['timefield'] = array(
        '#type'             => 'time',
        '#title'            => \Drupal\Component\Utility\Xss::filter($element['#title']),
        '#description'      => \Drupal\Component\Utility\Xss::filter($description),
        '#default_value'    => $this->getSetting('timefield'),
        '#required'         => $element['#required'],
        '#weight'           => $weight,
        '#delta'            => $delta,
        '#element_validate' => array('timefield_time_validate'),
      );
    return $element;
  }
}
