<?php

namespace OUFabric\OUCommon;

/**
* Data Persist class for data storage
*/
class DataCache extends AbstractDataStore {

  public function __construct( $keyBase = NULL, $hashKey = TRUE ) {
    parent::__construct( $keyBase, $hashKey );
  }

  function getTable() {
    return 'oucommon_1-cache';
  }

}

// EOF;