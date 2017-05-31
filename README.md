Vidi for TYPO3 CMS
==================

Generic List Component where Content can be filtered in an advanced way... Veni, vidi, vici! This extension is based on [Vidi](https://github.com/fabarea/vidi) which provides more or
less the same feature set but in the Backend.

![](https://raw.github.com/fabarea/vidi_frontend/master/Documentation/Frontend-01.png)

Once installed, it can be configured what content type to render associated with a customizable template in the plugin record in the Backend.
On the Frontend side, we use the excellent [DataTables](http://www.datatables.net/) which is a powerful jQuery plugin smart and fast to sort and filter data.
The Filter bar is provided by the [Visual Search](http://documentcloud.github.io/visualsearch/) jQuery plugin which offers nice facet capabilities and intuitive search.

Live example http://www.washinhcf.org/resources/publications/

Project info and releases
-------------------------

Stable version:
http://typo3.org/extensions/repository/view/vidi_frontend

Development version:
https://github.com/fabarea/vidi_frontend

```sh
git clone https://github.com/fabarea/vidi_frontend.git
```

News about latest development are also announced on http://twitter.com/fudriot

Installation and requirement
============================

The extension **requires TYPO3 7.6 or greater** . Install the extension as normal in the Extension Manager from the [TER](http://typo3.org/extensions/repository/view/vidi_frontend) or download the Git version:

```sh
# local installation
cd typo3conf/ext

# download the source
git clone https://github.com/fabarea/vidi_frontend.git

# -> next step, is to open the Extension Manager in the BE.
```

You are almost there! Create a Content Element of type "Vidi Frontend" in `General Plugin` > `Generic List Component` and configure at your convenience.

![](https://raw.github.com/fabarea/vidi_frontend/master/Documentation/Backend-01.png)

Configuration
=============

The plugin can be configured in various places such as TypoScript, PHP or in the plugin record itself.

**Important** by default, the CSS + JS files are loaded for Bootstrap. For a more Vanilla flavor, edit the `path` in the `settings` key in TypoScript and
load the right assets for you. See below the comments::

    #############################
    # plugin.tx_vidifrontend
    #############################
    plugin.tx_vidifrontend {

        settings {

            asset {

                vidiCss {
                    # For none Bootstrap replace "vidi_frontend.bootstrap.min.css" by "vidi_frontend.min.css"
                    path = EXT:vidi_frontend/Resources/Public/Build/StyleSheets/vidi_frontend.bootstrap.min.css
                    type = css
                }

                vidiJs {
                    # For none Bootstrap replace "vidi_frontend.bootstrap.min.js" by "vidi_frontend.min.js"
                    path = EXT:vidi_frontend/Resources/Public/Build/JavaScript/vidi_frontend.bootstrap.min.js
                    type = js
                }
            }
        }
    }

Custom Grid Renderer
--------------------

Assumming we want a complete customized output for a column, we can can achieve this by implementing a Grid Render.
Here is an exemple for table `fe_users`.
We first have to register the new column in the TCA in some `Configuration/TCA/Override/fe_users.php`.

```php
$tca = [
    'grid_frontend' => [
        'columns' => [

            # The key is totally free here. However we prefix with "__" to distinguish between a "regular" column associated to a field.
            '__my_custom_column' => [
                'renderers' => array(
                    'Vendor\MyExt\Grid\MyColumnRenderer',
                ),
                'sorting' => FALSE,
                'sortable' => FALSE,
                'label' => '',
            ],
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_users'], $tca);
```

The corresponding class to be placed in `EXT:MyExt/Classes/Grid/MyColumnRenderer`:

```php
namespace Vendor\MyExt\Grid;

/**
 * Class to render a custom output.
 */
class MyColumnRenderer extends Fab\Vidi\Grid\ColumnRendererAbstract {

    /**
     * Render a publication.
     *
     * @return string
     */
    public function render() {
        return $output;
}
```

Adjust column configuration
---------------------------

Configuration of the columns is taken from the TCA. Sometimes we need to adjust its configuration for the Frontend and we can simply enriches it.
Best is to learn by example and get inspired by ``EXT:vidi_frontend/Configuration/TCA/fe_users.php``:

```php
$tca = array(
    'grid_frontend' => array(
        'columns' => array(

            # Custom fields for the FE goes here
            'title' => [],
        ),
    ),
);
```

Custom Facets
-------------

Facets are visible in the Visual Search and enable the search by criteria. Facets are generally mapped to a field but it is not mandatory ; it can be arbitrary values. To provide a custom Facet, the interface `\Fab\Vidi\Facet\FacetInterface` must be implemented. Best is to take inspiration of the `\Fab\Vidi\Facet\StandardFacet` and provide your own implementation.

```php
$tca = [
    'grid_frontend' => [
        'facets' => [
            new \Vendor\MyExt\Facets\MyCustomFacet(),
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['fe_users'], $tca);
```

The associate class:

```php
<?php
namespace Vendor\MyExt\Facets;


use Fab\Vidi\Facet\FacetInterface;
use Fab\Vidi\Facet\StandardFacet;
use Fab\Vidi\Persistence\Matcher;

/**
 * Class for configuring a custom Facet item.
 */
class CategoryPublicationFacet implements FacetInterface
{

    /**
     * @var string
     */
    protected $name = '__categories_publications';

    /**
     * @var string
     */
    protected $label = 'Categories';

    /**
     * @var array
     */
    protected $suggestions = [];

    /**
     * @var string
     */
    protected $fieldNameAndPath = 'metadata.categories';

    /**
     * @var string
     */
    protected $dataType;

    /**
     * @var string
     */
    protected $canModifyMatcher = true;


    /**
     * Constructor of a Generic Facet in Vidi.
     *
     * @param string $name
     * @param string $label
     * @param array $suggestions
     * @param string $fieldNameAndPath
     */
    public function __construct($name = '', $label = '', array $suggestions = [], $fieldNameAndPath = '')
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getSuggestions()
    {

        return [1 => 'foo', 2 => 'bar', ];
    }

    /**
     * @return bool
     */
    public function hasSuggestions()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getFieldNameAndPath()
    {
        return $this->fieldNameAndPath;
    }

    /**
     * @param string $dataType
     * @return $this
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
        return $this;
    }

    /**
     * @return bool
     */
    public function canModifyMatcher()
    {
        return $this->canModifyMatcher;
    }

    /**
     * @param Matcher $matcher
     * @param $value
     * @return Matcher
     */
    public function modifyMatcher(Matcher $matcher, $value)
    {
        if (MathUtility::canBeInterpretedAsInteger($value)) {
            $matcher->equals('metadata.categories', $value);
        } else {
            $matcher->like('metadata.categories', $value);
        }
        return $matcher;
    }

    /**
     * Magic method implementation for retrieving state.
     *
     * @param array $states
     * @return StandardFacet
     */
    static public function __set_state($states)
    {
        return new CategoryPublicationFacet($states['name'], $states['label'], $states['suggestions'], $states['fieldNameAndPath']);
    }
}
```

Register a new template
-----------------------

The detail view of the content can be personalized per plugin record. To register more templates, simply define them in your TypoScript configuration.
This TypoScript will typically be put under within ``EXT:foo/Configuration/TypoScript/setup.txt``:

    plugin.tx_vidifrontend {
        settings {
            templates {

                # Key "1", "2" is already taken by this extension.
                # Use key "10", "11" and following for your own templates to be safe.
                10 {
                    title = Foo detail view
                    path = EXT:foo/Resources/Private/Templates/VidiFrontend/ShowFoo.html
                    dataType = fe_users
                }
            }
        }
    }

Add custom Constraints
======================

If required to add additional custom constraints at a "low" level, one can take advantage of a Signal Slot in the Content Repository of Vidi. To do so, first register the slot in one of your `ext_localconf.php` file:

```php
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');

$signalSlotDispatcher->connect(
    'Fab\Vidi\Domain\Repository\ContentRepository',
    'postProcessConstraintsObject',
    'Vendor\Extension\Aspects\ProductsAspect',
    'processConstraints',
    true
);
```

Next step is to write and customise the PHP class as given as example below. You can freely manipulate the $constraints object and personalize at your need:

```php
<?php
namespace Vendor\Extension\Aspects;

use Fab\Vidi\Persistence\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;

/**
 * Class which handle signal slot for Vidi Content controller
 */
class ProductsAspect {

    /**
     * Post-process the constraints object to respect the file mounts.
     *
     * @param Query $query
     * @param ConstraintInterface|NULL $constraints
     * @return array
     */
    public function processConstraints(Query $query, $constraints) {
        if ($this->isFrontendMode() && $query->getType() === 'tt_products') {

            $additionalConstraints = $query->logicalAnd(
                $query->logicalNot($query->equals('title', '')),
                $query->logicalNot($query->equals('image', ''))
            );

            if (is_null($constraints)) {
                $constraints = $additionalConstraints;
            } else {

                $constraints = $query->logicalAnd(
                    $constraints,
                    $additionalConstraints
                );
            }
        }
        return array($query, $constraints);
    }

    /**
     * Returns whether the current mode is Frontend
     *
     * @return bool
     */
    protected function isFrontendMode()
    {
        return TYPO3_MODE == 'FE';
    }
}
```

Transmit dynamic parameter
==========================

We can transmit additional GET / POST parameter to dynamically filter the result set in the Grid.
A typical use case is to add a drop down menu to do some additional filter. In this case,
the parameter name must look like where "foo" is a field name.
The value can be a simple value (equals) or a CSV list which will be interpreted as an array (in).

    tx_vididfrontend_pi1[matches][foo]=bar
    tx_vididfrontend_pi1[matches][foo]=bar,baz

Add custom Actions
==================

By default, Vidi Frontend includes some default mass actions such as XML, CSV, XLS export. It is of course possible to add your own actions. Two steps are required for that. The first is to declare in the TCA:

```php
$tca = array(
    'grid_frontend' => [
        'columns' => [
            ...
        ],
        'actions' => [
            new \Vendor\MyExt\MassAction\MyAction(),
        ]
    ],
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['tx_domain_model_foo'], $tca);
```

Then, you need to declare your own class and implement the ```MassActionInterface``` where we have two main methods:

* ```render()``` where the HTML for the menu item is assembled.
* ```execute()``` where we get the items from the request and we can process them according to our needs. The ```execute()``` method must return a ```ResultActionInterface``` which includes the response plus possibles headers to be sent to the client (browser).

```php
<?php
namespace Vendor\MyExt\MassAction;


use Fab\VidiFrontend\Service\ContentService;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;


/**
 * Class MyAction
 */
class MyAction extends AbstractMassAction
{

    /**
     * @var string
     */
    protected $name = 'my_action';

    /**
     * @return string
     */
    public function render()
    {
        $result = sprintf('<li><a href="%s" class="export-csv" data-format="csv"><i class="fa fa-file-text-o"></i> %s</a></li>',
            $this->getMassActionUrl(),
            LocalizationUtility::translate('my_action', 'foo')
        );
        return $result;
    }

    /**
     * Return the name of this action..
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Execute the action.
     *
     * @param ContentService $contentService
     * @return ResultActionInterface
     */
    public function execute(ContentService $contentService)
    {
        $result = new JsonResultAction();
        $objects = $contentService->getObjects();

        // Make sure we have something to process...
        if ((bool)$objects) {

            // do something
            ...

            $result->setOuptut('foo')
        }

        return $result;
    }
}
```

On the top of that you may consider loading your own JS to catch the action and trigger on the client side whatever action is necessary for your such as an Ajax request.


RealURL configuration
=====================

RealURL configuration could look as follows to display nice URL to a detail view.

```php
'postVarSets' => [
    '_DEFAULT' => [
        'content' => [
            ['GETvar' => 'tx_vidifrontend_pi1[contentElement]'],
            ['GETvar' => 'tx_vidifrontend_pi1[action]'],
            ['GETvar' => 'tx_vidifrontend_pi1[content]'],
        ],
    ]
],
```

Building assets in development
==============================

The extension provides JS / CSS bundles which included all the necessary code. If you need to make a new build for those JS / CSS files,
consider that [Bower](http://bower.io/) and [Grunt](http://gruntjs.com/) must be installed on your system as prerequisite.

Install the required Web Components:

```sh
cd typo3conf/ext/vidi_frontend

# This will populate the directory Resources/Private/BowerComponents.
bower install

# Install the necessary NodeJS package.
npm install
```

Then, you can run the Grunt of the extension to generate a build:

```sh
cd typo3conf/ext/vidi_frontend
grunt build
```

While developing, you can use the ``watch`` which will generate the build as you edit files:

```sh
grunt watch
```

Patch VisualSearch
------------------

To improve the User experience, [Visual Search](http://documentcloud.github.io/visualsearch/) plugin has been patched avoiding the drop down menu to appear inopportunely.
It means when making a fresh build, the patch must be (for now) manually added:

```sh
cd Resources/Private/BowerComponents/visualsearch/
grep -lr "app.searchBox.searchEvent(e)" .

-> There should be 2 occurrences. Comment lines below related to "_.defer".

# Remove assumed already jQuery from dependency
curl http://documentcloud.github.io/visualsearch/vendor/jquery.ui.core.js > Resources/Private/BowerComponents/visualsearch/build-min/dependencies.js
curl http://documentcloud.github.io/visualsearch/vendor/jquery.ui.position.js >> Resources/Private/BowerComponents/visualsearch/build-min/dependencies.js
curl http://documentcloud.github.io/visualsearch/vendor/jquery.ui.widget.js >> Resources/Private/BowerComponents/visualsearch/build-min/dependencies.js
curl http://documentcloud.github.io/visualsearch/vendor/jquery.ui.menu.js >> Resources/Private/BowerComponents/visualsearch/build-min/dependencies.js
curl http://documentcloud.github.io/visualsearch/vendor/jquery.ui.autocomplete.js >> Resources/Private/BowerComponents/visualsearch/build-min/dependencies.js
curl http://documentcloud.github.io/visualsearch/vendor/underscore-1.5.2.js >> Resources/Private/BowerComponents/visualsearch/build-min/dependencies.js
curl http://documentcloud.github.io/visualsearch/vendor/backbone-1.1.0.js >> Resources/Private/BowerComponents/visualsearch/build-min/dependencies.js
```
