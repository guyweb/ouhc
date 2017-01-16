<?php

namespace OUFabric\OUCommon;

/**
* Data Persist class for data storage
*/
class DataPersist extends AbstractDataStore {
  
  function getTable() {
    return 'oucommon_1-data';
  }

}