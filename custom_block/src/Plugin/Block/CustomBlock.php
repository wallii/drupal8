<?php
/**
 * @file
 * @author Waliullah Khan
 * Contains \Drupal\custom_block\Plugin\Block\CustomBlock.
 * Please place this file under your Custom_block(module_root_folder)/src/Plugin/Block
 */
namespace Drupal\custom_block\Plugin\Block;
use Drupal\Core\Block\BlockBase;
/**
 * Provides a 'Custom' Block.
 *
 * @Block(
 *   id = "custom_block",
 *   admin_label = @Translation("Custom Block"),
 *   category = @Translation("Hello World"),
 * )
 */
class CustomBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
   public function build() {
    return array(
      '#markup' => $this->t('Hi my number is @number!,@name and my address @town', 
       array (
         '@number' => 9643603701, '@name' => 'Waliullah Khan', '@town' => 'Noida',
       )
       ),
	   );
  }

}
?>