<?php
namespace App\Enums;

enum StatusUser:string
{
    //Estados que usuário pode assumir
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BLOCKED = 'blocked';
    case PENDING = 'pending';
}
?>
