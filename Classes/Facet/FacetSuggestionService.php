<?php
namespace Fab\VidiFrontend\Facet;

/*
 * This file is part of the Fab/VidiFrontend project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Fab\Vidi\Domain\Repository\ContentRepository;
use Fab\Vidi\Resolver\FieldPathResolver;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;
use Fab\Vidi\Persistence\Matcher;
use Fab\Vidi\Persistence\MatcherObjectFactory;
use Fab\Vidi\Tca\Tca;

/**
 * Class for configuring a custom Facet item.
 */
class FacetSuggestionService
{

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $dataType;

    /**
     * Constructor
     *
     * @param array $settings
     * @param string $dataType
     */
    public function __construct(array $settings, $dataType = '')
    {
        $this->settings = $settings;
        $this->dataType = $dataType;
    }

    /**
     * Retrieve possible suggestions for a field name
     *
     * @param string $fieldNameAndPath
     * @return array
     */
    public function getSuggestions($fieldNameAndPath)
    {

        $values = [];

        $dataType = $this->getFieldPathResolver()->getDataType($fieldNameAndPath, $this->dataType);
        $fieldName = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $this->dataType);

        if (FrontendTca::grid($this->dataType)->facet($fieldNameAndPath)->hasSuggestions()) {
            $values = FrontendTca::grid($this->dataType)->facet($fieldNameAndPath)->getSuggestions();
        } else if (Tca::table($dataType)->hasField($fieldName)) {

            if (Tca::table($dataType)->field($fieldName)->hasRelation()) {

                // Fetch the adequate repository
                $foreignTable = Tca::table($dataType)->field($fieldName)->getForeignTable();
                $contentRepository = ContentRepositoryFactory::getInstance($foreignTable);
                $table = Tca::table($foreignTable);

                // Initialize the matcher object.
                $matcher = MatcherObjectFactory::getInstance()->getMatcher([], $foreignTable);

                $numberOfValues = $contentRepository->countBy($matcher);
                if ($numberOfValues <= $this->getLimit()) {

                    $contents = $contentRepository->findBy($matcher);

                    foreach ($contents as $content) {
                        $values[] = array($content->getUid() => $content[$table->getLabelField()]);
                    }
                }
            } elseif (!Tca::table($dataType)->field($fieldName)->isTextArea()) { // We don't want suggestion if field is text area.
                // Fetch the adequate repository
                /** @var ContentRepository $contentRepository */
                $contentRepository = ContentRepositoryFactory::getInstance($this->dataType);

                /** @var $matcher Matcher */
                $matcher = GeneralUtility::makeInstance('Fab\Vidi\Persistence\Matcher', [], $dataType);

                // Count the number of objects.
                $numberOfValues = $contentRepository->countDistinctValues($fieldName, $matcher);

                // Only returns suggestion if there are not too many for the browser.
                if ($numberOfValues <= $this->getLimit()) {

                    // Query the repository.
                    $contents = $contentRepository->findDistinctValues($fieldName, $matcher);

                    foreach ($contents as $content) {
                        $value = $content[$fieldName];
                        $label = $content[$fieldName];
                        if (Tca::table($dataType)->field($fieldName)->isSelect()) {
                            $label = Tca::table($dataType)->field($fieldName)->getLabelForItem($value);
                        }

                        $values[] = $label;
                    }
                }
            }
        }
        return $values;
    }

    /**
     * Return from settings the suggestion limit.
     *
     * @return int
     */
    protected function getLimit()
    {
        $suggestionLimit = (int)$this->settings['suggestionLimit'];
        if ($suggestionLimit <= 0) {
            $suggestionLimit = 1000;
        }
        return $suggestionLimit;
    }

    /**
     * @return FieldPathResolver
     * @throws \InvalidArgumentException
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance('Fab\Vidi\Resolver\FieldPathResolver');
    }
}
