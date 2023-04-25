<?php

namespace App\Models;

use App\Rules\ProhibitedWords;
use App\Rules\TrustedDomains;
use App\Services\Interfaces\UserModificationLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use MinuteOfLaravel\Validation\SelfValidatingModel;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $created
 * @property Carbon $deleted
 * @property ?string $notes
 */
class User extends SelfValidatingModel
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    const CREATED_AT = 'created';

    const UPDATED_AT = null;

    const DELETED_AT = 'deleted';

    public $casts = [
        'created' => 'datetime',
        'deleted' => 'datetime',
    ];

    protected $attributes = [
        'notes' => null,
        'deleted' => null,
    ];

    public $fillable = ['name', 'email', 'notes', 'created', 'deleted'];

    protected function rules(bool $forUpdate): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:8',
                'regex:/^[a-z0-9]+$/',
                'unique:users,name' . ($forUpdate ? ',' . $this->id : ''),
                App::make(ProhibitedWords::class),
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email' . ($forUpdate ? ',' . $this->id : ''),
                App::make(TrustedDomains::class),
            ],
            'created' => 'date',
            'deleted' => 'nullable|date|after:'.$this->created->format('Y-m-d H:i:s'),
        ];
    }

    public static function boot(): void
    {
        parent::boot();
        /** @var UserModificationLog $log */
        $log = App::make(UserModificationLog::class);
        self::created(function (self $model) use ($log) {
            $log->logCreation($model);
        });
        self::updated(function (self $model) use ($log) {
            $log->logUpdate($model);
        });
        self::deleted(function (self $model) use ($log) {
            $log->logDeletion($model);
        });
        static::creating(function (self $model) {
            Validator::make($model->toArray(), $model->rules(false))->validate();
        });
        static::updating(function (self $model) {
            Validator::make($model->toArray(), $model->rules(true))->validate();
        });
    }

}
