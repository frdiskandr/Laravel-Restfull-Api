<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $table = "contacts";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        "name",
        "email",
        "phone",
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class, "user_id", "id");
    }

     public function contact(): HasMany{
        return $this->hasMany(Contact::class, "contact_id", "id");
    }
}
