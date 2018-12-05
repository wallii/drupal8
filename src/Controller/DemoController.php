<?php
/**
 * @file
 * @author Waliullah Khan
 * Contains \Drupal\demo\Controller\DemoController.
 * Please place this file under your demo(module_root_folder)/src/Controller/
 */
namespace Drupal\demo\Controller;
/**
 * Provides route responses for the Example module.
 */
class DemoController {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function autopublishedArticle() {
	$database = \Drupal::database();

    $query = $database->select('node_field_data', 'n');
	
	$query->join('node__field_publish_later','e','e.entity_id = n.nid');
	$query->join('node__field_live_blog_date','p','p.entity_id = n.nid');
	$query->fields('n', ['nid','title']);
	$query->fields('p', ['field_live_blog_date_value']);
	$query->condition('n.status',0);
	$query->condition('p.field_live_blog_date_value', time(), '<');
	$query->condition('e.field_publish_later_value',1);
	$query->orderBy('p.field_live_blog_date_value', 'asc');
	$query->range(0,49);
    $emb_nodes = $query->execute();
    while($record = $emb_nodes->fetchAssoc()){
		
		$nid[$record['nid']] = $record['title'];
		
		# Publish and save node
		//$node->created=$record['field_live_blog_date_value'];
		//$node->changed=$record['field_live_blog_date_value'];
		//$node->status = 1;
		//node_save($node);
		//echo "hello";
		//print_r($nid);
		$node = \Drupal\node\Entity\Node::load($record['nid']);
		$node->set("created", $record['field_live_blog_date_value']);
        $node->set("changed", $record['field_live_blog_date_value']);
        $node->setPublished(TRUE);
		$node->save();
		
	}
   
    $element = array(
      '#markup' => 'Hello',
    );
	//echo "<pre>";
	print_r(@$nid);
    return $element;
	
  }
}
?>