<?php
class Goldman_BrandDirectory_Model_Brand
    extends Mage_Core_Model_Abstract
{
    const VISIBILITY_HIDDEN = '0';
    const VISIBILITY_DIRECTORY = '1';

    protected function _construct()
    {
        /**
         * This tells Magento where the related resource model can be found.
         *
         * For a resource model, Magento will use the standard model alias -
         * in this case 'smashingmagazine_branddirectory' - and look in
         * config.xml for a child node <resourceModel/>. This will be the
         * location that Magento will look for a model when
         * Mage::getResourceModel() is called - in our case,
         * SmashingMagazine_BrandDirectory_Model_Resource.
         */
        $this->_init('goldman_branddirectory/brand');
    }

    /**
     * This method is used in the grid and form for populating the dropdown.
     */
    public function getAvailableVisibilies()
    {
        return array(
            self::VISIBILITY_HIDDEN
                => Mage::helper('goldman_branddirectory')
                       ->__('Hidden'),
            self::VISIBILITY_DIRECTORY
                => Mage::helper('goldman_branddirectory')
                       ->__('Visible in Directory'),
        );
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        /**
         * Perform some actions just before a brand is saved.
         */
        $this->_updateTimestamps();
        $this->_prepareUrlKey();

        return $this;
    }

    protected function _updateTimestamps()
    {
        $timestamp = now();

        /**
         * Set the last updated timestamp.
         */
        $this->setUpdatedAt($timestamp);

        /**
         * If we have a brand new object, set the created timestamp.
         */
        if ($this->isObjectNew()) {
            $this->setCreatedAt($timestamp);
        }

        return $this;
    }

    protected function _prepareUrlKey()
    {
        /**
         * In this method, you might consider ensuring
         * that the URL Key entered is unique and
         * contains only alphanumeric characters.
         */

        return $this;
    }
}
