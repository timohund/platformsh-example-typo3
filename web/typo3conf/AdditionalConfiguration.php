<?php
$relationships = getenv("PLATFORM_RELATIONSHIPS");
if ($relationships) {

    $relationships = json_decode(base64_decode($relationships), TRUE);

    foreach ($relationships['database'] as $endpoint) {
        if (empty($endpoint['query']['is_master'])) {
            continue;
        }
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dbal']['handlerCfg']['_DEFAULT']['config']['driver'] = 'mysqli';
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driver'] = 'mysqli';
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['host'] = $endpoint['host'];
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['port'] = $endpoint['port'];
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['dbname'] = $endpoint['path'];
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['user'] = $endpoint['username'];
        $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['password'] = $endpoint['password'];
    }

    $redisHost = "";
    $redisPort = "";
    foreach ($relationships['redis'] as $endpoint) {
        $redisHost = $endpoint['host'];
        $redisPort = $endpoint['port'];
    }

    $list = [
        'cache_pages' => 86400,
        'cache_pagesection' => 86400,
        'cache_hash' => 86400,
        'extbase_object' => 0,
        'extbase_reflection' => 0,
        'extbase_datamapfactory_datamap' => 0
    ];

    $counter = 3;
    foreach ($list as $key => $lifetime) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$key]['backend'] = \TYPO3\CMS\Core\Cache\Backend\RedisBackend::class;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$key]['options'] = [
            'database' => $counter++,
            'hostname' => $redisHost,
            'port' => $redisPort,
            'defaultLifetime' => $lifetime
        ];
    }

    foreach($relationships as $relationName => $relationship) {
        if(substr($relationName, 0, 4) !== 'solr') {
            continue;
        }
        $languageKey = str_replace('solr-', '', $relationName);
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['Connections'][$languageKey] = $relationship[0];
    }

}