<?php

class ProxiBlue_CloudinaryFetch_Model_Design_Package extends Aoe_JsCssTstamp_Model_Package //Mage_Core_Model_Design_Package
{
    /**
     * Merge specified javascript files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedJsUrl($files)
    {
        $url = parent::getMergedJsUrl($files);
        if (!Mage::app()->getStore()->isAdmin() && Mage::getStoreConfig('web/cloudinary_fetch/enabled')) {
            $parsedUrl = parse_url($url);
            $parsedUrl['host'] = preg_replace('/^https?:/i','', Mage::getUrl('', array('_secure' => Mage::app()->getRequest()->isSecure())));
            $pathEnd = strrpos($parsedUrl['path'], Mage::getStoreConfig('web/cloudinary_fetch/url_options'));
            $realPath = str_replace(Mage::getStoreConfig('web/cloudinary_fetch/url_options'), '', substr($parsedUrl['path'],$pathEnd));
            $parsedUrl['path'] = $realPath;
            if(isset($parsedUrl['scheme'])) {
                $parsedUrl['scheme'] = (Mage::app()->getRequest()->isSecure()) ? 'https:' : 'http:';
            }
            $url = implode('', $parsedUrl);
        }
        return $url;

    }

    /**
     * Merge specified css files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedCssUrl($files)
    {
        $url = parent::getMergedCssUrl($files);
        if (!Mage::app()->getStore()->isAdmin() && Mage::getStoreConfig('web/cloudinary_fetch/enabled')) {
            $parsedUrl = parse_url($url);
            $parsedUrl['host'] = preg_replace('/^https?:/i','', Mage::getUrl('', array('_secure' => Mage::app()->getRequest()->isSecure())));
            $pathEnd = strrpos($parsedUrl['path'], Mage::getStoreConfig('web/cloudinary_fetch/url_options'));
            $realPath = str_replace(Mage::getStoreConfig('web/cloudinary_fetch/url_options'), '', substr($parsedUrl['path'],$pathEnd));
            $parsedUrl['path'] = $realPath;
            if(isset($parsedUrl['scheme'])) {
                $parsedUrl['scheme'] = (Mage::app()->getRequest()->isSecure()) ? 'https:' : 'http:';
            }
            $url = implode('', $parsedUrl);
        }
        return $url;
    }

    public function getSkinUrl($file = null, array $params = array())
    {
        $result = parent::getSkinUrl($file, $params);
        if (!Mage::app()->getStore()->isAdmin() && Mage::getStoreConfig('web/cloudinary_fetch/enabled_skin')) {
            $matches = array();
            if (preg_match('/(.*)\.(gif|png|jpg)$/i', $result, $matches)) {
                $parsedUrl = parse_url($result);
                $parsedUrl['host'] = Mage::getStoreConfig('web/cloudinary_fetch/base_url');
                $parsedUrl['path'] = Mage::getStoreConfig('web/cloudinary_fetch/url_options') . $parsedUrl['path'];
                $result = implode('', $parsedUrl);
            }
        }
        return $result;
    }
    
}
