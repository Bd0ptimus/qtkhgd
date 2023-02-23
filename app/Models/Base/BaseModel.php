<?php 
namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    CONST YES_NO = [1 => 'Có', 0 => 'Không'];
    protected static $listValues = [];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $selectedYear = \App\Admin\Helpers\ListHelper::listYear()[0];
        $this->connection = "db_$selectedYear";
    }

    public function getTextValue($key) {
        return static::$listValues[$key][$this->$key] ?? null;
    }
    public static function getListValues() {
        return static::$listValues;
    }
}