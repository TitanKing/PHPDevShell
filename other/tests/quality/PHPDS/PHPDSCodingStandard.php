<?php
/**
 * PHPDS Coding Standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @author    Greg <greg@phpdevshell.org>
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
		throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * PHPDS Coding Standard.
 *
 * @category  PHP
 * @author    Greg <greg@phpdevshell.org>
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_PHPDS_PHPDSCodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{


		/**
		 * Return a list of external sniffs to include with this standard.
		 *
		 * The PEAR standard uses some generic sniffs.
		 *
		 * @return array
		 */
		public function getIncludedSniffs()
		{
				return array(
						'Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
						'Generic/Sniffs/CodeAnalysis/EmptyStatementSniff.php',
						'Generic/Sniffs/Commenting/TodoSniff.php',
						/*'Generic/Sniffs/Files/LineEndingsSniff.php',
						'Generic/Sniffs/Files/LineLengthSniff.php',*/
						'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
						'Generic/Sniffs/PHP/LowerCaseConstantSniff.php',
						'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
						'Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php',
						'PEAR/Sniffs/Classes/ClassDeclarationSniff.php',
						/*'PEAR/Sniffs/Commenting/ClassCommentSniff.php',
						'PEAR/Sniffs/Commenting/FileCommentSniff.php',
						'PEAR/Sniffs/Commenting/FunctionCommentSniff.php',
						'PEAR/Sniffs/Commenting/InlineCommentSniff.php',*/
						'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php',
						'PEAR/Sniffs/ControlStructures/InlineControlStructureSniff.php',
						'PEAR/Sniffs/ControlStructures/MultiLineConditionSniff.php',
						'PEAR/Sniffs/Files/IncludingFileSniff.php',
						'PEAR/Sniffs/Functions/FunctionCallArgumentSpacingSniff.php',
						'PEAR/Sniffs/Functions/FunctionCallSignatureSniff.php',
						'PEAR/Sniffs/Functions/FunctionDeclarationSniff.php',
						'PEAR/Sniffs/Functions/ValidDefaultValueSniff.php',
						/*'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php',*/
						'PEAR/Sniffs/NamingConventions/ValidFunctionNameSniff.php',
						'PEAR/Sniffs/NamingConventions/ValidVariableNameSniff.php',
						/*'PEAR/Sniffs/WhiteSpace/ObjectOperatorIndentSniff.php',
						'PEAR/Sniffs/WhiteSpace/ScopeClosingBraceSniff.php',
						'PEAR/Sniffs/WhiteSpace/ScopeIndentSniff.php',*/
						'Zend/Sniffs/Files/ClosingTagSniff.php'
					);

		}//end getIncludedSniffs()


}//end class
?>
