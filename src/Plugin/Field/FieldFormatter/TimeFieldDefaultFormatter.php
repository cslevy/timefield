<?php /**
 * @file
 * Contains \Drupal\timefield\Plugin\Field\FieldFormatter\TimeFieldDefaultFormatter.
 */

namespace Drupal\timefield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * @FieldFormatter(
 *  id = "timefield_default_formatter",
 *  label = @Translation("Timefield formatter"),
 *  field_types = {"timefield"}
 * )
 */
class TimeFieldDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = array();
    foreach ($items as $delta => $item) {
      if (!empty($item->value)) {
        $element[$delta]['#markup'] = $item->value;
      }
    }


    return $element;
  }
}
