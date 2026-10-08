<?php

namespace App\Services\Guest;

use App\Enums\TypeLicense;
use App\Models\Guest\Invite;
use App\Models\License;
use App\Models\Plan;

class InvitationValidator
{
    // Requirements to generator
    public string $acceptedLicenseType = TypeLicense::DONO;

    public function verifyLicenseTypeRequester(TypeLicense $type): bool
    {
        // Checks if the requester's license is an owner license
        return ($type == $this->acceptedLicenseType) ? true : false;
    }

    public function verifyAvailablelicenseCount(Plan $requesterPlan): bool
    {
        // Checks if there are available licenses.;
        return ($requesterPlan->hasAvailableSlots()) ? true : false;
    }

    public function checkInvitationRequirements(TypeLicense $typeLicense, Plan $requesterPlan): bool
    {
        // Checks whether the requirements are met.
        return ($this->verifyLicenseTypeRequester($typeLicense) && $this->verifyAvailablelicenseCount($requesterPlan)) ? true : false;
    }

    // Validate the invitation

    public function verifyToken(Invite $requesterInvite): bool
    {
        // checks if the token can be used
        return ($requesterInvite->isValid()) ? true : false;
    }
}
