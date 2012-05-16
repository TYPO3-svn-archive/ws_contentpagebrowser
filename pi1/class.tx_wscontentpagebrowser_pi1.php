<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Nikolay Orlenko (info@web-spectr.com)
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
 * Plugin 'WS content with pagebrowser' for the 'ws_contentpagebrowser' extension.
 *
 * This extension enables the administrator to show content from a specific
 * column of the Typo3 BE or from specific system folder with pagebrowser. 
 * This extension works only with "pagebrowse" by D.Dulepov. 
 * 
 *
 * @author  Nikolay Orlenko <info@web-spectr.com>
 */


require_once(PATH_tslib."class.tslib_pibase.php");

class tx_wscontentpagebrowser_pi1 extends tslib_pibase {
  var $prefixId = "tx_wscontentpagebrowser_pi1";    // Same as class name
  var $scriptRelPath = "pi1/class.tx_wscontentpagebrowser_pi1.php"; // Path to this script relative to the extension dir.
  var $extKey = "ws_contentpagebrowser";  // The extension key.
  var $pi_checkCHash = TRUE;
    
  /**
   * The "main" method
   * This method looks for content in sustem folder. 
   * If system folder is not set, then method get content from current page and current column
   * 
   * @param string  $content  Unused
   * @param array $conf Configuration
   * @return  string  Generated content
   */  
  function main($content,$conf) {
    $this->init($conf);  
    
    if (!is_array($this->conf['content.'])) {
      return $this->pi_wrapInBaseClass($this->pi_getLL('no_ts_template'));
    }
    
    $content = $this->getContent();
    $pagebrowser = $this->getPageBrowserContent($this->conf['numberOfPages']);
    
    return $content . $pagebrowser;    
  }
  
  /**
   * Gedo init staff.
   * @param   array $conf plugin configuration
   * @return  void
   */  
  
  function init($conf){
    $flexFormConf = $piFlexForm = array();    
    $this->pi_loadLL();
    $this->conf = $conf;
    
    // Init and get the flexform data of the plugin
    $this->pi_initPIflexForm(); 
    $this->lConf = array(); // Setup our storage array...
    // Assign the flexform data to a local variable for easier access
    $piFlexForm = $this->cObj->data['pi_flexform'];
    
    // Traverse the entire array based on the language...
    // and assign each configuration option to $this->lConf array...
    if (is_array($piFlexForm['data'])) {
      foreach ( $piFlexForm['data'] as $sheet => $data ) {
        if(is_array($data)) {
          foreach ( $data as $lang => $value ) {
            if(is_array($value)) {
              foreach ( $value as $key => $val ) {
                  $flexFormConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
              }
            }  
          }
        }  
      } 
    }
    
    // Set storage pid. 1: from typoscrip, 2: from flexform, 3:current page
    if ($this->conf['storagePid']) {
      $this->conf['storagePid'] = intval($this->conf['storagePid']);
    }
    if ($flexFormConf['storagePid']) {
      $this->conf['storagePid'] = intval($flexFormConf['storagePid']);
    }
    if (!$this->conf['storagePid'] && intval($this->conf['content.']['select.']['pidInList'])) {
      $this->conf['storagePid'] = intval($this->conf['content.']['select.']['pidInList']);
    }    
    if (!$this->conf['storagePid'] && !intval($this->conf['content.']['select.']['pidInList'])) {
      $this->conf['storagePid'] = $GLOBALS["TSFE"]->id;
    }    
    
    // Set limit value. 1: from typoscrip, 2: from flexform, 3:current page
    if ($this->conf['limit']) {
      $this->conf['limit'] = intval($this->conf['limit']);
    }
    if ($flexFormConf['limit']) {
      $this->conf['limit'] = intval($flexFormConf['limit']);  
    }
    if (!$this->conf['limit'] && intval($this->conf['content.']['select.']['max'])) {
      $this->conf['limit'] = intval($this->conf['content.']['select.']['max']);
    }
    if (!$this->conf['limit'] && !intval($this->conf['content.']['select.']['max'])) {
      $this->conf['limit'] = 10;
    }    
  }  
    
  /**
   * Get content as CType CONTENT.
   *
   * @return  string  Generated content
   */  
  function getContent() {
    $content = '';
    
    //Store original content pid
    $orig_contentPid = $GLOBALS["TSFE"]->contentPid;
    //Set content pid to storagePid
    $GLOBALS["TSFE"]->contentPid = $this->conf['storagePid'];
    $this->conf['content.']['select.']['pidInList'] = $this->conf['storagePid'];    
    //Count content for pagination
    $res = $this->cObj->exec_getQuery($this->conf['content.']['table'], $this->conf['content.']['select.']);
    $this->conf['count'] = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
    
    $this->conf['begin'] = $this->piVars['page'] ? $this->piVars['page'] * $this->conf['limit'] : 0 ;
    $this->conf['numberOfPages'] = ceil($this->conf['count'] / $this->conf['limit']);    
    
    $this->conf['content.']['select.']['begin'] = $this->conf['begin'];
    $this->conf['content.']['select.']['max'] = $this->conf['limit'];
    
    $content = $this->cObj->cObjGetSingle($this->conf['content'], $this->conf['content.']);
    if ($error = $GLOBALS['TYPO3_DB']->sql_error()) {
      $content .= $error;
    }
    $GLOBALS["TSFE"]->contentPid = $orig_contentPid;
    
    return $content;   
  }
  
  /**
   * Get pagebrowser content using "pagebrowse" configuration.
   * @param   int $numberOfPages number of pages in browser
   * @return  string  Generated content
   */  
  function getPageBrowserContent($numberOfPages){    
    // Get default configuration
    $conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_pagebrowse_pi1.'];    
    // Modify this configuration
    $conf['pageParameterName'] = $this->prefixId . '|page';
    $conf['numberOfPages'] = $numberOfPages;
        
    return $this->cObj->cObjGetSingle('USER', $conf);    
  }
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/ws_contentpagebrowser/pi1/class.tx_wscontentpagebrowser_pi1.php"]) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/ws_contentpagebrowser/pi1/class.tx_wscontentpagebrowser_pi1.php"]);
}

?>