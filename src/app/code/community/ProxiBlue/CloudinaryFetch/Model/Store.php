<?php

class ProxiBlue_CloudinaryFetch_Model_Store extends Mage_Core_Model_Store
{
    protected function _updateMediaPathUseRewrites($secure = null, $type = self::URL_TYPE_MEDIA)
    {
        $url = parent::_updateMediaPathUseRewrites($secure,$type);
        if(!Mage::app()->getStore()->isAdmin() && Mage::getStoreConfig('web/cloudinary_fetch/enabled')) {
            $parsedUrl = parse_url($url);
            if(isset($parsedUrl['path'])) {
                $url = Mage::getStoreConfig('web/cloudinary_fetch/base_url') . Mage::getStoreConfig('web/cloudinary_fetch/url_options') . $parsedUrl['path'];
            }
        }
        return $url;
    }
}
