<?php
namespace ARC\ProductConfigurator\Filesystem;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

/**
 * Class AmazonS3.
 *
 * @package ARC\ProductConfigurator\Filesystem
 */
class AmazonS3
{
    /**
     * Get Amazon S3 filesystem object.
     *
     * @param string $prefix
     *
     * @return Filesystem
     */
    public static function get(string $prefix): Filesystem
    {
        $client = new S3Client([
            'credentials' => [
                'key'    => env('AMAZON_S3_KEY'),
                'secret' => env('AMAZON_S3_SECRET'),
            ],
            'region' => env('AMAZON_S3_REGION'),
            'version' => '2006-03-01',
        ]);

        $adapter = new AwsS3Adapter($client, env('AMAZON_S3_BUCKET'), $prefix);

        return new Filesystem($adapter);
    }
}
