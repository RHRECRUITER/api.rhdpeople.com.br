<?php

namespace App\Services\Guest;

use App\Enums\TypeLicense;
use App\Models\Guest\Invite;
use App\Models\License;
use App\Models\User;
use App\Repositories\Eloquent\EloquentLicenseRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\Guest\EloquentInviteRepository;
use App\Services\Utilitary\Notification;
use DateTime;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class InvitationGenerator
{
    public function __construct(
        protected EloquentUserRepository $repositoryUser,
        protected EloquentInviteRepository $repositoryInvite,
        protected EloquentLicenseRepository $repositoryLicense,
        protected Notification $notification,
        protected TemplateService $emailTemplate
    ) {}

    public function createrInvitation(string $email, string $idPlan, TypeLicense $type, DateTime $expirationDate): Invite
    {
        // Generate a random token and create an invitation in the database.
        $token = Str::random(40);

        return $this->repositoryInvite->create([
            'token' => $token,
            'email' => $email,
            'type_license' => $type,
            'expiration_date' => $expirationDate,
            'plan_id' => $idPlan,
        ]);
    }

    public function sendInvitation(string $token, string $recepientName, string $recepientEmail, DateTime $expirationDate): bool
    {
        // Send the invitation to the guest via email.
        $subjetc = 'Convite';
        $messagerHTML = $this->emailTemplate->getInvitationTemplate($recepientName, $this->sendRegisterLink($token, $expirationDate));

        return $this->notification->sendByEmail($recepientName, $recepientEmail, $subjetc, $messagerHTML);
    }

    public function createrUser(string $name, string $email, string $password): User
    {
        // Create a user in the database.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $this->repositoryUser->create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
        ]);
    }

    public function assignLicense(string $idPlan, string $idUser, TypeLicense $type): License
    {
        // Create a license and assign it to the user created in the database.
        return $license = $this->repositoryLicense->create([
            'plan_id' => $idPlan,
            'user_id' => $idUser,
            'type_license' => $type,
            'is_active' => true,
        ]);
    }

    public function sendRegisterLink(string $token, DateTime $expirationDate): string
    {
        // Create the temporary registration link.
        return URL::temporarySignedRoute('invite.generater', $expirationDate, ['token' => $token]);
    }
}
