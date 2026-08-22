<?php
namespace App\Services\Guest;

use App\Enums\TypeLicense;


use App\Repositories\Eloquent\EloquentLicenseRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\EloquentPlanRepository;
use App\Repositories\Eloquent\Guest\EloquentInviteRepository;

use App\Services\Guest\InvitationValidator;
use App\Services\Guest\InvitationGenerator;



use DateTime;

class InviteService{
    public function __construct(
        protected EloquentInviteRepository $repository,
        protected EloquentUserRepository $repositoryUser,
        protected EloquentLicenseRepository $repositoryLicense,
        protected EloquentPlanRepository $repositoryPlan,
        protected InvitationValidator $validator,
        protected InvitationGenerator $generate
    ){}
    public function createrPreRegistration(string $email, string $idPlan, TypeLicense $typeLicense, DateTime $expirationDate){
        /**
         * Processo para gerar o convite e envia ao convidado.
         */

        //Verificação do tipo de licença
        if(!$this->validator->verifyLicenseTypeRequester($typeLicense)){
            return [
                'data' => now(),
                'message'=>'Acesso negado pelo tipo de licença',
                'status '=> 403
            ];
        }

        //Verifique a quantidade de licença disponível
        if(!$this->validator->verifyAvailablelicenseCount($this->repositoryPlan->findById($idPlan))){
            return [
                'data' => now(),
                'message'=>'Licença disponível insuficiente',
                'status '=> 442
            ];
        }

        //Cria o convite
        $InviteCreated = $this->generate->createrInvitation(
            $email,
            $idPlan,
            $typeLicense,
            $expirationDate,
        );

        //Verifica se foi concluido com sucesso
        if($InviteCreated == null){
            return [
                'data' => now(),
                'message'=>'Erro ao tentar criar convite',
                'status '=> 442
            ];
        }

        //Efetua o envio do convite via e-mail
        if(!$this->generate->sendInvitation($InviteCreated['token'],$InviteCreated['name'],$email, $expirationDate)){
            return [
                'data' => now(),
                'message'=>'Envio malsucedido',
                'status '=> 442
            ];
        }

        //Retorna a mensagem de sucesso
        return [
            'data' => now(),
            'message'=>'Convite enviado com sucesso'
        ];
    }

    public function createrInviteRegister(string $name, string $password, string $token){
        /**
         * Com dados recebidos é feito o registro do usuario
         */

        //Busca o convite pelo token
        $inviteCreated = $this->repository->findByToken($token);

        //Cria o usuario com as informações coletadas
        $userCreated = $this->generate->createrUser(
            $name,
            $inviteCreated['email'],
            $password
        );

        if($userCreated == null){
            return [
                'data' => now(),
                'message'=>'Erro ao tentar criar o usuario',
                'status '=> 442
            ];
        }

        $licenseCreated = $this->generate->assignLicense(
            $inviteCreated['plan_id'],
            $userCreated['id'],
            $userCreated['type_license'],
        );


        if($licenseCreated == null){
            return [
                'data' => now(),
                'message'=>'Erro ao tentar criar a licença',
                'status '=> 442
            ];
        }

        //Retorna a mensagem de sucesso
        return [
            'data' => now(),
            'message'=>' Registro realizado com sucesso'
        ];
    }

    public function validateToken(string $token){
         /**
         * Com dados recebidos é feito o registro do usuario
         */

        //Busca o convite pelo token
        $inviteCreated = $this->repository->findByToken($token);

        //Verifica se existe algum convite utilizado o token informado
        if($inviteCreated == null){
            return [
                'data' => now(),
                'message'=>'Convite não localizado na fonte de dados com o token informado',
                'status '=> 442
            ];
        }

        //Verifica se é possivel utilizar convite
        if(!$inviteCreated->isValid()){
            return [
                'data' => now(),
                'message'=>'Convite invalido ! expirado ou vencido',
                'status '=> 442
            ];
        }

        return [
            'data' => [
                $inviteCreated['email'],
                $inviteCreated['type_license'],
            ],
            'message'=>' Registro realizado com sucesso'
        ];

    }


}

