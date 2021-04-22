<?php

namespace Pharaoh\Permission\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * 覆寫序列化方法 toArray()時使用
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function groups()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
