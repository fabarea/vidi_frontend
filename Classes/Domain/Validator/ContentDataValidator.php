<?php
namespace Fab\VidiFrontend\Domain\Validator;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validate $contentData to be called from the correct page.
 */
class ContentDataValidator extends AbstractValidator
{

    /**
     * Check that $contentData is called from the correct page.
     *
     * @param array $contentData
     * @return void
     */
    public function isValid($contentData)
    {
        if ((int)$contentData['pid'] !== (int)$this->getFrontendObject()->id) {
            $this->addError('Content element is called from an invalide page.', 1457590642);
        }
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendObject()
    {
        return $GLOBALS['TSFE'];
    }

}
