<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait StoreImage
{
  /**
   * checking image is exists on storage
   * @param string $disk
   * @param string|null $url
   * @return bool
   */
  public function checkImageExists(string $disk, string|null $url): bool
  {
    if($url != null) {
      return Storage::disk($disk)->exists($url);
    }
    return false;
  }

  /**
   * Delete image on storage
   * @param string $disk
   * @param string|null $url
   * @return bool
   */
  public function deleteImage(string $disk, string|null $url): bool
  {
    if($this->checkImageExists($disk, $url)) {
      return Storage::disk($disk)->delete($url);
    }
    return false; 
  }

  /**
   * Store image on storage
   * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|array|null $file
   * @param string $disk
   * @param string $location
   * @return string
   */
  public function storeImage(UploadedFile $file, string $disk, $location): string
  {
    $filename = $file->store($location, ['disk' => $disk]);
    return $filename;
  }
}