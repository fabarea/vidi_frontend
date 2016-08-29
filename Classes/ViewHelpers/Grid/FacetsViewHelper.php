<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns the json serialization of the search fields.
 */
class FacetsViewHelper extends AbstractViewHelper
{

    /**
     * Returns the json serialization of the search fields.
     *
     * @return boolean
     */
    public function render()
    {
        $dataType = $this->templateVariableContainer->get('dataType');
        $settings = $this->templateVariableContainer->get('settings');

        $facetIdentifiers = GeneralUtility::trimExplode(',', $settings['facets'], TRUE);
        $facets = array();
        foreach ($facetIdentifiers as $facetName) {
            $name = FrontendTca::grid($dataType)->facet($facetName)->getName();
            $facets[$name] = FrontendTca::grid($dataType)->facet($facetName)->getLabel();
        }

        return json_encode($facets);
    }

}
