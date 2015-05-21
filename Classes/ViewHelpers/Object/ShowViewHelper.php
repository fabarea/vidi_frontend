<?php
namespace Fab\VidiFrontend\ViewHelpers\Object;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Domain\Model\Content;

/**
 * View helper for rendering multiple rows.
 */
class ShowViewHelper extends AbstractViewHelper {


	/**
	 * Display the object
	 *
	 * @return string
	 */
	public function render() {

		/** @var Content $object */
		$object = $this->templateVariableContainer->get('object');

		$output = array();
		foreach ($object->toArray() as $fieldName => $value) {
			$output[] = sprintf(
				'<tr><td>%s</td><td>%s</td></tr>',
				$fieldName,
				$value
			);
		}

		return '<table class="table table-striped table-hover">' . implode("\n", $output) . '</table>';
	}

}
