<?php

namespace App\Services\Institution;
/**
 * Class InstitutionAttestationsDetails
 *
 * Helper class to calculate the remaining undergraduate PALs for an instituion
 */
class InstitutionAttestationsDetails {

    /**
     * Calculate PALs details for an institution
     *
     * @param int $issuedInstAttestations
     * @param int $issuedResGradInstAttestations
     * @param int $declinedInstAttestations
     * @param int $declinedResGradInstAttestations
     * @param object $cap
     *
     * @return array
     */
    public function getInstitutionAttestInfo($issuedInstAttestations, $issuedResGradInstAttestations, $declinedInstAttestations, $declinedResGradInstAttestations, $cap) {

        // Total Undergrad. issued PALs = Total issued PALs - Total Grad. Pals
        $issuedUndergradAttestations = $issuedInstAttestations - $issuedResGradInstAttestations;

        // Total Undergrad. declined PALs = Total issued PALs - Total Grad. Pals
        $declinedUndergradAttestations = $declinedInstAttestations - $declinedResGradInstAttestations;

        // Remaining Total attestations for the institution - (issued and declined)
        $remainingTotalAttestations = $cap->total_attestations - ($issuedInstAttestations + $declinedInstAttestations);

        // Calculate the default remaining Undergrad. PALs based on the Inst. Cap details
        $undergradAttestationsLimitDefault = $cap->total_attestations - $cap->total_reserved_graduate_attestations;

        // Calculate the real remaining Undegrad. PALs based on the number of already Undergrad. PALs issued and declined
        $undergradAttestationsRemaining = $undergradAttestationsLimitDefault - ($issuedUndergradAttestations + $declinedInstAttestations);

        // If Undegrad. PALs have been already issued, select the real remaining Undergrad. Attest. total as the new cap.
        $undergradAttestationsLimitFinal = ($undergradAttestationsLimitDefault < $undergradAttestationsRemaining) ? $undergradAttestationsLimitDefault : $undergradAttestationsRemaining;

        // Last verification to always make sure than the remaining number of Undergrad. PALs is never higher than the total remaining PALs
        if ($remainingTotalAttestations < $undergradAttestationsLimitFinal) {
            $undergradAttestationsLimitFinal = $remainingTotalAttestations;
        }

        return array(
            'issued' => $issuedInstAttestations,
            'declined' => $declinedInstAttestations,
            'issuedUndegrad' => $issuedUndergradAttestations,
            'declinedUndegrad' => $declinedUndergradAttestations,
            'undergradRemaining' => $undergradAttestationsLimitFinal,
            'issuedResGrad' => $issuedResGradInstAttestations,
        );
    }

}
