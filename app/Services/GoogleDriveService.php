<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    private static ?Client $client = null;

    private static ?Drive $service = null;

    private static ?GuzzleClient $httpClient = null;

    private string $folderId;

    private array $folderCache = [];

    public function __construct()
    {
        $this->folderId = config('filesystems.disks.google.folder_id');
        $this->initializeClients();
    }

    /**
     * Initialize Google Client and Drive Service (singleton pattern)
     */
    private function initializeClients(): void
    {
        if (self::$client === null) {
            // Create optimized HTTP client with keep-alive and HTTP/2
            self::$httpClient = new GuzzleClient([
                RequestOptions::TIMEOUT => 60,
                RequestOptions::CONNECT_TIMEOUT => 10,
                'version' => '2.0', // HTTP/2
                'headers' => [
                    'Connection' => 'keep-alive',
                    'Keep-Alive' => 'timeout=60, max=100',
                ],
                'curl' => [
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                    CURLOPT_TCP_KEEPALIVE => 1,
                    CURLOPT_TCP_KEEPIDLE => 60,
                    CURLOPT_TCP_KEEPINTVL => 30,
                ],
            ]);

            self::$client = new Client;
            self::$client->setHttpClient(self::$httpClient);
            self::$client->setClientId(config('filesystems.disks.google.client_id'));
            self::$client->setClientSecret(config('filesystems.disks.google.client_secret'));
            self::$client->refreshToken(config('filesystems.disks.google.refresh_token'));

            // Set application name for better API quotas
            self::$client->setApplicationName('E-Logbook Google Drive Integration');

            self::$service = new Drive(self::$client);
        }
    }

    /**
     * Upload a file to Google Drive (optimized with streaming for large files)
     */
    public function uploadFile(UploadedFile $file, ?string $subfolder = null): array
    {
        try {
            $startTime = microtime(true);
            $parentFolderId = $this->folderId;

            if ($subfolder) {
                $parentFolderId = $this->getOrCreateFolderOptimized($subfolder, $this->folderId);
            }

            // Use streaming upload for large files (> 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                $resumableUpload = new GoogleDriveResumableUpload(self::$client, self::$service);
                $result = $resumableUpload->streamUpload($file, $parentFolderId);

                if ($result['success']) {
                    $this->setFilePermissionOptimized($result['file_id']);
                }
            } else {
                // Direct upload for smaller files
                $driveFile = new DriveFile;
                $driveFile->setName($file->getClientOriginalName());
                $driveFile->setParents([$parentFolderId]);

                // Upload with minimal fields for faster response
                $uploadedFile = self::$service->files->create(
                    $driveFile,
                    [
                        'data' => file_get_contents($file->getRealPath()),
                        'mimeType' => $file->getClientMimeType(),
                        'uploadType' => 'multipart',
                        'fields' => 'id,name,webViewLink,size,mimeType,createdTime',
                    ]
                );

                // Make the file publicly accessible in batch to minimize round trips
                $this->setFilePermissionOptimized($uploadedFile->getId());

                $result = [
                    'success' => true,
                    'file_id' => $uploadedFile->getId(),
                    'file_name' => $uploadedFile->getName(),
                    'web_view_link' => $uploadedFile->getWebViewLink(),
                    'download_link' => 'https://drive.google.com/uc?id='.$uploadedFile->getId(),
                    'size' => $uploadedFile->getSize(),
                    'mime_type' => $uploadedFile->getMimeType(),
                    'created_time' => $uploadedFile->getCreatedTime(),
                ];
            }

            $endTime = microtime(true);
            $uploadTime = round(($endTime - $startTime) * 1000, 2);

            if ($result['success']) {
                $result['upload_time_ms'] = $uploadTime;
                $result['upload_speed_mbps'] = round(($file->getSize() / 1024 / 1024) / ($uploadTime / 1000), 2);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Google Drive upload failed: '.$e->getMessage(), [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'subfolder' => $subfolder,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload content as a file to Google Drive (optimized)
     */
    public function uploadContent(string $content, string $filename, string $mimeType = 'text/plain', ?string $subfolder = null): array
    {
        try {
            $parentFolderId = $this->folderId;

            if ($subfolder) {
                $parentFolderId = $this->getOrCreateFolderOptimized($subfolder, $this->folderId);
            }

            $driveFile = new DriveFile;
            $driveFile->setName($filename);
            $driveFile->setParents([$parentFolderId]);

            $result = self::$service->files->create(
                $driveFile,
                [
                    'data' => $content,
                    'mimeType' => $mimeType,
                    'uploadType' => 'multipart',
                    'fields' => 'id,name,webViewLink,size,mimeType,createdTime',
                ]
            );

            // Make the file publicly accessible
            $this->setFilePermissionOptimized($result->getId());

            return [
                'success' => true,
                'file_id' => $result->getId(),
                'file_name' => $result->getName(),
                'web_view_link' => $result->getWebViewLink(),
                'download_link' => 'https://drive.google.com/uc?id='.$result->getId(),
                'size' => $result->getSize(),
                'mime_type' => $result->getMimeType(),
                'created_time' => $result->getCreatedTime(),
            ];

        } catch (\Exception $e) {
            Log::error('Google Drive content upload failed: '.$e->getMessage(), [
                'filename' => $filename,
                'content_length' => strlen($content),
                'subfolder' => $subfolder,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * List files in Google Drive folder (optimized)
     */
    public function listFiles(?string $subfolder = null): array
    {
        try {
            $folderId = $this->folderId;

            if ($subfolder) {
                $cachedFolderId = $this->getCachedFolderId($subfolder, $this->folderId);
                if (! $cachedFolderId) {
                    return [
                        'success' => false,
                        'error' => 'Subfolder not found',
                    ];
                }
                $folderId = $cachedFolderId;
            }

            $query = "'{$folderId}' in parents and trashed=false";
            $results = self::$service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id,name,size,mimeType,createdTime,webViewLink)',
                'pageSize' => 100, // Limit for better performance
            ]);

            $files = [];
            foreach ($results->getFiles() as $file) {
                $files[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'created_time' => $file->getCreatedTime(),
                    'web_view_link' => $file->getWebViewLink(),
                    'download_link' => 'https://drive.google.com/uc?id='.$file->getId(),
                ];
            }

            return [
                'success' => true,
                'files' => $files,
            ];

        } catch (\Exception $e) {
            Log::error('Google Drive list files failed: '.$e->getMessage(), [
                'subfolder' => $subfolder,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a file from Google Drive
     */
    public function deleteFile(string $fileId): array
    {
        try {
            self::$service->files->delete($fileId);

            return [
                'success' => true,
                'message' => 'File deleted successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Google Drive delete failed: '.$e->getMessage(), [
                'file_id' => $fileId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Test connection to Google Drive
     */
    public function testConnection(): array
    {
        try {
            $about = self::$service->about->get(['fields' => 'user']);

            return [
                'success' => true,
                'message' => 'Connected to Google Drive successfully',
                'user_email' => $about->getUser()->getEmailAddress(),
                'user_name' => $about->getUser()->getDisplayName(),
            ];

        } catch (\Exception $e) {
            Log::error('Google Drive connection test failed: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get or create a folder by name (optimized with caching)
     */
    private function getOrCreateFolderOptimized(string $folderName, ?string $parentId = null): string
    {
        $cacheKey = "gdrive_folder_{$parentId}_{$folderName}";

        // Try cache first
        if (isset($this->folderCache[$cacheKey])) {
            return $this->folderCache[$cacheKey];
        }

        // Try Laravel cache
        $cachedId = Cache::get($cacheKey);
        if ($cachedId) {
            $this->folderCache[$cacheKey] = $cachedId;

            return $cachedId;
        }

        try {
            // Search for existing folder with minimal fields
            $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and trashed=false";
            if ($parentId) {
                $query .= " and '{$parentId}' in parents";
            }

            $results = self::$service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id)',
                'pageSize' => 1,
            ]);

            $folders = $results->getFiles();

            if (count($folders) > 0) {
                $folderId = $folders[0]->getId();
            } else {
                // Create the folder
                $folder = new DriveFile;
                $folder->setName($folderName);
                $folder->setMimeType('application/vnd.google-apps.folder');

                if ($parentId) {
                    $folder->setParents([$parentId]);
                }

                $createdFolder = self::$service->files->create($folder, [
                    'fields' => 'id',
                ]);
                $folderId = $createdFolder->getId();
            }

            // Cache the result
            $this->folderCache[$cacheKey] = $folderId;
            Cache::put($cacheKey, $folderId, 3600); // Cache for 1 hour

            return $folderId;

        } catch (\Exception $e) {
            Log::error('Google Drive folder creation failed: '.$e->getMessage(), [
                'folder_name' => $folderName,
                'parent_id' => $parentId,
            ]);
            throw $e;
        }
    }

    /**
     * Get cached folder ID
     */
    private function getCachedFolderId(string $folderName, ?string $parentId = null): ?string
    {
        $cacheKey = "gdrive_folder_{$parentId}_{$folderName}";

        // Check memory cache first
        if (isset($this->folderCache[$cacheKey])) {
            return $this->folderCache[$cacheKey];
        }

        // Check Laravel cache
        $cachedId = Cache::get($cacheKey);
        if ($cachedId) {
            $this->folderCache[$cacheKey] = $cachedId;

            return $cachedId;
        }

        // If not cached, search once and cache it
        try {
            $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and trashed=false";
            if ($parentId) {
                $query .= " and '{$parentId}' in parents";
            }

            $results = self::$service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id)',
                'pageSize' => 1,
            ]);

            $folders = $results->getFiles();
            if (count($folders) > 0) {
                $folderId = $folders[0]->getId();
                $this->folderCache[$cacheKey] = $folderId;
                Cache::put($cacheKey, $folderId, 3600);

                return $folderId;
            }
        } catch (\Exception $e) {
            Log::error('Google Drive folder search failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Set file permission optimized (async-like)
     */
    private function setFilePermissionOptimized(string $fileId): void
    {
        try {
            $permission = new Permission;
            $permission->setRole('reader');
            $permission->setType('anyone');

            // Use minimal fields for faster response
            self::$service->permissions->create($fileId, $permission, [
                'fields' => 'id',
            ]);
        } catch (\Exception $e) {
            // Log but don't fail the upload for permission issues
            Log::warning('Failed to set file permission: '.$e->getMessage(), [
                'file_id' => $fileId,
            ]);
        }
    }

    /**
     * Batch upload multiple files (for future use)
     */
    public function batchUploadFiles(array $files, ?string $subfolder = null): array
    {
        $results = [];
        $parentFolderId = $this->folderId;

        if ($subfolder) {
            $parentFolderId = $this->getOrCreateFolderOptimized($subfolder, $this->folderId);
        }

        foreach ($files as $file) {
            $results[] = $this->uploadFile($file, null); // subfolder already resolved
        }

        return $results;
    }

    /**
     * Clear folder cache
     */
    public function clearFolderCache(): void
    {
        $this->folderCache = [];
        Cache::forget('gdrive_folder_*');
    }

    /**
     * Get service statistics
     */
    public function getServiceStats(): array
    {
        return [
            'cached_folders' => count($this->folderCache),
            'http_client_reused' => self::$httpClient !== null,
            'google_client_reused' => self::$client !== null,
        ];
    }

    public function getFileStream(string $fileId)
    {
        try {
            $response = self::$service->files->get($fileId, [
                'alt' => 'media',
            ]);

            $content = $response->getBody()->getContents();
            $contentType = $response->getHeader('Content-Type')[0] ?? 'application/octet-stream';

            return [
                'success' => true,
                'content' => $content,
                'content_type' => $contentType,
            ];
        } catch (\Exception $e) {
            Log::error('Google Drive get file failed: '.$e->getMessage(), [
                'file_id' => $fileId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getFileUrl(string $fileId): string
    {
        return url("api/google-drive/files/{$fileId}");
    }
}
