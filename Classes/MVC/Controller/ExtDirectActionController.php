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
 * A Controller used for answering via AJAX speaking JSON
 * 
 * @package     MvcExtjs
 * @subpackage  MVC/Controller
 * @author      Dennis Ahrens <dennis.ahrens@fh-hannover.de>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_MvcExtjs_MVC_Controller_ExtDirectActionController extends Tx_Extbase_MVC_Controller_ActionController {
	
	/**
	 * Initializes the View to be a Tx_MvcExtjs_ExtDirect_View that renders json without Template Files.
	 * 
	 * @return void
	 */
	public function initializeView() {
		$this->view = $this->objectManager->create('Tx_MvcExtjs_MVC_View_ExtDirectView');
		$this->view->setControllerContext($this->controllerContext);
	}
	
}
?>