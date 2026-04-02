<?php


class modQuickViewContentProcessor extends modProcessor
{
    /** @var QuickView $QuickView */
    public $QuickView;
    public $action;
    public $script;
    public $element;

    /** @var modParser|pdoParser|QuickViewParser $parser */
    public $parser;
    public $cacheable;
    public $propertySet;

    public $languageTopics = ['quickview'];
    public $permission = '';


    public function initialize()
    {
        $corePath = $this->modx->getOption('quickview_core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/quickview/');
        /** @var QuickView $QuickView */
        $this->QuickView = $this->modx->getService('quickview', 'QuickView', $corePath . 'model/quickview/', ['core_path' => $corePath]);
        $this->QuickView->initialize($this->getProperty('ctx', $this->modx->context->key));

        $this->action = $this->QuickView->explodeAndClean($this->getProperty('action'), '/');
        $this->action = end($this->action);

        $this->element = trim($this->getProperty('element'));
        /* get property set */
        $this->propertySet = '';
        if ($pos = strpos($this->element, '@')) {
            $this->propertySet = substr($this->element, $pos + 1);
            $this->element = substr($this->element, 0, $pos);
        }
        if (strpos($this->element, '!') === 0) {
            $this->element = substr($this->element, 1);
            $this->cacheable = false;
        } else {
            $this->cacheable = true;
        }

        $allowed = $this->QuickView->getOption('allowed_' . $this->action, null);
        $allowed = $this->QuickView->explodeAndClean($allowed);

        if (!empty($allowed) AND !in_array($this->element, $allowed)) {
            return $this->QuickView->lexicon('err_lock', ['name' => $this->element]);
        }

        if (!$this->modx->getParser()) {
            return false;
        }

        $this->parser = &$this->modx->parser;
        $setProcessingUncacheable = function ($uncacheable = true) {
            $this->_processingUncacheable = $uncacheable;

            return $this->_processingUncacheable;
        };
        $set = $setProcessingUncacheable->bindTo($this->parser, get_class($this->parser));
        $set();

        if (isset($this->parser->pdoTools)) {
            $useFenom = $this->modx->getOption('pdotools_fenom_default', null, false);
            $this->parser->pdoTools->config['useFenomParser'] = $useFenom;
        }

        return parent::initialize();
    }


    /** {@inheritDoc} */
    public function process()
    {
        $output = null;

        /* process script */
        if (!$script = $this->getScript()) {
            return false;
            //return $this->QuickView->failure('err_element_nf', $data, array('name' => $this->element));
        }
        $properties = [];
        if (!empty($this->propertySet)) {
            if ($tmp = $script->getPropertySet($this->propertySet)) {
                $properties = $tmp;
            }
        } else {
            $properties = $script->getProperties();
        }

        /* process request */
        $this->modx->setPlaceholders($_REQUEST, 'request.');

        /* process resource */
        $rid = (int)$this->getProperty('id');
        if (!empty($rid) AND $resource = $this->modx->getObject('modResource', $rid)) {
            $this->QuickView->processContext($resource->get('context_key'));
            $this->QuickView->processResource($resource->get('id'));
        }

        /* process ctx */
        $ctx = (string)$this->getProperty('ctx', 'web');
        $this->QuickView->processContext($ctx);

        /* process lexicon */
        $topics = (array)$this->getProperty('topics', []);
        $this->QuickView->processLexicon($topics);

        /* process script */
        $script->setCacheable($this->cacheable);
        $content = $script->getContent();
        $output = $script->process($properties, $content);

        $maxIterations = intval($this->modx->getOption('parser_max_iterations', null, 10));
        $this->parser->processElementTags('', $output, true, false, '[[', ']]', [], $maxIterations);
        $this->parser->processElementTags('', $output, true, true, '[[', ']]', [], $maxIterations);


        /* Insert Startup jscripts & CSS scripts into template - template must have a </head> tag */
        $js = $this->modx->getRegisteredClientStartupScripts();
        if ((strpos($output, '</head>') !== false) AND $js) {
            /* change to just before closing </head> */
            $output = preg_replace("/(<\/head>)/i", $js . "\n\\1", $output, 1);
        }
        /* Insert jscripts & html block into template - template must have a </body> tag */
        $js = $this->modx->getRegisteredClientScripts();
        if ((strpos($output, '</body>') !== false) AND $js) {
            $output = preg_replace("/(<\/body>)/i", $js . "\n\\1", $output, 1);
        }

        $totalTime = (microtime(true) - $this->modx->startTime);
        $queryTime = $this->modx->queryTime;
        $queries = isset ($this->modx->executedQueries) ? $this->modx->executedQueries : 0;
        $phpTime = $totalTime - $queryTime;
        $queryTime = sprintf("%2.4f s", $queryTime);
        $totalTime = sprintf("%2.4f s", $totalTime);
        $phpTime = sprintf("%2.4f s", $phpTime);
        $source = $this->modx->resourceGenerated ? "database" : "cache";
        $memory = number_format(memory_get_usage(true) / 1024, 0, ",", " ") . ' kb';
        $output = str_replace("[^q^]", $queries, $output);
        $output = str_replace("[^qt^]", $queryTime, $output);
        $output = str_replace("[^p^]", $phpTime, $output);
        $output = str_replace("[^t^]", $totalTime, $output);
        $output = str_replace("[^s^]", $source, $output);
        $output = str_replace("[^m^]", $memory, $output);

        return $output;

        return $this->QuickView->success('', [
            'output' => $output,
        ]);
    }

    public function getScript()
    {
        $script = $content = $path = null;

        if (strpos($this->element, '@FILE ') !== false) {
            $this->element = str_replace('@FILE ', '', $this->element);

            $path = $this->modx->getOption('pdotools_elements_path', null, '{core_path}elements/', true);
            $path = $this->QuickView->translatePath($path);

            if (strpos($path, MODX_BASE_PATH) === false AND strpos($path, MODX_CORE_PATH) === false) {
                $path = MODX_BASE_PATH . $path;
            }
            $path = preg_replace('#/+#', '/', $path . ltrim($this->element, './'));
            if (preg_match('#\.(html|tpl)$#i', $path) AND file_exists($path) AND $content = file_get_contents($path)) {
                $this->element = 'file-' . sha1($path);
            }
        }

        switch ($this->action) {
            case 'snippet':
                $classScript = 'modSnippet';
                $fields = ['name' => $this->element];
                break;
            case 'chunk':
                $classScript = 'modChunk';
                $fields = ['name' => $this->element];
                break;
            case 'template':
                $classScript = 'modTemplate';
                $fields = ['templatename' => $this->element];
                break;
            default:
                return $script;
        }

        /** @var modScript $script */
        if ($content) {
            $script = $this->modx->newObject($classScript, $fields);
            $script->setContent($content);
            if ($script instanceof modScript) {
                /** @var modScript $element */
                $script->_scriptName = $script->getScriptName() . $this->element;
            }
            $script->set('static', true);
            $script->set('static_file', $path);
        } else {
            $script = $this->modx->getObject($classScript, $fields);
        }

        return $script;
    }

}

return 'modQuickViewContentProcessor';