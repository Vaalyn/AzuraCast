<?php

declare(strict_types=1);

namespace App\Entity\StorageLocationAdapter;

use App\Entity\Enums\StorageLocationAdapters;
use App\Flysystem\Adapter\ExtendedAdapterInterface;
use App\Flysystem\Adapter\WebDavAdapter;
use Sabre\DAV\Client AS WebDavClient;

final class WebDavStorageLocationAdapter extends AbstractStorageLocationLocationAdapter
{
    public function getType(): StorageLocationAdapters
    {
        return StorageLocationAdapters::WebDav;
    }

    public static function filterPath(string $path): string
    {
        if (str_ends_with($path, '/')) {
            return $path;
        }

        return $path . '/';
    }

    public function getStorageAdapter(): ExtendedAdapterInterface
    {
        $filteredPath = self::filterPath($this->storageLocation->getPath());
        return new WebDavAdapter($this->getClient(), $filteredPath);
    }

    private function getClient(): WebDavClient
    {
        return new WebDavClient([
            'baseUri' => $this->storageLocation->getWebDavBaseUri(),
            'userName' => $this->storageLocation->getWebDavUsername(),
            'password' => $this->storageLocation->getWebDavPassword(),
        ]);
    }
}
