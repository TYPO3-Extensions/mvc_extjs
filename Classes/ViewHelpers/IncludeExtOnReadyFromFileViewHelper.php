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
 * View helper which allows you to include inline JS code into a module Container.
 * Note: This feature is experimental!
 * Note: You MUST wrap this Helper with <mvcextjs:Be.moduleContainer>-Tags or <mvcextjs:Fe.pluginContainer>-Tags
 *
 * = Examples =
 *
 * <mvcextjs:be.moduleContainer pageTitle="foo" enableJumpToUrl="false" enableClickMenu="false" loadPrototype="false" loadScriptaculous="false" scriptaculousModule="someModule,someOtherModule" loadExtJs="true" loadExtJsTheme="false" extJsAdapter="jQuery" enableExtJsDebug="true" addCssFile="{f:uri.resource(path:'styles/backend.css')}" addJsFile="{f:uri.resource('scripts/main.js')}">
 * 	<mvcextjs:includeExtOnReadyCode file="foo.js" extKey="blog_example" pathInsideExt="Resources/Public/JavaScript" />
 * </mvcextjs:be.moduleContainer>
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  tx_mvcextjs
 * @author      Dennis Ahrens <dennis.ahrens@fh-hannover.de>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_MvcExtjs_ViewHelpers_IncludeExtOnReadyFromFileViewHelper extends Tx_MvcExtjs_ViewHelpers_AbstractViewHelper {

	/**
	 * Calls addJsFile on the Instance of t3lib_pagerenderer.
	 * 
	 * @param string $name the file to include
	 * @param string $extKey the extension, where the file is located
	 * @param string $pathInsideExt the path to the file relative to the ext-folder
	 * @return void
	 */
	public function render($name = 'extOnReady.js', $extKey = NULL, $pathInsideExt = 'Resources/Public/JavaScript/') {
		if ($extKey == NULL) {
			$extKey = $this->controllerContext->getRequest()->getControllerExtensionKey();
		}
		$extPath = t3lib_extMgm::extPath($extKey);

		$filePath = $extPath . $pathInsideExt . $name;

		if (!file_exists($filePath)) {
			throw new Tx_MvcExtjs_Exception('File not found: ' . $filePath, 1264197781);
		}

		$fileContent = file_get_contents($extPath . $pathInsideExt . $name);

		$this->pageRenderer->addExtOnReadyCode($fileContent);
	}

}

?>