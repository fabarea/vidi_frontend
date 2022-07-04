<?php
namespace Fab\VidiFrontend\Form\Elements;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Tca\Tca;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TemplateMenuElement extends AbstractElement
{
    public function render()
    {
        $configuration = $this->getPluginConfiguration();
        $output = '';
        if (is_array($configuration['settings']['listTemplates'])) {
            $selectedItem = '';
            if (!empty($params['row']['pi_flexform'])) {
                $values = $params['row']['pi_flexform'];
                if (!empty($values['data']['sDEF']['lDEF']['settings.templateList'])) {
                    $selectedItem = $values['data']['sDEF']['lDEF']['settings.templateList']['vDEF'];
                }
            }
            $options = array();
            foreach ($configuration['settings']['listTemplates'] as $template) {
                $options[] = sprintf('<option value="%s" %s>%s</option>',
                    $template['path'],
                    $selectedItem === $template['path'] ? 'selected="selected"' : '',
                    $template['label']
                );
            }
            $output = sprintf('<select name="data[tt_content][%s][pi_flexform][data][sDEF][lDEF][settings.templateList][vDEF]">%s</select>',
                $params['row']['uid'],
                implode("\n", $options)
            );
        }

        $result['html'] = $output;
        return $result;
    }

}
