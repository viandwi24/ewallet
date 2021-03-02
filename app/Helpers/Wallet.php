<?php
namespace App\Helpers;

use App\Models\Cash;
use App\Models\User;

class Wallet
{
    protected $user;
    protected $id;
    protected $last_cash = null;

    public function __construct($id)
    {
        $this->id = $id;
        $this->user = User::find($id);
    }

    public function init()
    {
        if ($this->last_cash == null) $this->last_cash = $this->user->cashes()->orderBy('id', 'DESC')->first();
    }

    public function getBalance()
    {
        $this->init();
        return $this->last_cash->balance;
    }
}