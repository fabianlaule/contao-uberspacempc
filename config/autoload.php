<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package UberspaceMPC
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'UberspaceMPC',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Models
	'UberspaceMPC\UberspacempcModel'  => 'system/modules/uberspacempc/models/UberspacempcModel.php',

	// Modules
	'UberspaceMPC\ModuleUberspaceMPC' => 'system/modules/uberspacempc/modules/ModuleUberspaceMPC.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_uberspacempc' => 'system/modules/uberspacempc/templates',
));
