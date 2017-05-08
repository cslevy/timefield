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
  public static function defaultSettings() {
    $library = \Drupal::service('library.discovery')->getLibraryByName('timefield', 'timepicker');
    return array(
      'disable_plugin' => empty($library) ? TRUE : FALSE,
      'separator' => ':',
      'showLeadingZero' => FALSE,
      'showPeriod' => FALSE,
      'periodSeparator' => '',
      'am_text' => 'AM',
      'pm_text' => 'PM',
      'showCloseButton' => FALSE,
      'closeButtonText' => 'Close',
      'showNowButton' => FALSE,
      'nowButtonText' => 'Now',
      'showDeselectButton' => FALSE,
      'deselectButtonText' => 'Deselect',
      'myPosition' => 'left top',
      'atPosition' => 'left bottom',

    ) + parent::defaultSettings();
  }

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
      $element['#attached']['drupalSettings']['timefield'][$instance_class] = $js_settings;
    }

    $element += array(
      '#delta' => $delta,
    );

    $value = isset($items[$delta]) ? timefield_integer_to_time($instance_settings, $items[$delta]->value) : '';
    $element['value'] = array(
      '#type' => 'textfield',
      '#title' => \Drupal\Component\Utility\Xss::filter($element['#title']),
      '#description' => \Drupal\Component\Utility\Xss::filter($element['#description']),
      '#default_value' => $value,
      '#required' => $element['#required'],
      '#weight' => (isset($element['#weight'])) ? $element['#weight'] : 0,
      '#delta' => $delta,
      '#element_validate' => array('timefield_time_validate'),
      '#attributes' => array(
        'class' => array(
          'edit-timefield-timepicker',
          $instance_class
        )
      ),
    );

    return $element;
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $library = \Drupal::service('library.discovery')
      ->getLibraryByName('timefield', 'timepicker');
    if (empty($library)) {
      drupal_set_message("You will not have enhanced time input widget without downloading the plugin. " . \Drupal::l("Read installation instructions here.", \Drupal\Core\Url::fromUri('http://drupalcode.org/project/timefield.git/blob_plain/HEAD:/README.txt')), 'warning');
    }
    $elements['disable_plugin'] = array(
      '#title' => t('Disable jQuery Timepicker plugin.'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('disable_plugin'),
      '#description' => t('Do not use jQuery Timepicker plugin for input.'),
      '#disabled' => (empty($library)),
    );

    $elements['input_format'] = array(
      '#title' => t('Time Input Format'),
      '#type' => 'fieldset',
    );
    $elements['input_format']['separator'] = array(
      '#title' => t('Hour and Minute Separator'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('separator'),
      '#size' => 10,
      '#description' => t('The character to use to separate hours and minutes.'),
    );
    $elements['input_format']['showLeadingZero'] = array(
      '#title' => t('Show Leading Zero for Hour'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('showLeadingZero'),
      '#description' => t('Whether or not to show a leading zero for hours < 10.'),
    );
    $elements['input_format']['showPeriod'] = array(
      '#title' => t('Show AM/PM Label'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('showPeriod'),
      '#description' => t('Whether or not to show AM/PM on the input textfield both on the widget and in the text field after selecting the time with the widget.'),
    );
    $elements['input_format']['periodSeparator'] = array(
      '#title' => t('What character should appear between the time and the Period (AM/PM)'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('periodSeparator'),
      '#size' => 10,
      '#description' => t('The character to use to separate the time from the time period (AM/PM).'),
    );
    $elements['input_format']['am_text'] = array(
      '#title' => t('AM text'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('am_text'),
      '#size' => 10,
    );
    $elements['input_format']['pm_text'] = array(
      '#title' => t('PM text'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('pm_text'),
      '#size' => 10,
    );
    $elements['input_format']['showCloseButton'] = array(
      '#title' => t('Show a Button to Close the Picker Widget'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('showCloseButton'),
      '#states' => array(
        'invisible' => array(
          ':input[name="fields[field_ido][settings_edit_form][settings][disable_plugin]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $elements['input_format']['closeButtonText'] = array(
      '#title' => t('Close Button text'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('closeButtonText'),
      '#size' => 10,
      '#states' => array(
        'invisible' => array(
          ':input[name="fields[field_ido][settings_edit_form][settings][disable_plugin]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $elements['input_format']['showNowButton'] = array(
      '#title' => t('Show a Button to Select the Current Time'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('showNowButton'),
      '#states' => array(
        'invisible' => array(
          ':input[name="fields[field_ido][settings_edit_form][settings][disable_plugin]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $elements['input_format']['nowButtonText'] = array(
      '#title' => t('Now Button text'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('nowButtonText'),
      '#size' => 10,
      '#states' => array(
        'invisible' => array(
          ':input[name="fields[field_ido][settings_edit_form][settings][disable_plugin]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $elements['input_format']['showDeselectButton'] = array(
      '#title' => t('Show a Button to Deselect the time in the Picker Widget'),
      '#type' => 'checkbox',
      '#default_value' => $this->getSetting('showDeselectButton'),
      '#states' => array(
        'invisible' => array(
          ':input[name="fields[field_ido][settings_edit_form][settings][disable_plugin]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $elements['input_format']['deselectButtonText'] = array(
      '#title' => t('Deselect Button text'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('deselectButtonText'),
      '#size' => 10,
      '#states' => array(
        'invisible' => array(
          ':input[name="fields[field_ido][settings_edit_form][settings][disable_plugin]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $elements['input_format']['myPosition'] = array(
      '#title' => t('my Position'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('myPosition'),
      '#options' => array_combine(array(
        'left top',
        'left center',
        'left bottom',
        'center top',
        'center center',
        'center bottom',
        'right top',
        'right center',
        'right bottom'
      ), array(
        'left top',
        'left center',
        'left bottom',
        'center top',
        'center center',
        'center bottom',
        'right top',
        'right center',
        'right bottom'
      )),
      '#description' => t('Corner of the timpicker widget dialog to position. See !jquery_info for more info.', array('!jquery_info' => \Drupal::l(t("jQuery UI Position documentation"), \Drupal\Core\Url::fromUri('http://jqueryui.com/demos/position')))),
      '#states' => array(
        'invisible' => array(
          ':input[name="fields[field_ido][settings_edit_form][settings][disable_plugin]"]' => array('checked' => TRUE),
        ),
      ),
    );
    $elements['input_format']['atPosition'] = array(
      '#title' => t('at Position'),
      '#type' => 'select',
      '#options' => array_combine(array(
        'left top',
        'left center',
        'left bottom',
        'center top',
        'center center',
        'center bottom',
        'right top',
        'right center',
        'right bottom'
      ), array(
        'left top',
        'left center',
        'left bottom',
        'center top',
        'center center',
        'center bottom',
        'right top',
        'right center',
        'right bottom'
      )),
      '#default_value' => $this->getSetting('atPosition'),
      '#description' => t('Where to position "my Position" relative to input widget textfield See !jquery_info for more info.', array('!jquery_info' => \Drupal::l(t("jQuery UI Position documentation"), \Drupal\Core\Url::fromUri('http://jqueryui.com/demos/position')))),
      '#states' => array(
        'invisible' => array(
          ':input[name="fields[field_ido][settings_edit_form][settings][disable_plugin]"]' => array('checked' => TRUE),
        ),
      ),
    );

    return $elements;
  }
}
