<?phpclass Layout extends Config{    public static $js_files     = array();    public static $css_files    = array();    public static $configData   = array();    public static $bodyClasses  = array();    public static $skinBasePath = '';    public static $skinPath     = '';    public static $templatePath = '';    public static $rootLayout = '1column.phtml';    public function addItem($obj)    {        debug($obj);    }    public function setPageTitle($site_title)    {        $this->setData('site_title', $site_title, 'config');    }    public function setPageMetaKeywords($site_meta_keywords)    {        $this->setData('site_meta_keywords', $site_meta_keywords, 'config');    }    public function setPageMetaDescription($site_meta_description)    {        $this->setData('site_meta_description', $$site_meta_description, 'config');    }    public function getSiteTitle()    {        return self::$configData['site_title'];    }    public function getPageTitle()    {        $title = $this->getData('site_title', 'config') . " - " . self::$configData['site_title'];        return $title;    }    public function getPageMetaKeywords()    {        $site_meta_keywords = $this->getData('site_meta_keywords',                                             'config'            ) . " " . self::$configData['site_meta_keywords'];        return $site_meta_keywords;    }    public function getPageMetaDescription()    {        $site_meta_description = $this->getData('site_meta_description',                                                'config'            ) . " " . self::$configData['site_meta_description'];        return $site_meta_description;    }    public function setBodyClass($cssclass)    {        self::$bodyClasses[] = $cssclass;    }    public function getBodyClass()    {        $body_class = $this->getData('body_class', 'config') . " " . implode(" ", self::$bodyClasses);        return $body_class;    }    public static function addJs($js_file)    {        self::$js_files[] = $js_file;    }    public static function addCss($css_file)    {        self::$css_files[] = $css_file;    }}