<?php
namespace Fab\VidiFrontend\Tca;

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
class FrontendGridService extends GridService { // implements TcaServiceInterface (?)

	/**
	 * __construct
	 *
	 * @throws InvalidKeyInArrayException
	 * @param string $tableName
	 * @return \Fab\VidiFrontend\Tca\FrontendGridService
	 */
	public function __construct($tableName) {

		parent::__construct($tableName);

		if (!empty($GLOBALS['TCA'][$this->tableName]['grid_frontend'])) {
			$this->tca = $GLOBALS['TCA'][$this->tableName]['grid_frontend'];
		}
	}

	/**
	 * Get the translation of a label given a column name.
	 *
	 * @param string $fieldNameAndPath
	 * @return string
	 */
	public function getLabel($fieldNameAndPath) {
		if ($this->hasLabel($fieldNameAndPath)) {
			$field = $this->getField($fieldNameAndPath);
			$label = LocalizationUtility::translate($field['label'], '');
			if (is_null($label)) {
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
	 */
	public function getFields() {
		$allFields = Tca::grid($this->tableName)->getAllFields();
		$frontendFields = is_array($this->tca['columns']) ? $this->tca['columns'] : array();
		return array_merge($allFields, $frontendFields);
	}

	/**
	 * Tell whether the field exists in the grid or not.
	 *
	 * @param string $fieldName
	 * @return bool
	 */
	public function hasField($fieldName) {
		return isset($this->tca['columns'][$fieldName]);
	}

	/**
	 * Returns an array containing facets fields.
	 *
	 * @return array
	 */
	public function getFacets() {
		$frontendFacets = array();
		if (is_array($this->tca['facets'])) {
			$frontendFacets = $this->tca['facets'];
		}

		$allFacets = Tca::grid($this->tableName)->getFacets();
		return array_merge($allFacets, $frontendFacets);
	}

	/**
	 * Returns an array containing facets fields.
	 *
	 * @return array
	 */
	public function getFacetNames() {
		$facetNames = array();

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

			if (!in_array($facet, $facetNames)) {
				$facetNames[] = $facet;
			}
		}
		return $facetNames;
	}

	/**
	 * Returns a "facet" service instance.
	 *
	 * @param string|FacetInterface $facet
	 * @return \Fab\Vidi\Tca\FacetService
	 */
	public function facet($facet = '') {
		if (!$facet instanceof StandardFacet) {
			$label = $this->getLabel($facet);

			/** @var StandardFacet $facet */
			$facet = GeneralUtility::makeInstance('Fab\Vidi\Facet\StandardFacet', $facet, $label);
		}

		if (empty($this->instances[$facet->getName()])) {

			/** @var \Fab\Vidi\Tca\FacetService $instance */
			$instance = GeneralUtility::makeInstance(
				'Fab\Vidi\Tca\FacetService',
				$facet,
				$this->tableName
			);

			$this->instances[$facet->getName()] = $instance;
		}

		return $this->instances[$facet->getName()];
	}

}
