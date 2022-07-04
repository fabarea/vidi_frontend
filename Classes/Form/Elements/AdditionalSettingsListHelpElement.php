<?php
namespace Fab\VidiFrontend\Form\Elements;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */


class AdditionalSettingsListHelpElement extends AbstractElement
{
    public function render()
    {
        $configuration = $this->getPluginConfiguration();

        $output = '';

        $typoscriptConfigurationKey = $params['fieldConf']['config']['typoscriptConfigurationKey'];
        if (is_array($configuration['settings'][$typoscriptConfigurationKey])) {
            $selectedItem = '';
            if (!empty($params['row']['pi_flexform'])) {
                $values = $params['row']['pi_flexform'];
                if (!empty($values['data']['sDEF']['lDEF']['settings.templateList'])) {
                    $selectedItem = $values['data']['sDEF']['lDEF']['settings.templateList']['vDEF'];
                }
            }

            $lines = [];
            foreach ($configuration['settings'][$typoscriptConfigurationKey] as $template) {
                if ($selectedItem === $template['path']
                    && isset($template['additionalSettingsHelp'])
                    && trim($template['additionalSettingsHelp']) !== '') {

                    $rawLines = explode("\n", trim($template['additionalSettingsHelp']));
                    foreach ($rawLines as $rawLine) {
                        $formattedLine = trim(htmlspecialchars($rawLine));

                        if ($formattedLine === '') {
                            $formattedLine = '&nbsp;';
                        }

                        if (preg_match('/^#/', $formattedLine)) {
                            $formattedLine = sprintf('<div style="color: grey">%s</div>', $formattedLine);
                        } else {
                            $formattedLine = sprintf('<div>%s</div>', $formattedLine);
                        }
                        $lines[] = $formattedLine;
                    }
                }
            }

            if (!empty($lines)) {

                $output = sprintf(
                    '
<div>
    <label class="t3js-formengine-label">
        %s
    </label>
</div>
%s',
                    $this->getLanguageService()->sL('LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:settings.availableAdditionalSettings'),
                    implode("\n", $lines)
                );
            }
        }
        $result['html'] = $output;
        return $result;
    }

}
