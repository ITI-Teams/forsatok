<?php
namespace App\Domains\Shared\Traits;


use App\Domains\Shared\Services\Audit\AuditLogger;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::getAuditLogger()->log([
                'action' => 'created',
                'model' => $model,
                'changes' => ['new' => $model->getAttributes()],
            ]);
        });

        static::updated(function ($model) {
            self::getAuditLogger()->log([
                'action' => 'updated',
                'model' => $model,
                'changes' => [
                    'old' => $model->getOriginal(),
                    'new' => $model->getAttributes()
                ],
            ]);
        });

        static::deleted(function ($model) {
            self::getAuditLogger()->log([
                'action' => 'deleted',
                'model' => $model,
                'changes' => ['old' => $model->getAttributes()],
            ]);
        });

        // Optionally: restored, forceDeleted...
    }

    protected static function getAuditLogger(): AuditLogger
    {
        return app(AuditLogger::class);
    }
}
