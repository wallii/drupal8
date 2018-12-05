<?php
/**
 * @file
 * @author Zee Media Developers
 * Contains \Drupal\zeesalaam\Controller\ZeesalaamController.
 * Please place this file under your zeesalaam(module_root_folder)/src/Controller/
 */
namespace Drupal\zeesalaam\Controller;
use \Drupal\node\Entity\Node;
use \Drupal\file\Entity\File;
/**
 * Provides route responses for the Example module.
 */
class ZeesalaamController {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function autopublishedNews() {
	$database = \Drupal::database();

    $query = $database->select('node_field_data', 'n');
	
	$query->join('node__field_publish_later','e','e.entity_id = n.nid');
	$query->join('node__field_publish_at','p','p.entity_id = n.nid');
	$query->fields('n', ['nid','title']);
	$query->fields('p', ['field_publish_at_value']);
	$query->condition('n.status',0);
	$query->condition('p.field_publish_at_value', time(), '<');
	$query->condition('e.field_publish_later_value',0);
	$query->orderBy('p.field_publish_at_value', 'asc');
	$query->range(0,49);
    $emb_nodes = $query->execute();
	$row1 = '';
    while($record = $emb_nodes->fetchAssoc()){
		
		$nid[$record['nid']] = $record['title'];
		$row1.= '<tr><td>'.$record['nid'].'</td><td>'.$record['title'].'</td></tr>';
		# Publish and save node
		$node = \Drupal\node\Entity\Node::load($record['nid']);
		$node->set("created", $record['field_publish_at_value']);
        $node->set("changed", $record['field_publish_at_value']);
		$node->set("field_publish_later", 1);
        $node->setPublished(TRUE);
		$node->save();
		
	}
   
   $header = '<tr><th>NID</th><th>TITLE</th></tr>';
	$rows = '<p>' . t('Auto Published') . '</p>'.'<table>'.$header.$row1.'</table>';
	
	$my_dynamic_html = '<ul>'.$rows.'</ul>';
	$render_array = array(
		'#markup' => $my_dynamic_html,
		'#prefix' => '<div>',
		'#suffix' => '</div>',
	);
	
