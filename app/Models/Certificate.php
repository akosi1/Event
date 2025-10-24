<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'certificate_path',
    ];

    /**
     * The user who owns this certificate
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The event this certificate is for
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Accessor for the full certificate image URL
     */
    public function getCertificateUrlAttribute(): string
    {
        if (!$this->certificate_path) {
            return asset('images/default-certificate.jpg');
        }

        if (filter_var($this->certificate_path, FILTER_VALIDATE_URL)) {
            return $this->certificate_path;
        }

        $path = public_path('certificates/' . $this->certificate_path);
        return file_exists($path)
            ? asset('certificates/' . $this->certificate_path)
            : asset('images/default-certificate.jpg');
    }

    /**
     * Delete the certificate image when model is deleted
     */
    protected static function booted()
    {
        static::deleting(function ($certificate) {
            $path = public_path('certificates/' . $certificate->certificate_path);
            if (file_exists($path)) {
                @unlink($path);
            }
        });
    }
}
