<?php
namespace Fab\VidiFrontend\Persistence;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Domain\Model\Selection;
use Fab\Vidi\Domain\Repository\SelectionRepository;
use Fab\Vidi\Resolver\FieldPathResolver;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use Fab\Vidi\Persistence\Matcher;
use Fab\Vidi\Tca\Tca;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Factory class related to Matcher object.
 */
class MatcherFactory implements SingletonInterface
{

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * Gets a singleton instance of this class.
     *
     * @return MatcherFactory
     * @throws \InvalidArgumentException
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(MatcherFactory::class);
    }

    /**
     * Returns a matcher object.
     *
     * @param array $settings
     * @param array $matches
     * @param string $dataType
     * @return Matcher
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \InvalidArgumentException
     */
    public function getMatcher(array $settings, array $matches = array(), $dataType)
    {
        $this->settings = $settings;

        /** @var $matcher Matcher */
        $matcher = GeneralUtility::makeInstance(\Fab\Vidi\Persistence\Matcher::class, $matches, $dataType);

        $matcher = $this->applyCriteriaFromDataTables($matcher, $dataType);
        $matcher = $this->applyCriteriaFromSelection($matcher, $dataType);
        $matcher = $this->applyCriteriaFromMatchesArgument($matcher, $matches);
        $matcher = $this->applyCriteriaFromAdditionalConstraints($matcher);

        // Trigger signal for post processing Matcher Object.
        $this->emitPostProcessMatcherObjectSignal($matcher);

        if ($settings['logicalSeparator'] === Matcher::LOGICAL_OR) {
            $matcher->setLogicalSeparatorForEquals(Matcher::LOGICAL_OR);
            $matcher->setLogicalSeparatorForLike(Matcher::LOGICAL_OR);
            $matcher->setLogicalSeparatorForIn(Matcher::LOGICAL_OR);
            #$matcher->setLogicalSeparatorForSearchTerm(Matcher::LOGICAL_OR);
            #$matcher->setDefaultLogicalSeparator(Matcher::LOGICAL_OR);
        }

        return $matcher;
    }

    /**
     * @param Matcher $matcher
     * @param array $matches
     * @return Matcher $matcher
     */
    protected function applyCriteriaFromMatchesArgument(Matcher $matcher, $matches)
    {
        foreach ($matches as $fieldNameAndPath => $value) {
            // CSV values should be considered as "in" operator in Query, otherwise "equals".
            $explodedValues = GeneralUtility::trimExplode(',', $value, true);
            if (count($explodedValues) > 1) {
                $matcher->in($fieldNameAndPath, $explodedValues);
            } else {
                $matcher->equals($fieldNameAndPath, $explodedValues[0]);
            }
        }

        return $matcher;
    }

    /**
     * Apply criteria from categories.
     *
     * @param Matcher $matcher
     * @return Matcher $matcher
     */
    protected function applyCriteriaFromAdditionalConstraints(Matcher $matcher)
    {

        if (!empty($this->settings['additionalEquals'])) {
            $constraints = GeneralUtility::trimExplode("\n", $this->settings['additionalEquals'], TRUE);
            foreach ($constraints as $constraint) {

                // hidden feature, constraint should not starts with # which considered a commented statement
                if (false === strpos($constraint, '#')) {

                    if (preg_match('/(.+) (>=|>|<|<=|=|like) (.+)/is', $constraint, $matches) && count($matches) === 4) {

                        $operator = $matcher->getSupportedOperators()[strtolower(trim($matches[2]))];
                        $operand = trim($matches[1]);
                        $value = trim($matches[3]);

                        $matcher->$operator($operand, $value);
                    } elseif (preg_match('/(.+) (in) (.+)/is', $constraint, $matches) && count($matches) === 4) {

                        $operator = $matcher->getSupportedOperators()[trim($matches[2])];
                        $operand = trim($matches[1]);
                        $value = trim($matches[3]);
                        $matcher->$operator($operand, GeneralUtility::trimExplode(',', $value, true));
                    }
                }
            }
        }
        return $matcher;
    }

    /**
     * Apply criteria specific to jQuery plugin DataTable.
     *
     * @param Matcher $matcher
     * @param string $dataType
     * @return Matcher $matcher
     */
    protected function applyCriteriaFromDataTables(Matcher $matcher, $dataType)
    {
        // Special case for Grid in the BE using jQuery DataTables plugin.
        // Retrieve a possible search term from GP.
        $query = GeneralUtility::_GP('search');
        if (is_array($query)) {
            if (!empty($query['value'])) {
                $query = $query['value'];
            } else {
                $query = '';
            }
        }

        if (strlen($query) > 0) {

            // Parse the json query coming from the Visual Search.
            $query = rawurldecode($query);
            $queryParts = json_decode($query, TRUE);

            if (is_array($queryParts)) {
                $matcher = $this->parseQuery($queryParts, $matcher, $dataType);
            } else {
                $matcher->setSearchTerm($query);
            }
        }
        return $matcher;
    }

    /**
     * Apply criteria from selection.
     *
     * @param Matcher $matcher
     * @param string $dataType
     * @return Matcher $matcher
     * @throws \InvalidArgumentException
     */
    protected function applyCriteriaFromSelection(Matcher $matcher, $dataType)
    {

        $selectionIdentifier = (int)$this->settings['selection'];
        if ($selectionIdentifier > 0) {

            /** @var SelectionRepository $selectionRepository */
            $selectionRepository = $this->getObjectManager()->get(SelectionRepository::class);

            /** @var Selection $selection */
            $selection = $selectionRepository->findByUid($selectionIdentifier);
            $queryParts = json_decode($selection->getQuery(), TRUE);
            $matcher = $this->parseQuery($queryParts, $matcher, $dataType);
        }
        return $matcher;
    }

    /**
     * Apply criteria specific to jQuery plugin DataTable.
     *
     * @param array $queryParts
     * @param Matcher $matcher
     * @param string $dataType
     * @return Matcher $matcher
     * @throws \InvalidArgumentException
     */
    protected function parseQuery(array $queryParts, Matcher $matcher, $dataType)
    {

        foreach ($queryParts as $queryPart) {
            $fieldNameAndPath = key($queryPart);

            $resolvedDataType = $this->getFieldPathResolver()->getDataType($fieldNameAndPath, $dataType);
            $fieldName = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $dataType);

            // Retrieve the value.
            $value = current($queryPart);

            if (FrontendTca::grid($resolvedDataType)->hasFacet($fieldName) && FrontendTca::grid($resolvedDataType)->facet($fieldName)->canModifyMatcher()) {
                $matcher = FrontendTca::grid($resolvedDataType)->facet($fieldName)->modifyMatcher($matcher, $value);
            } elseif (Tca::table($resolvedDataType)->hasField($fieldName)) {
                // Check whether the field exists and set it as "equal" or "like".
                if ($this->isOperatorEquals($fieldNameAndPath, $dataType, $value)) {
                    $matcher->equals($fieldNameAndPath, $value);
                } else {
                    $matcher->like($fieldNameAndPath, $value);
                }
            } elseif ($fieldNameAndPath === 'text') {
                // Special case if field is "text" which is a pseudo field in this case.
                // Set the search term which means Vidi will
                // search in various fields with operator "like". The fields come from key "searchFields" in the TCA.
                $matcher->setSearchTerm($value);
            }
        }

        return $matcher;
    }

    /**
     * Tell whether the operator should be equals instead of like for a search, e.g. if the value is numerical.
     *
     * @param string $fieldName
     * @param string $dataType
     * @param string $value
     * @return bool
     * @throws \Exception
     */
    protected function isOperatorEquals($fieldName, $dataType, $value)
    {
        return (Tca::table($dataType)->field($fieldName)->hasRelation() && MathUtility::canBeInterpretedAsInteger($value))
        || Tca::table($dataType)->field($fieldName)->isNumerical();
    }

    /**
     * Signal that is called for post-processing a matcher object.
     *
     * @param Matcher $matcher
     * @signal
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    protected function emitPostProcessMatcherObjectSignal(Matcher $matcher)
    {
        $this->getSignalSlotDispatcher()->dispatch(MatcherFactory::class, 'postProcessMatcherObject', array($matcher, $matcher->getDataType()));
    }

    /**
     * Get the SignalSlot dispatcher
     *
     * @return Dispatcher
     * @throws \InvalidArgumentException
     */
    protected function getSignalSlotDispatcher()
    {
        return $this->getObjectManager()->get(Dispatcher::class);
    }

    /**
     * @return ObjectManager
     * @throws \InvalidArgumentException
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return FieldPathResolver
     * @throws \InvalidArgumentException
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance(FieldPathResolver::class);
    }

}
