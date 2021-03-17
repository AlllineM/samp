<?php

namespace app\components;

use yii\base\Component;

class AmoComponent extends Component
{
    public $login;
    public $domain;
    public $hash;

    public $db; //а вот этого тут быть не должно
}
