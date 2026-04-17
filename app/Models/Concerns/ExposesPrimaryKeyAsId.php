<?php

namespace App\Models\Concerns;

/**
 * Mempertahankan akses $model->id meskipun nama kolom PK di DB adalah id_* (kebutuhan penamaan tugas).
 */
trait ExposesPrimaryKeyAsId
{
    public function getIdAttribute(): mixed
    {
        return $this->getAttribute($this->getKeyName());
    }
}
