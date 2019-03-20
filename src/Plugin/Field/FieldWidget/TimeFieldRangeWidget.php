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
 *  id = "timefield_range_widget",
 *  label = @Translation("Timefield Range"),
 *  field_types = {"timefield"}
 * )
 */
class TimeFieldRangeWidget extends TimeFieldStandardWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $instance_class = str_replace('_', '-', $items->getName()) . "-" . $delta;
    $instance_settings = $this->getSettings();
    if (!$instance_settings['disable_plugin']) {
      $js_settings = _timefield_js_settings($instance_class, $instance_settings);
      $context = array(
        'type' => 'field',
        'items' => $items,
      );

      \Drupal::moduleHandler()->alter('timefield_js_settings', $js_settings, $context);

      $element['#attached']['library'][] = 'timefield/timepicker';
      $element['#attached']['library'][] = 'timefield/timefield';
      $element['#attached']['drupalSettings']['timefield'][$instance_class . '-value'] = $js_settings;
      $element['#attached']['drupalSettings']['timefield'][$instance_class . '-end-value'] = $js_settings;
    }

    $element += array(
      '#delta' => $delta,
    );

    $value = isset($items[$delta]) ? timefield_integer_to_time($instance_settings, $items[$delta]->value) : '';
    $element['#type'] = 'fieldset';
    $element['value'] = array(
      '#type' => 'textfield',
      '#title' => t('Start Time'),
      '#default_value' => $value,
      '#required' => $element['#required'],
      '#weight' => (isset($element['#weight'])) ? $element['#weight'] : 0,
      '#delta' => $delta,
      '#element_validate' => array('timefield_time_validate'),
      '#attributes' => array(
        'class' => array(
          'edit-timefield-timepicker',
          $instance_class . '-value'
        )
      ),
    );
    $end_value = isset($items[$delta]) ? timefield_integer_to_time($instance_settings, $items[$delta]->end_value) : '';
    $element['end_value'] = array(
      '#type' => 'textfield',
      '#title' => t('End Time'),
      '#default_value' => $end_value,
      '#required' => $element['#required'],
      '#weight' => (isset($element['#weight'])) ? $element['#weight'] : 0,
      '#delta' => $delta,
      '#element_validate' => array('timefield_time_validate'),
      '#attributes' => array(
        'class' => array(
          'edit-timefield-timepicker',
          $instance_class . '-end-value'
        )
      ),
    );
    return $element;
  }
}
