<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Dennis Ahrens <dennis.ahrens@fh-hannover.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * View helper which allows you to include an ExtJS Store based on the object notation
 * of a domain model
 * Note: This feature is experimental!
 * Note: You MUST wrap this Helper with <mvcextjs:be.moduleContainer>-Tags
 *
 * = Examples =
 *
 * <mvcextjs:be.moduleContainer pageTitle="foo" enableJumpToUrl="false" enableClickMenu="false" loadPrototype="true" loadScriptaculous="false" loadExtJs="true" loadExtJsTheme="false" extJsAdapter="prototype" enableExtJsDebug="true">
 * 	<mvcextjs:Be.IncludeStore domainModel="yourModelName" actions="{read:'yourActionForFetchingTheRecords',update:'yourActionForUpdatingRecords'}" controller="yourController" extensionName="yourExtensionName" />
 * </mvcextjs:be.moduleContainer>
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  tx_mvcextjs
 * @author      Dennis Ahrens <dennis.ahrens@fh-hannover.de>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_MvcExtjs_ViewHelpers_JsCode_HttpProxyViewHelper extends Tx_MvcExtjs_ViewHelpers_JsCode_AbstractJavaScriptCodeViewHelper {

	/**
	 * 
	 * @var Tx_MvcExtjs_CodeGeneration_JavaScript_ExtJS_ExtendClass
	 */
	protected $proxy;
	
	/**
	 * 
	 * @var Tx_MvcExtjs_CodeGeneration_JavaScript_ExtJS_Config
	 */
	protected $config;
	
	/**
	 * override this method to change the StoreType f.e.
	 * 
	 * @see Classes/ViewHelpers/Be/Tx_MvcExtjs_ViewHelpers_Be_AbstractJavaScriptCodeViewHelper#initialize()
	 */
	public function initialize() {
		parent::initialize();
		$this->config = new Tx_MvcExtjs_CodeGeneration_JavaScript_ExtJS_Config();
		$this->proxy = new Tx_MvcExtjs_CodeGeneration_JavaScript_ExtJS_ExtendClass('defaultProxyName',
																				   'Ext.data.HttpProxy',
																					array(),
																					$this->config,
																					new Tx_MvcExtjs_CodeGeneration_JavaScript_Object(),
																					$this->extJsNamespace);
	}
	
	/**
	 * Renders the js code for a store, based on a domain model into the inline JS of your module.
	 * The store automatically loads its data via AJAX.
	 * 
	 * @param string $domainModel is used as variable name AND storeId for the generated store
	 * @param string $extensionName the EXT where the domainModel is located
	 * @param string $id choose a id for the created variable; default is $domainmodel . 'HttpProxy'
	 * @param string $controller
	 * @param array $api
	 * @return void
	 */
	public function render($domainModel = NULL,
						   $extensionName = NULL,
						   $id = NULL,
						   $controller = NULL,
						   $api = array()) {
		if ($extensionName === NULL)
			$extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
		$domainClassName = 'Tx_' . $extensionName . '_Domain_Model_' . $domainModel;
			// Check if the given domain model class exists
		if (!class_exists($domainClassName)) {
			throw new Tx_Fluid_Exception('The Domain Model Class (' . $domainClassName . ') for the given domainModel (' . $domainModel . ') was not found', 1264069568);
		}
			// build up and set the for the JS store variable
		$varNameProxy = $domainModel . 'HttpProxy';
		$this->proxy->setName($varNameProxy);
			// read the given config parameters into the Extjs Config Object
		($id === NULL) ? $this->config->set('id',$varNameProxy) : $this->config->set('id',$id);
		
		$apiObject = new Tx_MvcExtjs_CodeGeneration_JavaScript_ExtJS_Config();
		$uriBuilder = $this->controllerContext->getUriBuilder();
		foreach ($api as $apiCall => $action) {
			switch ($apiCall) {
				case 'read':
				case 'new':
				case 'update':
				case 'destroy':
					// TODO: move the "hack" that allow ajax communication in FE to a better place
					$uri = $uriBuilder->reset()->uriFor($action,array('format' => 'json'), $controller) . '&type=1249117332';
					$apiObject->set($apiCall, $uri);
					break;
				default:
					throw new Tx_Fluid_Exception('The extjs HttpProxy-API only knows about read, new, update and destroy, your value: ' . $apiCall . ' is not supported',1264095568);
			}
		}
		$this->config->setRaw('api',$apiObject);
			// apply the configuration again
		$this->proxy->setConfig($this->config);
			// allow objects to be declared inside this viewhelper; they are rendered above
		$this->renderChildren();
			// add the code and write it into the inline section in your HTML head
		$this->jsCode->addSnippet($this->proxy);
		$this->injectJsCode();
	}

	
	/**
	 * Renders the proxy variable for the store.
	 * 
	 * $actions has to look like this:
	 * $actions = array(
	 * 'extjsApiCall' => 'yourAction',
	 * );
	 * 
	 * Supported extjsApiCalls are:
	 *  - read
	 *  - update
	 *  - new
	 *  - destroy
	 * 
	 * @param string $controller the ajax controller
	 * @param array $actions the actions for the controller associated with the apiCall from extjs
	 * @return string JS Code containing a Ext.data.HttpProxy Variable
	 */
	private function renderProxyVariable($controller = NULL, array $actions = array()) {
		$uriBuilder = $this->controllerContext->getUriBuilder();
		$jsConstructor = Tx_MvcExtjs_ExtJS_Constructor::create();
		$jsConstructor->setVarName($this->varNameProxy);
		$jsConstructor->setObjectName('Ext.data.HttpProxy');

		$apiObject = Tx_MvcExtjs_ExtJS_Object::create();
		foreach ($actions as $apiCall => $action) {
			switch ($apiCall) {
				case 'read':
				case 'new':
				case 'update':
				case 'destroy':
					$uri = $uriBuilder->reset()->uriFor($action,array('format' => 'json'), $controller);
					$apiObject->set($apiCall, $uri);
					break;
				default:
					throw new Tx_Fluid_Exception('The extjs HttpProxy-API only knows about read, new, update and destroy, your value: ' . $apiCall . ' is not supported',1264095568);
			}
		}
		$jsConstructor->addRawConfig('api',$apiObject);
		$jsOut = $jsConstructor->build();
		return $jsOut;
	}

}
?>