<?php
return [
    'import' => 'Import Plugin',
    'file_format' => 'File format',
    'choose_file' => 'Choose File',
    'import_submit' => 'Import',
    'import_data' => 'Import :data',
    'import_note' => 'File <span style="color:red">.zip</span>, max size is <span style="color:red">50MB</span>',
    'error_unzip' => 'Error while unzip',
    'error_upload' => 'Error while uploading file',
    'error_check_config' => 'Cannot find config file',
    'error_config_format' => 'The config file is not in the right format',
    'import_success' => 'Import success!',
    'error_exist' => 'Plugin exist!',
    'plugin_import' => '<a href="'.route('admin_plugin.import').'" target=_new><span class="btn btn-success btn-flat"><i class="fa fa-floppy-o" aria-hidden="true"></i> Import Plugin</span></a>',
    'plugin_more' => '<a href="https://black-cart.org/plugin.html" target=_new><i class="fa fa-download" aria-hidden="true"></i> Download more HERE</a>',
    'manager'     => 'Plugins manager',
    'Shipping'    => 'Shipping <span class="right badge badge-warning">' . count(bc_get_all_plugin('Shipping')) . '</span>',
    'Shipping_plugin'    => 'Shipping extension',
    'Payment'     => 'Payment <span class="right badge badge-warning">' . count(bc_get_all_plugin('Payment')) . '</span>',
    'Payment_plugin'     => 'Payment extension',
    'Total'       => 'Total <span class="right badge badge-warning">' . count(bc_get_all_plugin('Total')) . '</span>',
    'Total_plugin'       => 'Total extension',
    'Other'       => 'Other plugin <span class="right badge badge-warning">' . count(bc_get_all_plugin('Other')) . '</span>',
    'Other_plugin'       => 'Other plugin',
    'Cms' => 'Cms plugins <span class="right badge badge-warning">' . count(bc_get_all_plugin('Cms')) . '</span>',
    'Cms_plugin' => 'Cms plugins',
    'Api' => 'Api plugins <span class="right badge badge-warning">' . count(bc_get_all_plugin('Api')) . '</span>',
    'Api_plugin' => 'Api plugins',
    'Block' => 'Block plugins <span class="right badge badge-warning">' . count(bc_get_all_plugin('Block')) . '</span>',
    'Block_plugin' => 'Block plugins',
    'libraries' => [
        'only_free' => 'Is free',
        'only_version' => 'Only version',
        'all_version' => 'All version',
        'sort_price_asc' => 'Price asc',
        'sort_price_desc' => 'Price desc',
        'sort_rating' => 'Rating',
        'sort_download' => 'Download',
        'search_keyword' => 'Keyword',
        'enter_search_keyword' => 'Enter keyword',
        'search_submit' => 'Filter result',
    ],
    'compatible'       => 'Compatible',
    'code'             => 'Code',
    'name'             => 'Name',
    'sort'             => 'Sort',
    'action'           => 'Action',
    'status'           => 'Status',
    'enable'           => 'Enable',
    'disable'          => 'Disable',
    'remove'           => 'Remove',
    'only_delete_data' => 'Only remove data',
    'install'          => 'Install',
    'config'           => 'Config',
    'actived'          => 'Actived',
    'disabled'         => 'Disabled',
    'not_install'      => 'Not install',
    'auth'             => 'Auth',
    'version'          => 'Version',
    'link'             => 'Link',
    'image'            => 'Image',
    'empty'            => 'Empty extension!',
    'local'            => 'Save local',
    'online'           => 'From library',
    'downloaded'       => 'Downloaded',
    'rated'            => 'Rated',
    'price'            => 'Price',
    'free'             => 'Free',
    'date'             => 'Date',
    'located'          => 'Located',
    'plugin_action' => [
        'install_success' => 'Installed successfully',
        'install_faild' => 'Installation failed',
        'table_exist' => 'Table :table already exists',
        'plugin_exist' => 'This plugin already exists',
        'action_error' => 'There was an error while :action',
    ],
];
