<?php
namespace App\Models;

use CodeIgniter\Model;
use Exception;

class WebhookLogModel extends Model
{
    protected $table = 'rest_log';
    protected $primaryKey = 'log_id';

    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;    
    protected $allowedFields = [];
    protected $createdField  = 'created';
    protected $updatedField  = 'updated';
    protected $deletedField  = 'deleted';

    
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null) {
        $this->create_allowed_field();
        parent::__construct($db, $validation);
    }

    //create automatic allowed field from config
    private function create_allowed_field() {
        $cfg = new \Appkita\CI4Restfull\Cache\CacheWebhookLog();
        foreach($cfg->toArray() as $key => $value) {
            array_push($this->allowedFields, $key);
        }
    }
}