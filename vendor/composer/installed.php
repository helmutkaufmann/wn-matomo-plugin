<?php return array(
    'root' => array(
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'type' => 'winter-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => '39b115f6d9e3e764da90eeccfe4a772132c6134d',
        'name' => 'mercator/wn-matomo-plugin',
        'dev' => true,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => '1.x-dev',
            'version' => '1.9999999.9999999.9999999-dev',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'dev_requirement' => false,
        ),
        'mercator/wn-matomo-plugin' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'type' => 'winter-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => '39b115f6d9e3e764da90eeccfe4a772132c6134d',
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
    ),
);
