<?php
/**
 * @file
 * Contains \Drupal\zeesalaam\Theme\ZeesalaamNegotiator.
 */
namespace Drupal\zeesalaam\Theme;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

class ZeesalaamNegotiator implements ThemeNegotiatorInterface {
   
  public function applies(RouteMatchInterface $route_match) {
	  $applies = FALSE;
	  $uc = FALSE;
	  if(isset($_GET['content'])){
		if($_GET['content']=='uc'){
			$uc = true;
		}
	 }
	 if (isset($_SERVER['HTTP_X_CUA']) && $uc==false) {
	
		if ($_SERVER['HTTP_X_CUA'] == 'mobile_version')
		{
			$applies = TRUE;
		}
	} elseif(isset($_SERVER['HTTP_IS_DESKTOP'])) {
		
		if ($_SERVER['HTTP_IS_DESKTOP'] == 'desktop_version') {
		
			$applies = FALSE;
		}
	}
	if(isset($_GET['desktop'])){	 
	  $applies = FALSE;	  
	}
	if(isset($_GET['theme'])){	 
	  $applies = TRUE;	  
	}
	
    return $applies;
  }
 
  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return 'zeemobile_th';
  }
} 
 ?>
 