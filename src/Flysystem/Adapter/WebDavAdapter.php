<?php

namespace App\Flysystem\Adapter;

use App\Flysystem\Attributes\FileAttributes;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\WebDAV\WebDAVAdapter as WebDAVWebDAVAdapter;
use Sabre\DAV\Client as WebDavClient;
use Throwable;

final class WebDavAdapter extends WebDAVWebDAVAdapter implements ExtendedAdapterInterface
{
    public const METADATA_FIND_PROPERTIES = [
        '{DAV:}displayname',
        '{DAV:}getcontentlength',
        '{DAV:}getcontenttype',
        '{DAV:}getlastmodified',
    ];

    public function __construct(
        private readonly WebDavClient $client,
        string $prefix = ''
    ) {
        parent::__construct($client, $prefix);
    }

    /** @inheritDoc */
    public function getMetadata(string $path): StorageAttributes
    {
        try {
            $metadata = $this->client->propFind($path, self::METADATA_FIND_PROPERTIES, 0);
        } catch (Throwable $exception) {
            throw UnableToRetrieveMetadata::create($path, 'metadata', '', $exception);
        }

        $metadata = $this->normalizeObject($metadata);

        return new FileAttributes(
            $path,
            fileSize: $metadata['file_size'] ?? null,
            lastModified: $metadata['last_modified'] ?? null,
            mimeType: $metadata['mime_type'] ?? null,
        );
    }

    protected function encodePath(string $path): string
    {
        $parts = explode('/', $path);

        foreach ($parts as $i => $part) {
            $parts[$i] = rawurlencode($part);
        }

        $ret = implode('/', $parts);

        return $ret;
    }

    private function normalizeObject(array $object): array
    {
        $mapping = [
            '{DAV:}getcontentlength' => 'file_size',
            '{DAV:}getcontenttype' => 'mime_type',
            'content-length' => 'file_size',
            'content-type' => 'mime_type',
        ];

        foreach ($mapping as $from => $to) {
            if (array_key_exists($from, $object)) {
                $object[$to] = $object[$from];
            }
        }

        array_key_exists('file_size', $object) && $object['file_size'] = (int) $object['file_size'];

        if (array_key_exists('{DAV:}getlastmodified', $object)) {
            $object['last_modified'] = strtotime($object['{DAV:}getlastmodified']);
        }

        return $object;
    }
}
