<?php
namespace App\Models;

use CodeIgniter\Model;
use Exception;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'UID';

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
        $cfg = new \Config\Restfull();
        foreach($cfg->user_config as $key => $value) {
            if ($key != 'model'){
                array_push($this->allowedFields, $value);
            }
        }
    }

    protected function beforeInsert(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    protected function beforeUpdate(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    private function getUpdatedDataWithHashedPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $plaintextPassword = $data['data']['password'];
            $data['data']['password'] = $this->hashPassword($plaintextPassword);
        }
        return $data;
    }

    private function hashPassword(string $plaintextPassword): string
    {
        return password_hash($plaintextPassword, PASSWORD_BCRYPT);
    }

     public function findUserByEmailAddress(string $emailAddress)
    {
        $user = $this
            ->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$user) 
            throw new Exception('User does not exist for specified email address');
        return $user;
    }
}