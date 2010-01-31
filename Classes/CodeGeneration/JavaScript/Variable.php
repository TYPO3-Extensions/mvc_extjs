<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Dennis Ahrens <dennis.ahrens@googlemail.com>
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
 * JavaScript Code Snippet
 * Representing a JavaScript Variable
 *
 * @category    CodeGeneration_JavaScript
 * @package     TYPO3
 * @subpackage  tx_mvcextjs
 * @author      Dennis Ahrens <dennis.ahrens@googlemail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_MvcExtjs_CodeGeneration_JavaScript_Variable implements Tx_MvcExtjs_CodeGeneration_JavaScript_SnippetInterface {
	
	/**
	 * the name of the variable
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * the value of the variable
	 * 
	 * @var Tx_MvcExtjs_CodeGeneration_JavaScript_SnippetInterface
	 */
	protected $value;
	
	/**
	 * the namespace 
	 * 
	 * @var mixed string or FALSE
	 */
	protected $namespace;
	
	/**
	 * should we use the statement var in front of the name? 
	 * (i still do not really understand where the difference is; further informations: http://www.w3schools.com/js/js_variables.asp)
	 * 
	 * @var boolean
	 */
	protected $var;
	
	/**
	 * 
	 * @param string $name
	 * @param Tx_MvcExtjs_CodeGeneration_JavaScript_SnippetInterface $value
	 * @param boolean $var
	 * @return unknown_type
	 */
	public function __construct($name = NULL, $value = NULL,$var = FALSE, $namespace = FALSE) {
		$this->name = $name;
		$this->value = $value;
		$this->var = $var;
		$this->namespace = $namespace;
	}
	
	/**
	 * 
	 * @param $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;	
	}
	
	/**
	 * 
	 * @param Tx_MvcExtjs_CodeGeneration_JavaScript_SnippetInterface $value
	 * @return void
	 */
	public function setValue($value) {
		$this->value = $value;
	}
	
	/**
	 * 
	 * @return Tx_MvcExtjs_CodeGeneration_JavaScript_SnippetInterface
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * 
	 * @param string $value
	 * @return void
	 */
	public function setNamespace($value) {
		$this->namespace = $value;
	}
	
	/**
	 * resets the namespace
	 * 
	 * @return void
	 */
	public function resetNamespace() {
		$this->namespace = FALSE;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getNamespace() {
		return $this->namespace;
	}
	
	/**
	 * 
	 * @param boolean $var
	 * @return void
	 */
	public function setVar($var) {
		$this->var = $var;	
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function getVar() {
		return $this->var;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Classes/CodeGeneration/JavaScript/Tx_MvcExtjs_CodeGeneration_JavaScript_SnippetInterface#build()
	 */
	public function build() {
		if (!is_string($this->name) || $this->name == '')
			throw new Tx_MvcExtjs_CodeGeneration_JavaScript_Exception('building a variable without a name will cause errors in javascript - use another snippet if u just want the stuff on the right side of the "="',1264952776);
		$js = '';
		if ($this->namespace) {
			$this->var = false;
			$js .= $this->namespace . '.';
		}
		if ($this->var) $js .= 'var ';
		$js .= $this->name . ' = ' . $this->value->build() . "\n";
		return $js;
	}
	
}

?>