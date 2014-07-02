<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_SearcherSpec extends ObjectBehavior
{
    /**
     * Available matchers:
     * - shouldReturnSearchResults  (compares an expected search results array against actual search results)
     *
     * Unsetting took and hits max score. They change on each request causing tests to fail
     *
     * @return array
     */
    public function getMatchers()
    {
        return array(
            'returnSearchResults' => function($subject, $expected) {
                    unset($subject['took']);
                    unset($subject['hits']['max_score']);
                    unset($expected['took']);
                    unset($expected['hits']['max_score']);

                    if(count(array_diff_assoc($subject, $expected)) > 0 ||  count(array_diff_assoc($expected, $subject)) > 0) {
                        throw new \Exception("Search results array do not match expected results array");
                    }
                    return true;
                },
        );
    }
/*
    function it_performs_a_match_all_query_and_returns_expected_response()
    {
        $responseJson = '{
   "took": 5,
   "timed_out": false,
   "_shards": {
      "total": 5,
      "successful": 5,
      "failed": 0
   },
   "hits": {
      "total": 3,
      "max_score": 0.13975425,
      "hits": [
         {
            "_index": "products_1",
            "_type": "product",
            "_id": "159",
            "_score": 0.13975425,
            "_source": {
               "sku": "microsoftnatural",
               "status": 1,
               "visibility": 4,
               "manufacturer": 120,
               "enable_googlecheckout": 0,
               "name": "Microsoft Natural Ergonomic Keyboard 4000",
               "image_label": null,
               "small_image_label": null,
               "thumbnail_label": null,
               "description": "The most comfortable ergonomic keyboard on the market! We just made a great deal for this Microsoft Natural ergonomic keyboard. And we know you’re going to love it. This newest addition to the world’s best selling line of ergonomic keyboards features a natural wrist alignment that will make your day! Just one touch allows you to perform a wealth of common but important tasks such as opening documents and replying to e-mail. The Microsoft Natural Model 4000 ergonomic keyboard also features an improved number pad with easy-to-reach symbols such left and right, equal sign and back space placed just above the number pad. Easy-access to the Internet. Multimedia keys. Lockable F keys and much more (see complete list of additional features below). Don’t you think it’s time to go natural? Microsoft’s Natural Ergonomic Model 4000 Keyboard. Available right here, for the best price!",
               "short_description": "The most comfortable ergonomic keyboard on the market! We just made a great deal for this Microsoft Natural ergonomic keyboard.",
               "meta_keyword": null,
               "price": 99.99,
               "in_stock": "1",
               "#categories": "34",
               "#show_in_categories": "15",
               "#position_category_15": "90036",
               "#position_category_3": "90036",
               "#position_category_34": "0",
               "#position_category_13": "90036",
               "#visibility": "4",
               "#price_0_1": 99.99,
               "#price_1_1": 99.99,
               "#price_2_1": 99.99,
               "#price_3_1": 99.99,
               "#price_4_1": 99.99,
               "_options": [
                  "Microsoft"
               ],
               "entity_id": "159",
               "type_id": "simple",
               "store_id": "0",
               "store_ids": [
                  "1",
                  "3",
                  "2"
               ],
               "set_id": "9",
               "category_ids": [
                  "1",
                  "3",
                  "13",
                  "15",
                  "34"
               ],
               "name_suggest": {
                  "input": [
                     "Touch",
                     "Microsoft",
                     "4000",
                     "Keybord",
                     "Micro",
                     "peripherals",
                     "microsoft",
                     "natural",
                     "ergonomic",
                     "keyboard",
                     "4000"
                  ],
                  "output": "Microsoft Natural Ergonomic Keyboard 4000",
                  "weight": 1,
                  "payload": {
                     "id": "159",
                     "path": "microsoft-natural-ergonomic-keyboard-4000.html"
                  }
               }
            }
         },
         {
            "_index": "products_1",
            "_type": "product",
            "_id": "147",
            "_score": 0.088388346,
            "_source": {
               "sku": "226bw",
               "status": 1,
               "visibility": 4,
               "manufacturer": 3,
               "contrast_ratio": 110,
               "name": "22\" Syncmaster LCD Monitor",
               "screensize": "22\"",
               "meta_keyword": null,
               "description": "The wide, 16:10 format of SAMSUNG\'s 226BW digital/Analog widescreen LCD monitor gives you plenty of room for all your computer applications and multiple images. DC 3000:1 contrast ratio creates crisp, easy-to-view images and gaming graphics, without ghosting or blurring. Complete with Microsoft® Vista® Premium certification and advanced dual interface for both analog digital video signals, the 226BW monitor is future-ready.",
               "short_description": "The wide, 16:10 format of SAMSUNG\'s 226BW digital/Analog widescreen LCD monitor gives you plenty of room for all your computer applications and multiple images. DC 3000:1 contrast ratio creates crisp, easy-to-view images and gaming graphics, without ghosting or blurring. Complete with Microsoft® Vista® Premium certification and advanced dual interface for both analog digital video signals, the 226BW monitor is future-ready.",
               "price": 399.99,
               "in_stock": "1",
               "#categories": "30",
               "#show_in_categories": "13",
               "#position_category_30": "0",
               "#position_category_13": "50020",
               "#position_category_15": "50020",
               "#position_category_3": "50020",
               "#visibility": "4",
               "#price_0_1": 399.99,
               "#price_1_1": 399.99,
               "#price_2_1": 399.99,
               "#price_3_1": 399.99,
               "#price_4_1": 399.99,
               "_options": [
                  "Samsung",
                  "3000:1"
               ],
               "entity_id": "147",
               "type_id": "simple",
               "store_id": "0",
               "store_ids": [
                  "1",
                  "3",
                  "2"
               ],
               "set_id": "61",
               "category_ids": [
                  "1",
                  "3",
                  "13",
                  "15",
                  "30"
               ],
               "name_suggest": {
                  "input": [
                     "monitors",
                     "22",
                     "syncmaster",
                     "lcd",
                     "monitor"
                  ],
                  "output": "22\" Syncmaster LCD Monitor",
                  "weight": 1,
                  "payload": {
                     "id": "147",
                     "path": "22-syncmaster-lcd-monitor.html"
                  }
               }
            }
         },
         {
            "_index": "products_1",
            "_type": "product",
            "_id": "162",
            "_score": 0.040683318,
            "_source": {
               "sku": "micronmouse5000",
               "status": 1,
               "visibility": 4,
               "manufacturer": 120,
               "color": 24,
               "enable_googlecheckout": 0,
               "name": "Microsoft Wireless Optical Mouse 5000",
               "image_label": null,
               "small_image_label": null,
               "thumbnail_label": null,
               "meta_keyword": null,
               "description": "Experience smoother tracking and wireless freedom. Navigate with enhanced precision with this ergonomic High Definition Optical mouse. Easily enlarge and edit detail with the new Magnifier and enjoy more than six months of battery life.",
               "short_description": "Experience smoother tracking and wireless freedom. Navigate with enhanced precision with this ergonomic High Definition Optical mouse.",
               "price": 59.99,
               "in_stock": "1",
               "#categories": "34",
               "#show_in_categories": "13",
               "#position_category_13": "90036",
               "#position_category_3": "90036",
               "#position_category_15": "90036",
               "#position_category_34": "0",
               "#visibility": "4",
               "#price_0_1": 59.99,
               "#price_1_1": 59.99,
               "#price_2_1": 59.99,
               "#price_3_1": 59.99,
               "#price_4_1": 59.99,
               "_options": [
                  "Microsoft",
                  "Black"
               ],
               "entity_id": "162",
               "type_id": "simple",
               "store_id": "0",
               "store_ids": [
                  "1",
                  "3",
                  "2"
               ],
               "set_id": "9",
               "category_ids": [
                  "1",
                  "3",
                  "13",
                  "15",
                  "34"
               ],
               "name_suggest": {
                  "input": [
                     "Touch",
                     "Mouse",
                     "Optical",
                     "Micro",
                     "peripherals",
                     "microsoft",
                     "wireless",
                     "optical",
                     "mouse",
                     "5000"
                  ],
                  "output": "Microsoft Wireless Optical Mouse 5000",
                  "weight": 1,
                  "payload": {
                     "id": "162",
                     "path": "microsoft-wireless-optical-mouse-5005.html"
                  }
               }
            }
         }
      ]
   }
}';
        $expected = \Zend_Json::decode($responseJson);
        $searchStore =  \Mage::app()
                             ->getStore(1);
        $query = \Mage::getModel('elasticsearch/query_matchall', $searchStore);
        $query->setIndexName('products');
        $query->setIndexType('product');
        $query->setQ("microsoft");


        $this->search($query)->shouldReturnSearchResults($expected);
    }*/

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Searcher');
    }

    function it_performs_a_filtered_query_with_filters_and_facets()
    {
        $responseJson = '{
   "took": 2,
   "timed_out": false,
   "_shards": {
      "total": 5,
      "successful": 5,
      "failed": 0
   },
   "hits": {
      "total": 3,
      "max_score": 0.35860232,
      "hits": [
         {
            "_index": "products_1",
            "_type": "product",
            "_id": "162",
            "_score": 0.35860232,
            "_source": {
               "sku": "micronmouse5000",
               "status": 1,
               "visibility": 4,
               "manufacturer": 120,
               "color": 24,
               "enable_googlecheckout": 0,
               "name": "Microsoft Wireless Optical Mouse 5000",
               "image_label": null,
               "small_image_label": null,
               "thumbnail_label": null,
               "meta_keyword": null,
               "description": "Experience smoother tracking and wireless freedom. Navigate with enhanced precision with this ergonomic High Definition Optical mouse. Easily enlarge and edit detail with the new Magnifier and enjoy more than six months of battery life.",
               "short_description": "Experience smoother tracking and wireless freedom. Navigate with enhanced precision with this ergonomic High Definition Optical mouse.",
               "price": 59.99,
               "in_stock": "1",
               "#categories": "34",
               "#show_in_categories": "13",
               "#position_category_13": "90036",
               "#position_category_3": "90036",
               "#position_category_15": "90036",
               "#position_category_34": "0",
               "#visibility": "4",
               "#price_0_1": 59.99,
               "#price_1_1": 59.99,
               "#price_2_1": 59.99,
               "#price_3_1": 59.99,
               "#price_4_1": 59.99,
               "_options": [
                  "Microsoft",
                  "Black"
               ],
               "entity_id": "162",
               "type_id": "simple",
               "store_id": "0",
               "store_ids": [
                  "1",
                  "3",
                  "2"
               ],
               "set_id": "9",
               "category_ids": [
                  "1",
                  "3",
                  "13",
                  "15",
                  "34"
               ],
               "name_suggest": {
                  "input": [
                     "Touch",
                     "Mouse",
                     "Optical",
                     "Micro",
                     "peripherals",
                     "microsoft",
                     "wireless",
                     "optical",
                     "mouse",
                     "5000"
                  ],
                  "output": "Microsoft Wireless Optical Mouse 5000",
                  "weight": 1,
                  "payload": {
                     "id": "162",
                     "path": "microsoft-wireless-optical-mouse-5005.html"
                  }
               }
            }
         },
         {
            "_index": "products_1",
            "_type": "product",
            "_id": "159",
            "_score": 0.32133454,
            "_source": {
               "sku": "microsoftnatural",
               "status": 1,
               "visibility": 4,
               "manufacturer": 120,
               "enable_googlecheckout": 0,
               "name": "Microsoft Natural Ergonomic Keyboard 4000",
               "image_label": null,
               "small_image_label": null,
               "thumbnail_label": null,
               "description": "The most comfortable ergonomic keyboard on the market! We just made a great deal for this Microsoft Natural ergonomic keyboard. And we know you’re going to love it. This newest addition to the world’s best selling line of ergonomic keyboards features a natural wrist alignment that will make your day! Just one touch allows you to perform a wealth of common but important tasks such as opening documents and replying to e-mail. The Microsoft Natural Model 4000 ergonomic keyboard also features an improved number pad with easy-to-reach symbols such left and right, equal sign and back space placed just above the number pad. Easy-access to the Internet. Multimedia keys. Lockable F keys and much more (see complete list of additional features below). Don’t you think it’s time to go natural? Microsoft’s Natural Ergonomic Model 4000 Keyboard. Available right here, for the best price!",
               "short_description": "The most comfortable ergonomic keyboard on the market! We just made a great deal for this Microsoft Natural ergonomic keyboard.",
               "meta_keyword": null,
               "price": 99.99,
               "in_stock": "1",
               "#categories": "34",
               "#show_in_categories": "15",
               "#position_category_15": "90036",
               "#position_category_3": "90036",
               "#position_category_34": "0",
               "#position_category_13": "90036",
               "#visibility": "4",
               "#price_0_1": 99.99,
               "#price_1_1": 99.99,
               "#price_2_1": 99.99,
               "#price_3_1": 99.99,
               "#price_4_1": 99.99,
               "_options": [
                  "Microsoft"
               ],
               "entity_id": "159",
               "type_id": "simple",
               "store_id": "0",
               "store_ids": [
                  "1",
                  "3",
                  "2"
               ],
               "set_id": "9",
               "category_ids": [
                  "1",
                  "3",
                  "13",
                  "15",
                  "34"
               ],
               "name_suggest": {
                  "input": [
                     "Touch",
                     "Microsoft",
                     "4000",
                     "Keybord",
                     "Micro",
                     "peripherals",
                     "microsoft",
                     "natural",
                     "ergonomic",
                     "keyboard",
                     "4000"
                  ],
                  "output": "Microsoft Natural Ergonomic Keyboard 4000",
                  "weight": 1,
                  "payload": {
                     "id": "159",
                     "path": "microsoft-natural-ergonomic-keyboard-4000.html"
                  }
               }
            }
         },
         {
            "_index": "products_1",
            "_type": "product",
            "_id": "147",
            "_score": 0.2032298,
            "_source": {
               "sku": "226bw",
               "status": 1,
               "visibility": 4,
               "manufacturer": 3,
               "contrast_ratio": 110,
               "name": "22\" Syncmaster LCD Monitor",
               "screensize": "22\"",
               "meta_keyword": null,
               "description": "The wide, 16:10 format of SAMSUNG\'s 226BW digital/Analog widescreen LCD monitor gives you plenty of room for all your computer applications and multiple images. DC 3000:1 contrast ratio creates crisp, easy-to-view images and gaming graphics, without ghosting or blurring. Complete with Microsoft® Vista® Premium certification and advanced dual interface for both analog digital video signals, the 226BW monitor is future-ready.",
               "short_description": "The wide, 16:10 format of SAMSUNG\'s 226BW digital/Analog widescreen LCD monitor gives you plenty of room for all your computer applications and multiple images. DC 3000:1 contrast ratio creates crisp, easy-to-view images and gaming graphics, without ghosting or blurring. Complete with Microsoft® Vista® Premium certification and advanced dual interface for both analog digital video signals, the 226BW monitor is future-ready.",
               "price": 399.99,
               "in_stock": "1",
               "#categories": "30",
               "#show_in_categories": "13",
               "#position_category_30": "0",
               "#position_category_13": "50020",
               "#position_category_15": "50020",
               "#position_category_3": "50020",
               "#visibility": "4",
               "#price_0_1": 399.99,
               "#price_1_1": 399.99,
               "#price_2_1": 399.99,
               "#price_3_1": 399.99,
               "#price_4_1": 399.99,
               "_options": [
                  "Samsung",
                  "3000:1"
               ],
               "entity_id": "147",
               "type_id": "simple",
               "store_id": "0",
               "store_ids": [
                  "1",
                  "3",
                  "2"
               ],
               "set_id": "61",
               "category_ids": [
                  "1",
                  "3",
                  "13",
                  "15",
                  "30"
               ],
               "name_suggest": {
                  "input": [
                     "monitors",
                     "22",
                     "syncmaster",
                     "lcd",
                     "monitor"
                  ],
                  "output": "22\" Syncmaster LCD Monitor",
                  "weight": 1,
                  "payload": {
                     "id": "147",
                     "path": "22-syncmaster-lcd-monitor.html"
                  }
               }
            }
         }
      ]
   },
   "facets": {
      "#categories": {
         "_type": "terms",
         "missing": 0,
         "total": 3,
         "other": 0,
         "terms": [
            {
               "term": "34",
               "count": 2
            },
            {
               "term": "30",
               "count": 1
            }
         ]
      },
      "color": {
         "_type": "terms",
         "missing": 2,
         "total": 1,
         "other": 0,
         "terms": [
            {
               "term": 24,
               "count": 1
            }
         ]
      },
      "#price_0_1": {
         "_type": "statistical",
         "count": 3,
         "total": 559.97,
         "min": 59.99,
         "max": 399.99,
         "mean": 186.65666666666667,
         "sum_of_squares": 173588.8003,
         "variance": 23022.222222222223,
         "std_deviation": 151.73075568988057
      }
   }
}';
        $queryString = \Mage::getModel('elasticsearch/query_string', \Mage::app()->getStore(1));
        $queryString->setDefaultField('_all');
        $queryString->setQueryString('microsoft');

        $boolQuery = \Mage::getModel('elasticsearch/query_bool', \Mage::app()->getStore(1));
        $boolQuery->addShould($queryString);

        $facets = array();
        $facets['#categories']['terms']['field'] = "#categories";
        $facets['color']['terms']['field'] = "color";
        $facets['#price_0_1']['statistical']['field'] = "#price_0_1";



        $expected = \Zend_Json::decode($responseJson);
        $searchStore =  \Mage::app()
                             ->getStore(1);
        $query = \Mage::getModel('elasticsearch/query_filtered', $searchStore);
        $query->setIndexName('products');
        $query->setIndexType('product');
        $query->setFilterType('and');
        //$query->setFilters(array($rangeFilter));
        $query->setFacets($facets);
        $query->setFQuery($boolQuery);
        $this->search($query)->shouldReturnSearchResults($expected);
    }
}
