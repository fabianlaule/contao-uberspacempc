<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   UberspaceMPC
 * @author    Fabian Laule <fabianlaule.de>
 * @license   LGPL
 * @copyright Fabian Laule 2013
 */


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['UberspaceMPC']    = '{title_legend},name,headline,type;{redirect_legend},uberspaceMPCredirect;{expert_legend:hide},cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][]  = 'uberspaceMPCredirect';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['uberspaceMPCredirect']     = 'jumpTo';

$GLOBALS['TL_DCA']['tl_module']['fields']['uberspaceMPCredirect'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_module']['uberspaceMPCredirect'],
			'exclude'                 => true,
			'filter'                  => false,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		);
