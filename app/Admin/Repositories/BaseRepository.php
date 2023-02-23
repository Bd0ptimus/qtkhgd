<?php

namespace App\Admin\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;
use Illuminate\Support\Facades\DB;


class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function withCount(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->withCount($relations)->get($columns);
    }

    /**
     * @param array $columns
     * @param array $relations
     * @param integer $paginate
     * @return Collection
     */
    public function paginate(array $columns = ['*'], array $relations = [], $paginate = 10)
    {
        return $this->model->select($columns)->with($relations)->paginate($paginate);
    }

    /**
     * @param array $columns
     * @param array $relations
     * @param integer $paginate
     * @return Collection
     */
    public function simplePaginate(array $columns = ['*'], array $relations = [], $paginate = 10)
    {
        return $this->model->select($columns)->with($relations)->simplePaginate($paginate);
    }

    /**
     * Get all trashed models.
     *
     * @return Collection
     */
    public function allTrashed(): Collection
    {
        return $this->model->onlyTrashed()->get();
    }

    /**
     * Find model by id.
     *
     * @param int $modelId
     * @param array $columns
     * @param array $relations
     * @param array $withCount
     * @param array $appends
     * @return Model
     */
    public function findById(
        int $modelId,
        array $columns = ['*'],
        array $relations = [],
        array $withCount = [],
        array $appends = []
    ): ?Model {
        return $this->model->select($columns)->with($relations)->withCount($withCount)->findOrFail($modelId)->append($appends);
    }

    /**
     * Find model by slug.
     *
     * @param int $modelSlug
     * @param array $columns
     * @param array $relations
     * @param array $appends
     * @return Model
     */
    public function findBySlug(
        string $modelSlug,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?Model {
        return $this->model->select($columns)->with($relations)->findOrFail($modelSlug)->append($appends);
    }

    /**
     * Find trashed model by id.
     *
     * @param int $modelId
     * @return Model
     */
    public function findTrashedById(int $modelId): ?Model
    {
        return $this->model->withTrashed()->findOrFail($modelId);
    }

    /**
     * Find only trashed model by id.
     *
     * @param int $modelId
     * @return Model
     */
    public function findOnlyTrashedById(int $modelId): ?Model
    {
        return $this->model->onlyTrashed()->findOrFail($modelId);
    }

    /**
     * Create a model.
     *
     * @param array $payload
     * @return Model
     */
    public function create(array $payload): ?Model
    {
        $model = $this->model->create($payload);

        return $model->fresh();
    }

    /**
     * Update existing model.
     *
     * @param int $modelId
     * @param array $payload
     * @return bool
     */
    public function update(int $modelId, array $payload): bool
    {
        $model = $this->findById($modelId);

        return $model->update($payload);
    }

    /**
     * Update or create model.
     *
     * @param array $modelId
     * @param array $payload
     * @return object
     */
    public function updateOrCreate(array $modelId, array $payload)
    {
        if(isset($payload['password'])) $payload['password'] = bcrypt($payload['password']);

        return $this->model->updateOrCreate($modelId, $payload);
    }

    /**
     * Delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function deleteById(int $modelId): bool
    {
        return $this->findById($modelId)->delete();
    }

    /**
     * Restore model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function restoreById(int $modelId): bool
    {
        return $this->findOnlyTrashedById($modelId)->restore();
    }

    /**
     * Permanently delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function permanentlyDeleteById(int $modelId): bool
    {
        return $this->findTrashedById($modelId)->forceDelete();
    }

    public function saveAttactment($model, $file, $clear = false) {
        if($clear == true) {
            $this->model->attachments()->delete();
        }

        Attachment::create([
            'attachable_type' => get_class($this->model),
            'attachable_id' => $model->id,
            'path' => $file['filepath'],
            'name' => $file['filename']
        ]);
    } 
}
