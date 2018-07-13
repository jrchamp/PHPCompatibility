<?php
/**
 * \PHPCompatibility\Sniffs\PHP\EmptyNonVariableSniff.
 *
 * PHP version 5.5
 *
 * @category PHP
 * @package  PHPCompatibility
 * @author   Juliette Reinders Folmer <phpcompatibility_nospam@adviesenzo.nl>
 */

namespace PHPCompatibility\Sniffs\PHP;

use PHPCompatibility\Sniff;

/**
 * \PHPCompatibility\Sniffs\PHP\EmptyNonVariableSniff.
 *
 * Verify that nothing but variables are passed to empty().
 *
 * PHP version 5.5
 *
 * @category PHP
 * @package  PHPCompatibility
 * @author   Juliette Reinders Folmer <phpcompatibility_nospam@adviesenzo.nl>
 */
class EmptyNonVariableSniff extends Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_EMPTY);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    public function process(\PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->supportsBelow('5.4') === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $open = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr, null, false, null, true);
        if ($open === false || isset($tokens[$open]['parenthesis_closer']) === false) {
            return;
        }

        $close = $tokens[$open]['parenthesis_closer'];

        $nestingLevel = 0;
        if ($close !== ($open + 1) && isset($tokens[$open + 1]['nested_parenthesis'])) {
            $nestingLevel = count($tokens[$open + 1]['nested_parenthesis']);
        }

        if ($this->isVariable($phpcsFile, ($open + 1), $close, $nestingLevel) === true) {
            return;
        }

        $phpcsFile->addError(
            'Only variables can be passed to empty() prior to PHP 5.5.',
            $stackPtr,
            'Found'
        );
    }
}
