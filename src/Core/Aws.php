<?php

namespace Cavesman;

use Aws\S3\S3Client;

/**
 * Amazon S3 Client Service
 *
 * @uses S3Client
 */
class Aws
{

    /**
     * Upload file to S3
     *
     * @param string $bucket
     * @param int $id
     * @param string $file
     * @param string $type
     * @return string
     */
    public static function upload(string $bucket, int $id, string $file, string $type = 'general'): string
    {
        $s3 = new S3Client([
            'version' => Config::get('aws.s3.version', 'latest'),
            'region' => Config::get('aws.s3.region', 'eu-west-1'), // reemplazar con la región de tu bucket
            'credentials' => [
                'key' => Config::get('aws.s3.credentials.key', 'strong-key'),
                'secret' => Config::get('aws.s3.credentials.secret', 'strong-secret')
            ]
        ]);

        $target = Config::getEnv() . '/' . $type;

        foreach (str_split($id) as $num) {
            $target .= '/' . $num;
        }

        $target .= '/' . $id . '.' . pathinfo($file, PATHINFO_EXTENSION);


        // Enviamos el archivo a S3
        $s3->putObject([
            'Bucket' => $bucket,
            'Key' => $target,
            'SourceFile' => $file,
        ]);
        return $target;
    }

    /**
     * Download File to S3
     *
     * @param $bucket
     * @param string $file
     * @return string
     */
    public static function download(string $bucket, string $file): string
    {
        $s3 = new S3Client([
            'version' => Config::get('aws.s3.version', 'latest'),
            'region' => Config::get('aws.s3.region', 'eu-west-1'), // reemplazar con la región de tu bucket
            'credentials' => [
                'key' => Config::get('aws.s3.credentials.key', 'strong-key'),
                'secret' => Config::get('aws.s3.credentials.secret', 'strong-secret')
            ]
        ]);

        $result = $s3->getObject([
            'Bucket' => $bucket,
            'Key' => $file
        ]);
        return $result['Body'] ?? '';

    }


    /**
     * Download File to S3
     *
     * @param string $file
     * @return string
     */
    public static function delete(string $bucket, string $file): string
    {
        $s3 = new S3Client([
            'version' => Config::get('aws.s3.version', 'latest'),
            'region' => Config::get('aws.s3.region', 'eu-west-1'), // reemplazar con la región de tu bucket
            'credentials' => [
                'key' => Config::get('aws.s3.credentials.key', 'strong-key'),
                'secret' => Config::get('aws.s3.credentials.secret', 'strong-secret')
            ]
        ]);

        return $s3->deleteObject([
            'Bucket' => $bucket,
            'Key' => $file
        ]);

    }

    /**
     * Generate S3 url
     *
     * @param string $file
     * @return string
     */
    public static function getUrl(string $file): string
    {
        $s3 = new S3Client([
            'version' => Config::get('aws.s3.version', 'latest'),
            'region' => Config::get('aws.s3.region', 'eu-west-1'), // reemplazar con la región de tu bucket
            'credentials' => [
                'key' => Config::get('aws.s3.credentials.key', 'strong-key'),
                'secret' => Config::get('aws.s3.credentials.secret', 'strong-secret')
            ]
        ]);

        return $s3->getObjectUrl(Config::get('aws.s3.bucket', 'strong-bucket'), $file);
    }
}
