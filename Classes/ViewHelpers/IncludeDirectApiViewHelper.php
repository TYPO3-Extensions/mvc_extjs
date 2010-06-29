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
 * View helper which allows 
 *
 * = Examples =
 *
 * <mvcextjs:be.moduleContainer pageTitle="foo" enableJumpToUrl="false" enableClickMenu="false" loadPrototype="false" loadScriptaculous="false" scriptaculousModule="someModule,someOtherModule" loadExtJs="true" loadExtJsTheme="false" extJsAdapter="jQuery" enableExtJsDebug="true" addCssFile="{f:uri.resource(path:'styles/backend.css')}" addJsFile="{f:uri.resource('scripts/main.js')}">
 * 	<mvcextjs:includeDirectApi />
 * </mvcextjs:be.moduleContainer>
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  tx_mvcextjs
 * @author      Dennis Ahrens <dennis.ahrens@fh-hannover.de>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id: IncludeInlineJsFromFileViewHelper.php 30242 2010-02-20 14:32:48Z xperseguers $
 */
class Tx_MvcExtjs_ViewHelpers_IncludeDirectApiViewHelper extends Tx_MvcExtjs_ViewHelpers_AbstractViewHelper {
	
	/**
	 * @var array
	 */
	protected $directApiCache;
	
	/**
	 * @var array
	 */
	protected $frameworkConfiguration;
	
	/**
	 * @see Classes/Core/ViewHelper/Tx_Fluid_Core_ViewHelper_AbstractViewHelper#initializeArguments()
	 */
	public function initializeArguments() {
		$this->frameworkConfiguration = Tx_Extbase_Dispatcher::getExtbaseFrameworkConfiguration();
		$this->directApiStorageKey = md5('Tx_MvcExtjs_ExtDirect_API_' . $this->frameworkConfiguration['pluginName']);;
	}
	
	/**
	 * Generates a Ext.Direct API descriptor and adds it to the pagerenderer.
	 * Also calls Ext.Direct.addProvider() on itself (at js side).
	 * The remote API is directly useable.
	 * 
	 * TODO: handle calls from FE (routeUrl)
	 * 
	 * @param string $name The name for the javascript variable.
	 * @param string $namespace The namespace the variable is placed.
	 * @param string $routeUrl You can specify a URL that acts a router. Default is mvc_extjs's DirectDispatcher
	 * @param boolean $cache
	 * 
	 * @return void
	 */
	public function render($name = 'remoteDescriptor',
						   $namespace = 'Ext.ux.TYPO3.app',
						   $routeUrl = NULL,
						   $cache = TRUE
						   ) {
		
		if ($routeUrl === NULL) {
			$pluginName = $this->controllerContext->getRequest()->getPluginName();
			$uriBuilder = $this->controllerContext->getUriBuilder();
			$arguments = array(
				'M' => 'MvcExtjsDirectDispatcher',
				'tx_mvcextjs_dispatcher[module]' => $pluginName
			);
			$routeUrl = $uriBuilder->reset()->setAddQueryString(TRUE)->setArguments($arguments)->build();
		}
		
		$cacheHash = md5($this->directApiStorageKey . $namespace . serialize($this->frameworkConfiguration['switchableControllerActions']));
		$cachedApi = ($cache) ? t3lib_pageSelect::getHash($cacheHash) : FALSE;
		
		if ($cachedApi) {
			$api = unserialize(t3lib_pageSelect::getHash($cacheHash));
		} else {
			$api = $this->createApi($routeUrl,$namespace);
			t3lib_pageSelect::storeHash($cacheHash,serialize($api),$this->directApiStorageKey);
		}
			// prepare output variable
		$jsCode = '';
		$descriptor = $namespace . '.' . $name;
			// build up the output
		$jsCode .= 'Ext.ns(\'' . $namespace . '\'); ' . "\n";
		$jsCode .= $descriptor . ' = ';
        $jsCode .= json_encode($api);
        $jsCode .= ";\n";
        $jsCode .= 'Ext.Direct.addProvider(' . $descriptor . ');' . "\n";
        	// add the output to the pageRenderer
        $this->pageRenderer->addExtOnReadyCode($jsCode,TRUE);
	}
	
	/**
	 * Creates the remote api based on the module/plugin configuration.
	 * 
	 * @param string $routeUrl
	 * @param string $namespace
	 * @return array
	 */
	protected function createApi($routeUrl,$namespace) {
		$api = array();
		$api['url'] = $routeUrl;
		$api['type'] = 'remoting';
		$api['namespace'] = $namespace;
		$api['actions'] = array();
		$reflectionService = Tx_MvcExtjs_DirectDispatcher::getReflectionService();
		foreach ($this->frameworkConfiguration['switchableControllerActions'] as $allowedControllerActions) {
			$controllerName = $allowedControllerActions['controller'];
			$unstrippedControllerName = $controllerName . 'Controller';
			$controllerObjectName = 'Tx_' . $this->controllerContext->getRequest()->getControllerExtensionName() . '_Controller_' . $unstrippedControllerName;
			$actions = explode(',',$allowedControllerActions['actions']);
			$controllerActions = array();
			foreach ($actions as $actionName) {
				$unstrippedActionName = $actionName . 'Action';
				try  {
					$actionParameters = $reflectionService->getMethodParameters($controllerObjectName,$unstrippedActionName);
					$controllerActions[] = array(
						'len' => count($actionParameters),
						'name' => $unstrippedActionName
					);
				} catch (ReflectionException $re) {
					if ($unstrippedActionName !== 'extObjAction') {
						t3lib_div::sysLog('You have a not existing action (' . $controllerObjectName . '::' . $unstrippedActionName . ') in your module/plugin configuration. It will not be available for Ext.Direct remote execution.','MvcExtjs',1);
					}
				}
			}
			$api['actions'][$unstrippedControllerName] = $controllerActions;
		}
		return $api;
	}

}

?>