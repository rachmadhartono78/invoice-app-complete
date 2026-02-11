<?php

namespace App\Services;

use Google\Client;
use Google\Http\MediaFileUpload;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveResumableUpload
{
    private Client $client;

    private Drive $service;

    private const CHUNK_SIZE = 2 * 1024 * 1024; // 2MB chunks for optimal speed

    public function __construct(Client $client, Drive $service)
    {
        $this->client = $client;
        $this->service = $service;
    }

    /**
     * Upload file using resumable upload with streaming
     */
    public function streamUpload(UploadedFile $file, string $parentFolderId): array
    {
        try {
            $startTime = microtime(true);

            // Prepare file metadata
            $driveFile = new DriveFile;
            $driveFile->setName($file->getClientOriginalName());
            $driveFile->setParents([$parentFolderId]);

            // Enable deferred mode for resumable upload
            $this->client->setDefer(true);

            // Create the upload request
            $request = $this->service->files->create($driveFile, [
                'fields' => 'id,name,webViewLink,size,mimeType,createdTime',
            ]);

            // Create media upload with chunking
            $media = new MediaFileUpload(
                $this->client,
                $request,
                $file->getClientMimeType(),
                null,
                true,
                self::CHUNK_SIZE
            );

            $media->setFileSize($file->getSize());

            // Track upload progress
            $uploadedBytes = 0;
            $totalBytes = $file->getSize();
            $progressPercentage = 0;

            // Open file for streaming
            $handle = fopen($file->getRealPath(), 'rb');
            if (! $handle) {
                throw new \Exception('Could not open file for reading');
            }

            $status = false;
            $chunkNumber = 0;

            // Upload file in chunks
            while (! $status && ! feof($handle)) {
                $chunk = fread($handle, self::CHUNK_SIZE);
                $status = $media->nextChunk($chunk);

                $uploadedBytes += strlen($chunk);
                $chunkNumber++;

                $newProgressPercentage = round(($uploadedBytes / $totalBytes) * 100);

                // Log progress at every 10% interval
                if ($newProgressPercentage > $progressPercentage && $newProgressPercentage % 10 === 0) {
                    $progressPercentage = $newProgressPercentage;
                    Log::info("Google Drive upload progress: {$progressPercentage}% - Chunk {$chunkNumber}", [
                        'file_name' => $file->getClientOriginalName(),
                        'uploaded_mb' => round($uploadedBytes / 1024 / 1024, 2),
                        'total_mb' => round($totalBytes / 1024 / 1024, 2),
                    ]);
                }
            }

            fclose($handle);

            // Reset deferred mode
            $this->client->setDefer(false);

            if (! $status) {
                throw new \Exception('Upload failed - no response from Google Drive');
            }

            $endTime = microtime(true);
            $uploadTime = round(($endTime - $startTime) * 1000, 2);
            $uploadSpeed = round(($totalBytes / 1024 / 1024) / ($uploadTime / 1000), 2);

            Log::info('Google Drive resumable upload completed', [
                'file_name' => $file->getClientOriginalName(),
                'file_size_mb' => round($totalBytes / 1024 / 1024, 2),
                'upload_time_ms' => $uploadTime,
                'upload_speed_mbps' => $uploadSpeed,
                'chunks_uploaded' => $chunkNumber,
            ]);

            return [
                'success' => true,
                'file_id' => $status->getId(),
                'file_name' => $status->getName(),
                'web_view_link' => $status->getWebViewLink(),
                'download_link' => 'https://drive.google.com/uc?id='.$status->getId(),
                'size' => $status->getSize(),
                'mime_type' => $status->getMimeType(),
                'created_time' => $status->getCreatedTime(),
                'upload_time_ms' => $uploadTime,
                'upload_speed_mbps' => $uploadSpeed,
                'chunks_uploaded' => $chunkNumber,
            ];

        } catch (\Exception $e) {
            Log::error('Google Drive resumable upload failed: '.$e->getMessage(), [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload with progress callback (for real-time updates)
     */
    public function uploadWithProgress(UploadedFile $file, string $parentFolderId, callable $progressCallback): array
    {
        try {
            $startTime = microtime(true);

            // Prepare file metadata
            $driveFile = new DriveFile;
            $driveFile->setName($file->getClientOriginalName());
            $driveFile->setParents([$parentFolderId]);

            // Enable deferred mode
            $this->client->setDefer(true);

            $request = $this->service->files->create($driveFile, [
                'fields' => 'id,name,webViewLink,size,mimeType,createdTime',
            ]);

            $media = new MediaFileUpload(
                $this->client,
                $request,
                $file->getClientMimeType(),
                null,
                true,
                self::CHUNK_SIZE
            );

            $media->setFileSize($file->getSize());

            $uploadedBytes = 0;
            $totalBytes = $file->getSize();

            $handle = fopen($file->getRealPath(), 'rb');
            if (! $handle) {
                throw new \Exception('Could not open file for reading');
            }

            $status = false;

            while (! $status && ! feof($handle)) {
                $chunk = fread($handle, self::CHUNK_SIZE);
                $status = $media->nextChunk($chunk);

                $uploadedBytes += strlen($chunk);
                $progressPercentage = round(($uploadedBytes / $totalBytes) * 100);

                // Call the progress callback
                $progressCallback($uploadedBytes, $totalBytes, $progressPercentage);
            }

            fclose($handle);
            $this->client->setDefer(false);

            if (! $status) {
                throw new \Exception('Upload failed');
            }

            $endTime = microtime(true);
            $uploadTime = round(($endTime - $startTime) * 1000, 2);

            return [
                'success' => true,
                'file_id' => $status->getId(),
                'file_name' => $status->getName(),
                'web_view_link' => $status->getWebViewLink(),
                'download_link' => 'https://drive.google.com/uc?id='.$status->getId(),
                'size' => $status->getSize(),
                'mime_type' => $status->getMimeType(),
                'created_time' => $status->getCreatedTime(),
                'upload_time_ms' => $uploadTime,
            ];

        } catch (\Exception $e) {
            Log::error('Google Drive upload with progress failed: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