	//echo "<pre>";
	//print_r(@$nid);
    return $render_array;
	
  }
	/**
	* Returns a simple page.
	* Video Auto Published with transcode
	* @return array
	*   A simple renderable array.
	*/
   public function autoPublishedFromTranscode() {
	//use \Drupal\node\Entity\Node;
    //use \Drupal\file\Entity\File;
	$_domain = array(
	'1' => 'Entertainment',
	'4' => 'Zee Salam',
	'2' => 'Religious',
	);  
	
	$title = '';
	$description = '';
	$url_video = '';
	$thumbnails = '';
	# local file path
	//$_filepath = 'C:/xampp/htdocs/zee-salaam/news/video/videoautopost_'.date('Ymd').'.txt';
	//$_nextfilepath = 'C:/xampp/htdocs/zee-salaam/news/video/videoautopost_'.date('Ymd', strtotime(' +1 day')).'.txt';
	# dev Server File Path
	$_filepath = '/var/www/zeesalaam.com/news/videos/videoautopost_'.date('Ymd').'.txt';
	$_nextfilepath = '/var/www/zeesalaam.com/news/videos/videoautopost_'.date('Ymd', strtotime(' +1 day')).'.txt';
	
	if(!file_exists($_nextfilepath)){		
		$_nxtvideoContent = file_put_contents($_nextfilepath, "");	
	}
	if(!file_exists($_filepath)){
		$_videoContent = file_put_contents($_filepath, "");	
	}
	$_videoContent = file_get_contents($_filepath);
	$count = 1;
	
	$json = file_get_contents("http://transcoding.zeenews.com/get_last_24h/6");
	$jsonIterator =json_decode($json, TRUE);
	$row1= '';
	foreach ($jsonIterator as $key => $val) {
			$_videoContent = file_get_contents($_filepath);
			
			if(is_array($val)){
				
				$title = $val['title'];
				$reg_title = $val['regional_title'];
				$description = $val['description'];
				$section = trim($val['section']);
				$tags = $val['tags'];
				# find summary from body description
			    $pos=stripos($description,"\n");
				$mybody= substr($description,0,$pos);
				$pos =stripos($mybody,"...");
				
				#
				if(isset($val['thumbnails']['1'])){
					$thumbnails = $val['thumbnails']['1'];
				
				}else{
					$thumbnails = $val['thumbnails']['0'];
				}
				
				
				if(empty($title)==false && strlen($title)>1 && empty($reg_title)==false && strlen($reg_title)>1 && empty($section)==false && strlen($thumbnails)>10){
					$url_video = $val['url_video'];
					$exp = explode(".mp4",$url_video);
					//echo "<pre>";
					$expsub = $exp[0];
					$expfinal = explode('/',$expsub);
					$countnumber = count($expfinal);
					$ytcode = $expfinal[$countnumber-1];
					$finalyt =  rtrim($ytcode,".");
					
					$querystring = strpos($url_video, ".m3u8");
					$_filenameW = substr($url_video, 0, $querystring+4);
					$pos = strpos($_videoContent, $_filenameW);
					$_tid =  array_search($section,$_domain);
					if(empty($_tid) || strlen($_tid)<1){
					$_tid = 4;
				    }
					$timenow = date('d-m-Y H:i:s');
                    $timestamp = strtotime($timenow);
					if($pos==false){
						# Image Upload
						$current_date =  date("Y/m/d");
						$directory = 'public://'.$current_date;
						if(file_prepare_directory($directory, FILE_CREATE_DIRECTORY)) {
						$imgPath = system_retrieve_file($thumbnails, $directory, False, FILE_EXISTS_RENAME);
						}else{
						$imgPath = system_retrieve_file($thumbnails, $directory, False, FILE_EXISTS_RENAME);
						}
						if ($thumbnails) {
							$file = File::create([
								'filename' => basename($imgPath),
								'filepath' => $imgPath,
								'filemime' => \Drupal::service('file.mime_type.guesser')->guess($imgPath)  ,
								'filesize' => filesize($imgPath),
								'uid' => 1,
								'timestamp' => time(),
								'uri' => $imgPath,
								'status' => FILE_STATUS_PERMANENT,
							]);
							$file->save();
							$fid = $file->id();
						} 
						# End Image Upload
						$count = $count + 1;
						$my_article = Node::create(['type' => 'videos']);
						$my_article->set('title', $reg_title);
                                                $my_article->set('field_mobile_title', $reg_title);
						$my_article->set('field_english_title', $title);
						$my_article->set('field_video_section', $_tid);
						$my_article->set('field_yt_code', $finalyt);
						$my_article->set('field_news_source', 4);
						$my_article->set('field_publish_later', 1);
						$my_article->set('field_publish_at', $timestamp);
						$my_article->set('uid', 1);
						$my_article->set('body',array(
						                              'value' => $description,
                                                      'format' => 'basic_html',
													  'summary' => $description
													  )
										);
						$my_article->set('field_isyoutube', 0);
						$my_article->set("field_image", [
							'target_id' => $fid,
							'alt' => $title,
							'title' => $title
						]);
						$my_article->enforceIsNew();
						$my_article->save();
						$myfile = file_put_contents($_filepath, '**'. $section . '**' . $title . '**' . $_filenameW . '**' . $thumbnails .PHP_EOL , FILE_APPEND | LOCK_EX);
						$nxtfile = file_put_contents($_nextfilepath, '**'. $section . '**' . $title . '**' . $_filenameW.PHP_EOL , FILE_APPEND | LOCK_EX);
						
						$row1.= '<tr><td>'.$_filenameW.'</td><td>'.$title.'</td></tr>';
						if($count==10){
						break;
					    }
						
					}
					
				}
				
			}
	}
	
	$header = '<tr><th>Video Path</th><th>TITLE</th></tr>';
	$rows = '<p>' . t('Auto Published Video') . '</p>'.'<table>'.$header.$row1.'</table>';

	$my_dynamic_html = '<ul>'.$rows.'</ul>';
	$render_array = array(
	'#markup' => $my_dynamic_html,
	'#prefix' => '<div>',
	'#suffix' => '</div>',
	);
	return $render_array;
  }	
  
}
?>
