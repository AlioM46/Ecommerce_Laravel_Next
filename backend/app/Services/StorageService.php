<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Exception;

class StorageService
{
    private S3Client $s3;
    private string $bucket;
    private string $endpoint;
    private string $publicUrl;

    public function __construct()
    {
        $this->bucket = config('services.r2.bucket');
        $this->endpoint = config('services.r2.endpoint');
        $this->publicUrl = config('services.r2.public_url', $this->endpoint);

        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => 'auto',
            'endpoint' => $this->endpoint,
            'credentials' => [
                'key'    => config('services.r2.key'),
                'secret' => config('services.r2.secret'),
            ],
            'use_path_style_endpoint' => true,
        ]);
    }

    /**
     * Upload a file to R2
     */
    public function upload(UploadedFile $file): array
    {

        
        try {
            $key = $this->generateKey($file->getClientOriginalExtension());
            
            $resource = fopen($file->getRealPath(), 'r');
            
            try {
                $this->s3->putObject([
                    'Bucket' => $this->bucket,
                    'Key' => $key,
                    'Body' => $resource,
                    'ContentType' => $file->getMimeType(),
                ]);
            } finally {
                if (is_resource($resource)) {
                    fclose($resource);
                }
            }

            return [
                'key' => $key,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'url' => $this->permanentUrl($key),
                'uploaded_at' => now()->toIso8601String(),
            ];
        } catch (AwsException $e) {
            throw new Exception('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Get temporary signed URL
     */
    public function temporaryUrl(string $key, int $minutes = 120): string
    {
        try {
            $cmd = $this->s3->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);

            $request = $this->s3->createPresignedRequest($cmd, "+{$minutes} minutes");
            
            return (string) $request->getUri();
        } catch (AwsException $e) {
            throw new Exception('Failed to generate temporary URL: ' . $e->getMessage());
        }
    }

    /**
     * Get permanent public URL
     */
    public function permanentUrl(string $key): string
    {
        return rtrim($this->publicUrl, '/') . '/' . ltrim($key, '/');
    }

    
    /**
     * Delete a file from R2
     */
    public function delete(string $key): bool
    {
        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            throw new Exception('Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Check if file exists
     */
    public function exists(string $key): bool
    {
        try {
            return $this->s3->doesObjectExist($this->bucket, $key);
        } catch (AwsException $e) {
            throw new Exception('Failed to check file existence: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique filename key
     */
    private function generateKey(string $extension): string
    {
        return Str::uuid()->toString() . '.' . $extension;
    }
}