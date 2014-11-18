<?php
namespace Fab\VidiFrontend\ViewHelpers\Grid\Column;

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

use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for rendering a column title in the grid.
 */
class TitleViewHelper extends AbstractViewHelper {

	/**
	 * Returns a column title.
	 *
	 * @return string
	 */
	public function render() {
		$dataType = $this->templateVariableContainer->get('dataType');
		$columnName = $this->templateVariableContainer->get('columnName');

		return FrontendTca::grid($dataType)->getLabel($columnName);
	}

}
