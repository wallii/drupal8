<?php
/**
 * @file
 * Contains \Drupal\demo\Form\ContributeForm.
 */

namespace Drupal\demo\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use  \Drupal\user\Entity\User;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
/**
 * Contribute form.
 */
class BasicAdminConfiguration extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
	  return 'basic_admin_configuration';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	  
			$ids = \Drupal::entityQuery('user')
			->condition('status', 1)
			->execute();
			$users = User::loadMultiple($ids);
			$allroles = array(''=>'--Select Roles--');
			$roleR = \Drupal::entityTypeManager()->getStorage('user_role')->loadMultiple();
			foreach($roleR as $key=>$value) {
				 $allrol =  $value->get('id');
				 $allroles[$allrol] = $allrol;
			} 
	  
	    $config = $this->config('demo.settings');
		
		$form['select_roles'] = array(
		'#title' => t('Select Role'),
		'#type' => 'select',
		'#description' => 'Select the Role',
		'#options' => $allroles,
		'#required' => TRUE,
		 '#ajax'          => [
            'callback'  => 'Drupal\demo\Form\BasicAdminConfiguration::get_all_user_list',
            'event'     => 'change',
            'wrapper'   => 'user-list-replace',
        ],
		);
		
		$form['user_list'] = array(
		'#type' => 'select',
		'#title' => t('Select User'),
		'#prefix' => '<div id="user-list-replace">',
        '#suffix' => '</div>',
        '#options' => array(''=>'--select user--'),
		'#validated' => True,
		);
		$form['content_type'] = array(
		'#type' => 'textfield',
		'#title' => t('Content Type'),
		'#required' => TRUE,
		'#default_value' => $config->get('contentType'),
		);
		
		$form['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Submit'),
		);
		return $form;
	  
  }
  
  public function get_all_user_list(array &$form, FormStateInterface $form_state) {
	   //$ajax_response = new AjaxResponse();
	   $roleName = $form_state->getValues()['select_roles'];
		$user_ids = \Drupal::entityQuery('user')
		->condition('status', 1)
		->condition('roles', $roleName)
		->execute();
		if(!empty($user_ids)) {
			//$option = $user_ids;
			$option = array(''=>'--select user--');
			foreach($user_ids as $uid) {
				$account = \Drupal\user\Entity\User::load($uid); // pass your uid
                $name = $account->getUsername();
				$option[$uid] = $name; 
			}
			
		} else {
			$option = array(''=>'--select user--');
		}
		/*
	    $form['user_list'] = array(
		'#type' => 'select',
		'#title' => t('Select User'),
		'#prefix' => '<div id="user-list-replace">',
        '#suffix' => '</div>',
        '#options' => $option,
		//'#attributes' => ['name' => 'user_list'],
		); 
        */
		$form_state->setRebuild(TRUE);
		$form['user_list']['#options'] = $option;
        	
      /*$elements =  array(
            $form['user_list']
     );
	 */

     return $form['user_list'];
}

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	  
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	   /*
	   $config = $this->config('demo.settings');
	  
	   $rolE = $form_state->getValues()['role'];
	    
	   $contType = $form_state->getValues()['content_type'];
	   
	   $this->configFactory->getEditable('demo.settings')
	   ->set('roleName', $rolE)
	   ->set('contentType', $contType)
	   ->save();
     */
	 $insert = db_insert('user_node_permissions')
		-> fields(array(
			'user_id' => $form_state->getValues()['user_list'],
			'role_id' => $form_state->getValues()['select_roles'],
			'content_machine_name' => $form_state->getValues()['content_type'],
		))
		->execute();
	
	  drupal_set_message(t('Settings have been saved'));
	 
	  return new RedirectResponse('admin/config/development/demo');
	   /*
	   foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
     } */
  }
}
?>