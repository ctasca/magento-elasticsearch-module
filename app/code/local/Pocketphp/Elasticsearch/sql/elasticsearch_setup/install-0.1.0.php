<?php
/** @var $installer Pocketphp_Elasticsearch_Model_Setup */
$installer = $this;

$installer->startSetup();

$installer->createElasticsearchAttributeGroup();
$installer->createNameSuggestInputAttribute();
$installer->createNameSuggestWeightAttribute();

$installer->endSetup();