<?php

namespace App\Enums;

enum TypeLicense:string
{
    //Tipos de licenças disponíves
    case OPERACIONAL = 'operacional';
    case COLABORADOR = 'colaborador';
    case NONE = 'nenhuma';
}
?>
