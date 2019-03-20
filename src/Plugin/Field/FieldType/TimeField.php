<?php

namespace Drupal\timefield\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'timefield' field type.
 *
 * @FieldType(
 *   id = "timefield",
 *   label = @Translation("Timefield"),
 *   module = "timefield",
 *   description = @Translation("This field stores a time in the database"),
 *   default_widget = "timefield_standard_widget",
 *   default_formatter = "timefield_default_formatter"
 * )
 */
class TimeField extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type'     => 'int',
          'not null' => FALSE,
          'default'  => NULL,
        ),
        'end_value' => array(
          'type'     => 'int',
          'not null' => FALSE,
          'default'  => NULL,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    $end_value = $this->get('end_value')->getValue();
    return ($value === NULL || $value === '') && ($end_value === NULL || $end_value === '');
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('integer')
      ->setLabel(t('Timefield'));
    $properties['end_value'] = DataDefinition::create('integer')
      ->setLabel(t('Timefield End Value'));
    return $properties;
  }
}
