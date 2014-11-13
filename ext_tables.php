<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'Fab.vidi_frontend',
		'Pi1',
		'Generic List Component'
	);

	$TCA['tt_content']['types']['list']['subtypes_addlist']['vidifrontend_pi1'] = 'pi_flexform';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
		'vidifrontend_pi1',
		sprintf('FILE:EXT:vidi_frontend/Configuration/FlexForm/VidiFrontend.xml')
	);

}

/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

/** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
$signalSlotDispatcher = $objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');

// Connect "postFileIndex" signal slot with the metadata service.
$signalSlotDispatcher->connect(
	'TYPO3\CMS\Vidi\Tca\Tca',
	'preProcessTca',
	'Fab\VidiFrontend\Configuration\TcaTweak',
	'tweakFileReferences',
	TRUE
);
