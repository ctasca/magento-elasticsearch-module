<?php
/**
 * READ LICENSE AT  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Pocketphp Elasticsearch module to newer versions in the future.
 * If you wish to customize the Pocketphp Elasticsearch module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @copyright  Copyright (C) 2014 Pocketphp ltd (http://pocketphp.co.uk)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Setup extends Mage_Catalog_Model_Resource_Setup
{
    public function createElasticsearchAttributeGroup()
    {
        $sets = $this->_getAttributeSetCollection();

        foreach ($sets as $set) {
            $group = Mage::getModel('eav/entity_attribute_group');
            $group->setAttributeGroupName('Elasticsearch')
                ->setAttributeSetId($set->getId())
                ->setSortOrder(100);
            $this->_trySavingGroup($group);
        }

        return $this->_returnCreatedResponse();
    }


    public function createNameSuggestInputAttribute()
    {
        try {
            $this->addAttribute(
                Mage_Catalog_Model_Product::ENTITY,
                'name_suggest_input',
                array(
                    'group'            => 'Elasticsearch',
                    'type'             => 'varchar',
                    'frontend'         => '',
                    'backend'          => '',
                    'label'            => 'Name Suggest Input',
                    'input'            => 'text',
                    'class'            => '',
                    'source'           => '',
                    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible'          => 1,
                    'required'         => 0,
                    'user_defined'     => 1,
                    'default'          => '',
                    'searchable'       => 0,
                    'filterable'       => 0,
                    'comparable'       => 0,
                    'visible_on_front' => 0,
                    'unique'           => 0
                )
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_returnCreatedResponse();

    }

    public function createNameSuggestWeightAttribute()
    {
        try {
            $this->addAttribute(
                Mage_Catalog_Model_Product::ENTITY,
                'name_suggest_weight',
                array(
                    'group'            => 'Elasticsearch',
                    'type'             => 'int',
                    'frontend'         => '',
                    'backend'          => '',
                    'label'            => 'Name Suggest Weight',
                    'input'            => 'text',
                    'class'            => '',
                    'source'           => '',
                    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible'          => 1,
                    'required'         => 0,
                    'user_defined'     => 1,
                    'default'          => '',
                    'searchable'       => 0,
                    'filterable'       => 0,
                    'comparable'       => 0,
                    'visible_on_front' => 0,
                    'unique'           => 0
                )
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_returnCreatedResponse();
    }

    /**
     *
     * Depreacated in favor of createRemoveCategoryNamesSuggestestionsAttribute
     * Wanted to negate the attribute
     *
     * @deprecated since version 0.4.0 alpha
     *
     * @return string
     */
    public function createAddCategoriesToNameSuggestAttribute()
    {
        return $this;
        /*
        try {
            $this->addAttribute(
                Mage_Catalog_Model_Product::ENTITY,
                'add_categories_to_name_suggest',
                array(
                    'group'            => 'Elasticsearch',
                    'type'             => 'int',
                    'frontend'         => '',
                    'backend'          => '',
                    'label'            => 'Add Categories to Suggestions?',
                    'input'            => 'boolean',
                    'class'            => '',
                    'source'           => 'eav/entity_attribute_source_table',
                    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible'          => 1,
                    'required'         => 0,
                    'user_defined'     => 1,
                    'default'          => '',
                    'searchable'       => 0,
                    'filterable'       => 0,
                    'comparable'       => 0,
                    'visible_on_front' => 0,
                    'unique'           => 0,
                    'note'             => "If set to 'Yes' product's category names are added to name_suggest."
                )
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_returnCreatedResponse();
        */
    }


    public function createRemoveCategoryNamesSuggestionsAttribute()
    {
        try {
            $this->addAttribute(
                Mage_Catalog_Model_Product::ENTITY,
                'remove_catnames_suggestions',
                array(
                    'group'            => 'Elasticsearch',
                    'type'             => 'int',
                    'frontend'         => '',
                    'backend'          => '',
                    'label'            => 'Remove Category Names Suggestions?',
                    'input'            => 'boolean',
                    'class'            => '',
                    'source'           => 'eav/entity_attribute_source_table',
                    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible'          => 1,
                    'required'         => 0,
                    'user_defined'     => 1,
                    'default'          => '',
                    'searchable'       => 0,
                    'filterable'       => 0,
                    'comparable'       => 0,
                    'visible_on_front' => 0,
                    'unique'           => 0,
                    'note'             => "If set to 'Yes' product's category names are removed from name_suggest value during indexing."
                )
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_returnCreatedResponse();
    }

    public function createRemoveProductNameSuggestionsAttribute()
    {
        try {
            $this->addAttribute(
                Mage_Catalog_Model_Product::ENTITY,
                'remove_name_suggestions',
                array(
                    'group'            => 'Elasticsearch',
                    'type'             => 'int',
                    'frontend'         => '',
                    'backend'          => '',
                    'label'            => 'Remove Product Name Suggestions?',
                    'input'            => 'boolean',
                    'class'            => '',
                    'source'           => 'eav/entity_attribute_source_table',
                    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible'          => 1,
                    'required'         => 0,
                    'user_defined'     => 1,
                    'default'          => '',
                    'searchable'       => 0,
                    'filterable'       => 0,
                    'comparable'       => 0,
                    'visible_on_front' => 0,
                    'unique'           => 0,
                    'note'             => "If set to 'Yes' split product's name is removed from name_suggest value during indexing"
                )
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_returnCreatedResponse();
    }

    public function createRemoveShortDescriptionSuggestionsAttribute()
    {
        try {
            $this->addAttribute(
                 Mage_Catalog_Model_Product::ENTITY,
                     'remove_sd_suggestions',
                     array(
                         'group'            => 'Elasticsearch',
                         'type'             => 'int',
                         'frontend'         => '',
                         'backend'          => '',
                         'label'            => 'Remove Short Description Suggestions?',
                         'input'            => 'boolean',
                         'class'            => '',
                         'source'           => 'eav/entity_attribute_source_table',
                         'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                         'visible'          => 1,
                         'required'         => 0,
                         'user_defined'     => 1,
                         'default'          => '',
                         'searchable'       => 0,
                         'filterable'       => 0,
                         'comparable'       => 0,
                         'visible_on_front' => 0,
                         'unique'           => 0,
                         'note'             => "If set to 'Yes' product's short description is removed from name_suggest value during indexing"
                     )
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_returnCreatedResponse();
    }

    /**
     * @deprecated
     * @return string
     */
    public function createEsFieldsAnalyzedAttribute()
    {
        return $this;
    }

    protected function _getAttributeSetCollection()
    {
        $entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
        $sets         = Mage::getModel('eav/entity_attribute_set')
            ->getResourceCollection()
            ->addFilter('entity_type_id', $entityTypeId);
        return $sets;
    }

    /**
     * @param $group
     */
    protected function _trySavingGroup($group)
    {
        try {
            $group->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * @return string
     */
    protected function _returnCreatedResponse()
    {
        return Zend_Json::encode(array(
            "created" => true
        ));
    }
}