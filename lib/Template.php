<?phpclass Template  extends Layout{    public static $cachedJson    = false;	public static $cacheUrl     = '';    public static $cachePath     = '';    public static $cacheJsonFile = '';    //Default Handler files    public $model = false;	public $template = false;		    public function _init()    {        global $globalJsFiles, $globalCssFiles, $_app_params, $modelObj, $app;		$this->model = $modelObj;		$this->template = $app;        $area = $_app_params['config']['area'];        $theme = $_app_params['config'][$area]['theme'];        self::$skinBasePath = $_app_params['config']['skin_path'];        self::$skinPath = self::$skinBasePath . "/" . $area . "/" . $theme . "/";        self::$templatePath = _BASEDIR . _APP . "/design/" . $area . "/" . $theme . "/template/";		self::$cacheUrl  = "var/cache/".$area . "/";        self::$cachePath = _BASEDIR  . self::$cacheUrl;        self::$cacheJsonFile = self::$cachePath . "cache.json";		        if (!is_writable(_BASEDIR . self::$skinPath)) {            die("Please Check skin directory path is writeable");        }        foreach ($globalJsFiles[$area] as $globalJsFile) {            self::addJs($globalJsFile);        }        foreach ($globalCssFiles[$area] as $globalCssFile) {            self::addCss($globalCssFile);        }    }    // For normal response    public function render($view_file, $params = array())    {        global $app, $_app_params;        $module = $_app_params['config']['module'];        self::$contentFile = $view_file;        if (file_exists($layoutFileName = self::$templatePath . self::$layout . ".phtml")) {            require_once($layoutFileName);        } else {            require_once(self::$templatePath . self::$exceptionLayoutFile . ".phtml");        }        Helper::updateCacheFile(self::$cacheJsonFile, self::$cachedJson);        if (PRINT_MEMORY_USAGE) {            self::printAppFilesStack();        }    }    // For ajax kind of response    public function renderIntoString($view_file, $params = array())    {        ob_start();        $this->render($view_file, $params);        $_template_content = ob_get_contents();        ob_end_clean();        return $_template_content;    }    public function renderJs()    {        $mergedJsFileName = $html = '';        self::$js_files = array_unique(self::$js_files);        if (self::$configData['js_compress'] == 1) {			Helper::makeDir(self::$cachePath);            $curentJsFiles['files'] = implode(",", self::$js_files);            if (file_exists(self::$cacheJsonFile)) {                if (self::$cachedJson === false) {                    self::$cachedJson = Helper::readCacheFile(self::$cacheJsonFile);                }                if (self::$cachedJson && is_array(self::$cachedJson) && isset(self::$cachedJson['js'])) {                    foreach (self::$cachedJson['js'] as $jsMergedFile) {                        if ($curentJsFiles['files'] == $jsMergedFile['files']) {                            $mergedJsFileName = $jsMergedFile['merged'];                        }                    }                }            }            if ($mergedJsFileName == '' || !file_exists(self::$cachePath . $mergedJsFileName)) {                include_once(_BASEDIR . "lib/thidparty/jsmin.php");                $mergedJsFileName = Helper::generateRandomString() . '.js';                $jsContents = '';                foreach (self::$js_files as $jsFile) {                    if (file_exists(_BASEDIR . self::$skinPath . $jsFile)) {                        $jsContents .= file_get_contents(_BASEDIR . self::$skinPath . $jsFile);                    }                }                $jsContents = JSMin::minify($jsContents);                $handle = fopen(self::$cachePath . $mergedJsFileName, 'w');                fwrite($handle, $jsContents);                fclose($handle);                $curentJsFiles['merged'] = $mergedJsFileName;                self::$cachedJson['js'][] = $curentJsFiles;            }            $html .= sprintf('<script type="text/javascript" src="%s"></script>' . "\n",                             _BASEURL . self::$cacheUrl . $mergedJsFileName            );        } else {            foreach (self::$js_files as $jsFile) {                if (file_exists(_BASEDIR . self::$skinPath . $jsFile)) {                    $html .= sprintf('<script type="text/javascript" src="%s"></script>' . "\n",                                     _BASEURL . self::$skinPath . $jsFile                    );                }            }        }        return $html;    }		    public function renderCss()    {        $mergedCssFileName = $html = '';        self::$css_files = array_unique(self::$css_files);        if (self::$configData['css_compress'] == 1) {            Helper::makeDir(self::$cachePath);            $cssMergedFiles = array();            $curentCssFiles['files'] = implode(",", self::$css_files);            if (file_exists(self::$cacheJsonFile)) {                if (self::$cachedJson === false) {                    self::$cachedJson = Helper::readCacheFile(self::$cacheJsonFile);                }                $cssMergedFiles = self::$cachedJson;                if (self::$cachedJson && is_array(self::$cachedJson) && isset(self::$cachedJson['css'])) {                    foreach (self::$cachedJson['css'] as $cssMergedFile) {                        if ($curentCssFiles['files'] == $cssMergedFile['files']) {                            $mergedCssFileName = $cssMergedFile['merged'];                        }                    }                }            }            if ($mergedCssFileName == '' || !file_exists(self::$cachePath . $mergedCssFileName)) {                 $mergedCssFileName = Helper::generateRandomString() . '.css';                $cssContents = '';                foreach (self::$css_files as $cssFile) {                    if (file_exists(_BASEDIR . self::$skinPath . $cssFile)) {                        $cssContents .= file_get_contents(_BASEDIR . self::$skinPath . $cssFile);                    }                }                $cssContents = Helper::compressCss($cssContents, _BASEURL . self::$skinPath);                $handle = fopen(self::$cachePath . $mergedCssFileName, 'w');                fwrite($handle, $cssContents);                fclose($handle);                $curentCssFiles['merged'] = $mergedCssFileName;                self::$cachedJson['css'][] = $curentCssFiles;            }            $html .= sprintf('<link rel="stylesheet" type="text/css" href="%s"/>' . "\n",                             _BASEURL . self::$cacheUrl . $mergedCssFileName            );        } else {            foreach (self::$css_files as $cssFile) {                if (file_exists(_BASEDIR . self::$skinPath . $cssFile)) {                    $html .= sprintf('<link rel="stylesheet" type="text/css" href="%s"/>' . "\n",                                     _BASEURL . self::$skinPath . $cssFile                    );                }            }        }        return $html;    }    public static function clearCssJsCache($path = 'frontend')    {        $script = "rm -rf " . _BASEDIR . "var/cache/$path/*";        return system($script, $retval);    }}