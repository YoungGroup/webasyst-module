<?php

/**
 * Class shopRetailcrmPluginBackendRequestController
 */
class shopRetailcrmPluginBackendRequestController extends waController
{
    /**
     * @param array $data
     */
    public function execute(array $data)
    {
        if ($data['is_create'])
            (new shopRetailcrmPluginBackendCreateController())->execute($data['id']);
        elseif ($data['is_update'])
            (new shopRetailcrmPluginBackendUpdateController())->execute($data['id']);
        elseif ($data['is_delete'])
            (new shopRetailcrmPluginBackendDeleteController())->execute($data['id']);
    }
}
