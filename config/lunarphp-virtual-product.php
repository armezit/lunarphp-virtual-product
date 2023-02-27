<?php

return [

    'sources' => [
        \Armezit\Lunar\VirtualProduct\SourceProviders\CodePool::class,
    ],

    /*
     * Automatically register virtual product hub slots
     * Set it to false, if you want to manually register them
     */
    'register_hub_slots' => true,

    /*
     * The name (handle) of hub slot which you want to display virtual product component
     */
    'virtual_product_slot' => 'product.all',

    /*
     * primary table which holds sources of each product
     */
    'virtual_products_table' => 'virtual_products',

    /*
     * Config related to the "code_pool" source
     */
    'code_pool' => [

        /*
         * tables of the "code pool" source provider
         */
        'schema_table' => 'virtual_products_code_pool_schema',
        'items_table' => 'virtual_products_code_pool_items',
        'archive_table' => 'virtual_products_code_pool_archive',
        'batches_table' => 'virtual_products_code_pool_batches',

        'import' => [
            'chunk_size' => 10,
            'max_upload_size' => (int)ini_get('upload_max_filesize') ?: 10240,
        ]
    ],

];
