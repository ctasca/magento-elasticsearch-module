# Elasticsearch Module for Magento
License http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

## Requirements before installation

Elasticsearch should be installed and at least the master node started.
For set up instructions read [elasticsearch documentation](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/setup.html).

## Installation

### With Composer
<pre>
"require": {
        ...
        "pocketphp/elasticsearch" : "*",
        ...
    },
    ...
    "repositories": [
            ...
            {
                "type": "vcs",
                "url": "git@github.com:ctasca/magento-elasticsearch-module.git"
            },
            ...
        ],
</pre>

### Supported Magento Versions
Elasticsearch module works in latest Magento versions, including 1.9.0.1 with rwd design package.

### With Package

[Download latest tagged version on Github](https://github.com/ctasca/magento-elasticsearch-module/releases/)

After download, extract the package and merge the following folders to your Magento installation:

* app
* js

`composer.json` file and `magetest` directory are not needed.

#### After successful installation you should see...

1. In system configuration: POCKETPHP/Elasticsearch section
2. Within 'Catalog' section, in 'Catalog Search' tab you should be able to select 'Elasticsearch' as Search Engine
3. When viewing a product in 'Manage Products' you should see an 'Elasticsearch' attribute group

Remember to log out from Admin Panel before trying to access Elasticsearch section.

## Configuration

The following configutation sections are available:

1. Host Settings
2. Authentication Settings
3. Suggestions Settings
4. Search Settings
5. Mapping
6. Index Settings
7. Developer Settings

### Host Settings

* `Host` - The primary Elasticsearch host
* `Port` - The primary Elasticsearch host port number
* `Additional Hosts` - Comma separated Elasticsearch Additional Hosts. Can be IP + Port, Just IP, Domain + Port, Just Domain
  Examples: 192.168.1.2:9201, 192.168.1.2, mydomain.server.com:9201, mydomain2.server.com

### Authentication Settings

* `Enable Authentication` - Enables authentication when connecting to elasticsearch server/servers.
   **For this to work Elasticsearch needs to be configured to support authentication.**
   If used in production, it is advisable to set up a reverse proxy to secure elasticsearch cluster.

* `Auth Plugin` - The auth plugin to perform authentications. Currently only **Jetty** plugin is supported.

#### Jetty Plugin specific options

* `User`
* `Password`
* `Authentication Type` - The auth type to use. Accepts four different options: Basic, Digets, NTLM, Any

For installation and configuration of Jetty plugin refer to [elasticsearch-jetty repository](https://github.com/sonian/elasticsearch-jetty)

### Suggestions Settings

* `Size` - Set search suggestions max size. Defauls to 10.
* `Fuzzy Queries?` - The completion suggester also supports fuzzy queries - this means, customers can actually have a
   typo in their searches and still get results back.
* `Fuzziness` - Set search suggestions fuzziness. Increase fuzziness to increase suggestions for mispelled terms.

### Search Settings

* `Searched Fields CSV` - Comma separated list of fields used when performing searches. If left blank '_all' will be used.
   It is possible to boost a particular field by adding '^n' to the field name (where n is a number).
   For example name^5, desciption^10 will make description more relevant than name.
* `Search Query` - Dropdown. Search query executed when performing searches. Default is 'Query String'

#### Query String query specific options

* `Lowercase Exapnded Fields` - Boolean value. Default is 'No'.
* `Use Dis Max` - Boolean value. Should the queries be combined using dis_max. Default is 'Yes'

#### Fuzzy Like This query specific options

* `Ignore Term Frequency` - Boolean value. Should term frequency be ignored. Defaults to 'No'.
* `Max Query Terms` - Integer value. The maximum number of query terms that will be included in any generated query. Defaults to 25.
* `Fuzziness` - The minimum similarity of the term variants. Defaults to AUTO. See the section called “Fuzziness in [elasticsearch documentation](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/common-options.html#fuzziness).
* `Prefix Length` - Length of required common prefix on variant terms. Defaults to 0.
* `Boost` - Sets the boost value of the query. Defaults to 1.0.
* `Analyzer` - Optional. The analyser that will be used to analyse the text. Defaults to the analyser associated with the field.

### Mapping

* `Analyzed Fields CSV` - Comma separated value of fields to be analysed with corresponding analyser type. Example:
   title:snowball,description:snowball,name:standard.
   Warning! Analysing filterable attributes should be avoided. These are automatically set to 'not_analyzed' for correct faceting in layered navigation during indexing.

### Index Settings
* `Number of Shards` -  Number of shards. Default is 5.
* `Number of Replicas` - Number of replicas. Default is 0.

## Enabling the Engine

To enable Elasticsearch in Magento, go to System Configuration, Catalog, Catalog Search and choose Elasticsearch as Search Engine.
After enabling the engine, rebuild Catalog product fulltext search index in Index Management.

## The Elasticsearch Attributes Group

Each product has an `Elasticsearch` Attributes group. This group mainly focus on the Completion Suggester API of Elasticsearch.

Attributes within the group:

* `Name Suggest Input` - You can procide a comma separated list of terms you want that product returned in search suggestion's lists.
   It basically provides a tagging system for the name of the product. For example you may want touch screen phones products tagged with the words 'touch' and 'smartphone'
   to achieve better suggestion results.
* `Name Suggest Weight` - The boost value for suggestion terms for a product.
* `Remove Category Names Suggestions?` - If set to 'Yes' product's category names are removed from name_suggest value during indexing.
   By default during indexing process, category names for product are added to name suggest input value.
* `Remove Product Name Suggestions?` - If set to 'Yes' split product's name is removed from name_suggest value during indexing.
   By default during indexing process the product name is split into words and added to name suggest input value. Set to 'No' to remove.
* `Remove Short Description Suggestions?` - If set to 'Yes' product's short description is removed from name_suggest value during indexing.
   By default during indexing the short desciption of a product is added as a single value to name suggest value. Set to 'No' to remove.


Note: the output of suggestions is always the name of the product

![Elasticsearch Suggester Attributes Group](https://dl.dropboxusercontent.com/u/81104563/ed86f232ec8de6f908fa48046bfc9be9-1.png)

## How it compares with default search?

Have you ever noticed searching for 'mens' in default Magento store with sample data returns high hill shoes?

Here are two screenshots, 'mens' search performed with Magento default search and with Elasticserach

### With Magento search

![Magento store results](https://dl.dropboxusercontent.com/u/81104563/Search%20results%20for%20%20%20mens%20%20%20%20Magento%20Commerce%20Demo%20Store.png)

### With Elasticsearch module

![Elasticsearch module results](https://dl.dropboxusercontent.com/u/81104563/Search%20results%20for%20mens%20.png)