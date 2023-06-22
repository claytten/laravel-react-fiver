<?php
namespace App\Services;

use App\Exceptions\CustomServiceException;
use App\Models\User;
use App\Traits\StoreImage;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
  use StoreImage;

  /**
   * Attempt to login user.
   *
   * @param string $fieldType
   * @param string $fieldValue
   * @param string $password
   * @param string $userAgent
   * @return array
   * @throws App\Exceptions\CustomServiceException
   */
  public function attemptLoginUser(string $fieldType, $fieldValue, $password, $userAgent): array
  {
    $credentials = [
      $fieldType => $fieldValue,
      'password' => $password,
    ];
    $user = User::where($fieldType, $fieldValue)->first();
    if (!$user || !Hash::check($credentials['password'], $user->password)) {
      throw new CustomServiceException(Response::HTTP_UNAUTHORIZED, 'The provided credentials are incorrect.');
    }

    $tokenCurrent = $user->tokens()->where('name', $userAgent);
    if ($user && $tokenCurrent->exists()) {
      throw new CustomServiceException(Response::HTTP_OK, 'Already logged in.');
    }

    return [
      'user' => $user->toArray(),
      'accessToken' => $user->createToken($userAgent)->plainTextToken,
    ];
  }

  /**
   * Register user.
   *
   * @param array $data
   * @return App\Models\User
   * @throws App\Exceptions\CustomServiceException
   */
  public function store(array $data): User
  {
    try {
      $user = User::create($data);
      return $user;
    } catch (\Exception $e) {
      throw new CustomServiceException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Error while creating user.');
    }
  }

  /**
   * Find user by id.
   *
   * @param string $id
   * @return App\Models\User
   * @throws App\Exceptions\CustomServiceException
   */
  public function findUserById(string $id): User
  {
    try {
      $user = User::findOrFail($id);
      return $user;
    } catch (\Exception $e) {
      throw new CustomServiceException(Response::HTTP_NOT_FOUND, 'User not found.');
    }
  }

  /**
   * Update Avatar User
   *
   * @param App\Models\User $user
   * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|array|null $file
   * @param string $disk
   * @return App\Models\User
   * 
   * @throws App\Exceptions\CustomServiceException
   */
  public function saveImage(User $user, UploadedFile $file, string $disk): User
  {
    try {
      $this->deleteImage($disk, $user->getRawOriginal('avatar_url'));
      $filename = $this->storeImage($file, $disk, User::FDIMAGE);
      $user->avatar_url = $filename;
      $user->save();
      return $user->refresh();
    } catch(\Exception $e) {
      throw new CustomServiceException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Error while saving image.');
    }
  }
}