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
 * View helper which returns suggestion for the Visual Search bar.
 */
class SuggestionsViewHelper extends AbstractViewHelper
{

    /**
     * Returns the json serialization of the search fields.
     *
     * @return boolean
     */
    public function render()
    {
        $settings = $this->templateVariableContainer->get('settings');

        $suggestions = [];
        $facets = GeneralUtility::trimExplode(',', $settings['facets'], true);
        foreach ($facets as $facetName) {
            $suggestions[$facetName] = $this->getFacetSuggestionService()->getSuggestions($facetName);
        }

        return json_encode($suggestions);
    }

    /**
     * @return \Fab\VidiFrontend\Facet\FacetSuggestionService
     */
    protected function getFacetSuggestionService()
    {
        $settings = [];
        $dataType = $this->templateVariableContainer->get('dataType');
        return GeneralUtility::makeInstance('Fab\VidiFrontend\Facet\FacetSuggestionService', $settings, $dataType);
    }

}
