<?php
/**
 * PHAN configuration
 *
 * @package skaut-google-drive-gallery
 */

return [
	'target_php_version'              => '7.3',
	'backward_compatibility_checks'   => false,
	'analyze_signature_compatibility' => true,
	'minimum_severity'                => 0,
	'directory_list'                  => [
		'src',
		'tests',
		'.phan',
		'dist/bundled/vendor',
	],
	'exclude_analysis_directory_list' => [
		'dist/bundled/vendor/',
	],
	'suppress_issue_types'            => [
		'PhanPluginDuplicateConditionalNullCoalescing',
	],
	'plugins'                         => [
		'AlwaysReturnPlugin',
		'DuplicateArrayKeyPlugin',
		'PregRegexCheckerPlugin',
		'UnreachableCodePlugin',
		'NonBoolBranchPlugin',
		'NonBoolInLogicalArithPlugin',
		'InvalidVariableIssetPlugin',
		'NoAssertPlugin',
		'DuplicateExpressionPlugin',
		'DollarDollarPlugin',
	],
];
