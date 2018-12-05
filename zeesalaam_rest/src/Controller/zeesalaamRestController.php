<?php
/**
 * @file
 * @author Zee Media Developers
 * Contains \Drupal\zeesalaam\Controller\zeesalaamRestController.
 * Please place this file under your zeesalaam(module_root_folder)/src/Controller/
 */
namespace Drupal\zeesalaam_rest\Controller;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\taxonomy\Entity\Term;
/**
 * Provides route responses for the Example module.
 */
class zeesalaamRestController extends ControllerBase {
	/**
	* Entity query factory.
	*
	* @var \Drupal\Core\Entity\Query\QueryFactory
	*/
	protected $entityQuery;

	/**
	* Constructs a new CustomRestController object.

	* @param \Drupal\Core\Entity\Query\QueryFactory $entityQuery
	* The entity query factory.
	*/
	public function __construct(QueryFactory $entity_query) {
		$this->entityQuery = $entity_query;
	}
	/**
	* {@inheritdoc}
	*/
	public static function create(ContainerInterface $container) {
		return new static(
			$container->get('entity.query')
		);
	}
	/**
	* Return the 50 most recently updated nodes in a formatted JSON response.
	*
	* @return \Symfony\Component\HttpFoundation\JsonResponse
	* The formatted JSON response.
	*/
	public function getData() {
	// Initialize the response array.
		$response_array = [];
		
	    $response_array['partnerName'] = 'Zee Salaam';
	    $response_array['partnerCode'] = '1';
	    $response_array['itemsCount'] = '50';
		//$response_array['items'] = ;
		$node_query = $this->entityQuery->get('node')
		 ->condition('status', 1)
		 ->condition('type', 'videos')
		 ->sort('changed', 'DESC')
		 ->range(0, 50)
		 ->execute();
		 
		if ($node_query) {
			$nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($node_query);
			foreach ($nodes as $node) {
			//echo "<pre>";
            //print_r(file_create_url($node->field_image->entity->getFileUri()));
            //die; 
			if($node->field_isyoutube->value ==1) {
				$autoPlay = false;
				//$ytCode = '<iframe width="560" height="315" src="https://www.youtube.com/embed/" '.$node->field_yt_code->value.'"></iframe>';
				$ytCode ="<iframe width='560' height='315' src='https://www.youtube.com/embed/".$node->field_yt_code->value."' frameborder='0'></iframe>";
				$embeddable = true;
				$duration = $node->field_video_duration->value;
				$videoSource = 'youtube';
				
			} else {
				$autoPlay = true;
				$ytCode = 'http://vodakm.zeenews.com/vod/'.$node->field_yt_code->value.'mp4/index.m3u8';
				$embeddable = false;
				$duration = '';
				if(!empty($node->get('field_news_source')->target_id)) {
					$term1 = Term::load($node->get('field_news_source')->target_id);
					$videoSource = $term1->getName();
				} else {
				 $videoSource = '';	
				}
			}
			$imageurl = file_create_url($node->field_image->entity->getFileUri());
			$createdDate = date('Y-m-d H:i:s',$node->created->value);
            $term = Term::load($node->get('field_video_section')->target_id);			
			$response_array['items'][] = [
			'ageRestrict' => 'false',
			'autoPlay' => $autoPlay,
			'categories' => $term->getName(),
			'contentURL' => $ytCode,
			'embeddable' => $embeddable,
			'embedHtml' => '',
			'definition' => '',
			'description' => '',
			'duration' => $duration,
			'langKey' => $node->langcode->value,
			'publishDate' => $createdDate,
			'source' => $videoSource,
			'title' => htmlspecialchars($node->title->value),
			'contentType' => 'TVVIDEO',
			'targetExtension' => 'M3U8',
			'statistics' => "{'viewCount': 0,'likeCount': 0,'disLikeCount': 0,'commentCount': 0,'favouriteCount': 0}",
			'images' => "[{'url':".$imageurl.",'width':700,'height':400}]",
			'thumbnails' => "[{'url':".$imageurl.",'width':700,'height':400}]",
			];
			}
		}
		else {
			// Set the default response to be returned if no results can be found.
			$response_array = ['message' => 'No new nodes.'];
		}
		// Add the node_list cache tag so the endpoint results will update when nodes are updated.
		$cache_metadata = new CacheableMetadata();
		$cache_metadata->setCacheTags(['node_list']);
		// Create the JSON response object and add the cache metadata.
		$response = new CacheableJsonResponse($response_array);
		$response->addCacheableDependency($cache_metadata);
       
		return $response;
	}
   
  
}
?>