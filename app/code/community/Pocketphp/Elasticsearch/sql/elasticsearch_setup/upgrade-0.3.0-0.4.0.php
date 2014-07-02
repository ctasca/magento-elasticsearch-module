<?php
/** @var $installer Pocketphp_Elasticsearch_Model_Setup */
$installer = $this;

$installer->startSetup();

$installer->createRemoveProductNameSuggestionsAttribute();

$installer->endSetup();