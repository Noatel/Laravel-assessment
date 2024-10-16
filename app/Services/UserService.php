<?php

namespace App\Services;

use App\Services\UserServiceInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\UserRequest;
use App\Models\Detail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class UserService implements UserServiceInterface
{
    /**
     * The model instance.
     *
     * @var User
     */
    protected $model;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Constructor to bind model to a repository.
     *
     * @param User                $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(User $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Retrieve all resources and paginate.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function list()
    {
        $users = $this->model::paginate();

        return $users;
    }

    /**
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes)
    {
        // Lets check if the UserRequest is valid
        $userRequest = new UserRequest();
        $validator = Validator::make($attributes, $userRequest->rules());

        // check if the validated got any errors
        if (!$validator->fails()) {
            $user = $this->model::create($attributes);
            return $user;
        }
    }

    /**
     * Retrieve model resource details.
     * Abort to 404 if not found.
     *
     * @param  integer $id
     * @return User|null
     */
    public function find(int $id): ?User
    {
        $user = $this->model::find($id);
        if ($user) {
            return $user;
        }

        return null;
    }

    public function findWithThrashed(int $id): ?User
    {
        $user = $this->model::withTrashed()->where('id', $id)->first();
        if ($user) {
            return $user;
        }

        return null;
    }

    /**
     * Summary of findThrashedUsers
     * @param int $id
     * @return mixed|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
     */
    public function findThrashedUsers(int $id): ?User
    {
        $user = $this->model::onlyTrashed()->find($id);
        if ($user) {
            return $user;
        }

        return null;
    }

    /**
     * Update model resource.
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        $user = $this->model::find($id);

        if ($user) {
            if (isset($attributes['photo'])) {
                $attributes['photo']->store('public/users');
                $attributes['photo']->hashName();
                $attributes['photo'] = $attributes['photo']->hashName();
            }

            $user = $user->update($attributes);

            return $user;
        }

        return false;
    }

    /**
     * Soft delete model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function destroy($id)
    {
        $user = $this->model::find($id);
        if ($user) {
            $user->delete();
        }
    }

    /**
     * Include only soft deleted records in the results.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listTrashed()
    {
        $users = $this->model::onlyTrashed()->paginate();
        return $users;
    }

    /**
     * Restore model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function restore($id)
    {
        $user = $this->model::onlyTrashed()->find($id);

        if ($user) {
            $user->restore();
        }
    }

    /**
     * Permanently delete model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function delete($id)
    {
        $user = $this->model::onlyTrashed()->find($id);
        if ($user) {
            $user->forceDelete();
        }
    }

    /**
     * Generate random hash key.
     *
     * @param  string $key
     * @return string
     */
    public function hash(string $key): string
    {
        // Code goes brrrr.
    }

    /**
     * Upload the given file.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    public function upload(UploadedFile $file)
    {
        $filePath = $file->store('public/users');
        $fileName = basename($filePath);

        if (Storage::exists($filePath)) {
            return $fileName;
        }

        return false;
    }

    /**
     * Get a paginated list of users.
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginatedUsers($perPage = 10): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function saveDetails($user)
    {
        $fullName = $user->getFullnameAttribute();
        $middleLetter =  $user->getFirstLetterOfMiddleIntital();
        $photo = $user->getImageUrl();
        $gender = $user->getGenderFromPrefix();

        $details = [
            ['key' => 'Full name', 'value' => $fullName],
            ['key' => 'Middle Initial', 'value' => $middleLetter],
            ['key' => 'Avatar', 'value' => $photo],
            ['key' => 'Gender', 'value' => $gender],
        ];

        foreach ($details as $detail) {
            $detailModel = new Detail();
            $detailModel->key = $detail['key'];
            $detailModel->value = $detail['value'];
            $detailModel->type = 'bio';
            $detailModel->user_id = $user->id;
            $detailModel->save();
        }
    }
}
