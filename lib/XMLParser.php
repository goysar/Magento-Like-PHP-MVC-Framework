<?php/** * SLIM_MVC_Framework * * @category  controllers * @package   SLIM_MVC_Framework * @copyright Copyright (c) 2014 (http://www.elamurugan.com/) * @author    Ela <nelamurugan@gmail.com> *//** * Class SLIM_MVC_Framework * * @category    controllers * @package     SLIM_MVC_Framework */class XMLParser{    public        $configXml      = false;    public        $localXml       = false;    public        $layoutXml      = false;    public        $dbDisabled     = false;    public static $adminRoutePath = '';    public function XMLParser()    {        global $defaultAdminPath;        if ($this->configXml == false) {            $this->configPrepare();        }        self::$adminRoutePath = $defaultAdminPath;    }    public function readXml($file)    {        $msg = array();        libxml_use_internal_errors(true);        $xml = simplexml_load_file($file);        if ($xml === false) {            foreach (libxml_get_errors() as $error) {                $msg[] = $error->message;            }            return $msg;        }        return $xml;    }    public function configPrepare()    {        if ($this->configXml == false) {            $this->configXml = $this->readXml(_APPDIR . "etc/config.xml");        }        return $this->configXml;    }    public function localPrepare()    {        if ($this->localXml == false) {            $this->localXml = $this->readXml(_APPDIR . "etc/local.xml");        }        return $this->localXml;    }    public function layoutPrepare($templatePath)    {        if ($this->layoutXml == false) {            $this->layoutXml = $this->readXml($templatePath);        }        return $this->layoutXml;    }    public function getXMLPathVal($xmlObj, $path, $returnAsObject = false, $attribute = '')    {        $pathArray = explode("/", $path);        $targetObj = false;        foreach ($pathArray as $ref) {            if ($targetObj == false && isset($xmlObj->$ref)) {                $targetObj = $xmlObj->$ref;            } elseif (isset($targetObj->$ref)) {                $targetObj = $targetObj->$ref;            }        }        if ($targetObj !== false) {            if ($returnAsObject) {                return $targetObj;            } elseif ($attribute == '') {                return (string)$targetObj;            } else {                return $this->getXmlAttributeValue($attribute);            }        }        return false;    }    public function getXmlAttributeValue($xmlObj, $attribute = '', $returnType = 'string')    {        if ($xmlObj && ($attributeValue = @$xmlObj->attributes()->$attribute)) {            return (string)$attributeValue;        }        return false;    }    public function getXmlAttributeValues($xmlObj)    {        if ($xmlObj && ($attributes = @$xmlObj->attributes())) {            return $attributes;        }        return false;    }    public function getAttributeValues($xmlObj, $attribute, $returnType = 'string')    {        if ($xmlObj && ($attributeVal = @$xmlObj->$attribute)) {            return (string)$attributeVal;        }        return false;    }    public function getConfig($path, $returnAsObject = false, $attribute = '')    {        if ($this->configXml == false) {            $this->configPrepare();        }        //        if($path == 'db/tables/config') debug($this->configXml);        return $this->getXMLPathVal($this->configXml, $path, $returnAsObject, $attribute);    }    public function getLocalXml($path, $returnAsObject = false, $attribute = '')    {        if ($this->localXml == false) {            $this->localPrepare();        }        return $this->getXMLPathVal($this->localXml, $path, $returnAsObject, $attribute);    }    public function getLayoutXml($path, $returnAsObject = false, $attribute = '')    {        return $this->getXMLPathVal($this->layoutXml, $path, $returnAsObject, $attribute);    }    public function getSystemTimeZone()    {        return $this->getConfig('default/time_zone');    }}