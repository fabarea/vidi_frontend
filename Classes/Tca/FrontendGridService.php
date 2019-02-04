<?php
namespace Fab\VidiFrontend\Tca;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\VidiFrontend\MassAction\DividerMenuItem;
use Fab\VidiFrontend\MassAction\DownloadAction;
use Fab\VidiFrontend\MassAction\ExportCsvAction;
use Fab\VidiFrontend\MassAction\ExportXlsAction;
use Fab\VidiFrontend\MassAction\ExportXmlAction;
use Fab\VidiFrontend\MassAction\MassActionInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Fab\Vidi\Exception\InvalidKeyInArrayException;
use Fab\Vidi\Facet\FacetInterface;
use Fab\Vidi\Facet\StandardFacet;
use Fab\Vidi\Tca\GridService;
use Fab\Vidi\Tca\Tca;

/**
 * A class to handle TCA grid configuration
 */
class FrontendGridService extends GridService
{

    /**
     * __construct
     *
     * @throws InvalidKeyInArrayException
     * @param string $tableName
     * @return \Fab\VidiFrontend\Tca\FrontendGridService
     */
    public function __construct($tableName)
    {

        parent::__construct($tableName);

        if (empty($GLOBALS['TCA'][$this->tableName]['grid_frontend'])) {
            $GLOBALS['TCA'][$this->tableName]['grid_frontend'] = [];
        }

        if (empty($GLOBALS['TCA'][$this->tableName]['grid_frontend']['columns'])) {
            $GLOBALS['TCA'][$this->tableName]['grid_frontend']['columns'] = [];
        }

        if (empty($GLOBALS['TCA'][$this->tableName]['grid_frontend']['columns']['__buttons'])) {
            $GLOBALS['TCA'][$this->tableName]['grid_frontend']['columns']['__buttons'] = [
                'renderer' => 'Fab\VidiFrontend\Grid\ShowButtonRenderer',
                'sortable' => false
            ];
        }

        $this->tca = $GLOBALS['TCA'][$this->tableName]['grid_frontend'];
    }

    /**
     * Get the translation of a label given a column name.
     *
     * @param string $fieldNameAndPath
     * @return string
     * @throws \Fab\Vidi\Exception\InvalidKeyInArrayException
     */
    public function getLabel($fieldNameAndPath)
    {
        if ($this->hasLabel($fieldNameAndPath)) {
            $field = $this->getField($fieldNameAndPath);
            $label = $this->getLanguageService()->sL($field['label']);
            if ($label === null) {
                $label = $field['label'];
            }
        } else {
            // Fetch the label from the Grid service provided by "vidi". He may know more about labels.
            $label = Tca::grid($this->tableName)->getLabel($fieldNameAndPath);
        }
        return $label;
    }

    /**
     * Returns an array containing column names.
     *
     * @return array
     * @throws \Fab\Vidi\Exception\NotExistingClassException
     */
    public function getFields()
    {
        $allFields = Tca::grid($this->tableName)->getAllFields();
        $frontendFields = is_array($this->tca['columns']) ? $this->tca['columns'] : [];
        return array_merge($allFields, $frontendFields);
    }

    /**
     * Returns an array containing column actions.
     *
     * @return MassActionInterface[]
     */
    public function getMassActions()
    {

        // Default classes
        $xlsAction = new ExportXlsAction();
        $xmlAction = new ExportXmlAction();
        $csvAction = new ExportCsvAction();
        $downloadAction = new DownloadAction();
        $divider = new DividerMenuItem();

        $massAction = [
            $xlsAction->getName() => $xlsAction,
            $xmlAction->getName() => $xmlAction,
            $csvAction->getName() => $csvAction,
            $downloadAction->getName() => $downloadAction,
            $divider->getName() => $divider,
        ];

        if (is_array($this->tca['actions'])) {
            /** @var MassActionInterface $action */
            foreach ($this->tca['actions'] as $action) {
                if (is_string($action)) {
                    $action = GeneralUtility::makeInstance($action);
                }
                $massAction[$action->getName()] = $action;
            }
        }
        return $massAction;
    }

    /**
     * Tell whether the field exists in the grid or not.
     *
     * @param string $fieldName
     * @return bool
     */
    public function hasField($fieldName)
    {
        return isset($this->tca['columns'][$fieldName]);
    }

    /**
     * Returns an array containing facets fields.
     *
     * @return FacetInterface[]
     * @throws \Fab\Vidi\Exception\NotExistingClassException
     */
    public function getFacets()
    {
        if ($this->facets === null) {

            // Default facets
            $this->facets = Tca::grid($this->tableName)->getFacets();

            // Override with facets for the Frontend
            if (is_array($this->tca['facets'])) {
                foreach ($this->tca['facets'] as $facetNameOrObject) {

                    if ($facetNameOrObject instanceof FacetInterface) {
                        $this->facets[$facetNameOrObject->getName()] = $facetNameOrObject;
                    } else {
                        $this->facets[$facetNameOrObject] = $this->instantiateStandardFacet($facetNameOrObject);
                    }
                }
            }
        }
        return $this->facets;
    }

    /**
     * Returns the "sortable" value of the column.
     *
     * @param string $fieldNameAndPath
     * @return int|string
     * @throws \Fab\Vidi\Exception\NotExistingClassException
     * @throws \Fab\Vidi\Exception\InvalidKeyInArrayException
     */
    public function isSortable($fieldNameAndPath)
    {
        $configuration = self::getField($fieldNameAndPath);
        if (isset($configuration['sortable'])) {
            $isSortable = (bool)$configuration['sortable'];
        } else {
            $isSortable = Tca::grid($this->tableName)->isSortable($fieldNameAndPath);
        }
        return $isSortable;
    }

    /**
     * Returns an array containing facets fields.
     *
     * @return array
     */
    public function getFacetNames()
    {
        $facetNames = [];

        if (is_array($this->tca['facets'])) {
            foreach ($this->tca['facets'] as $facet) {
                if ($facet instanceof StandardFacet) {
                    $facet = $facet->getName();
                }
                $facetNames[] = $facet;
            }
        }

        foreach (Tca::grid($this->tableName)->getFacets() as $facet) {
            if ($facet instanceof StandardFacet) {
                $facet = $facet->getName();
            }

            if (!in_array($facet, $facetNames, true)) {
                $facetNames[] = $facet;
            }
        }
        return $facetNames;
    }

    /**
     * Returns LanguageService
     *
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
