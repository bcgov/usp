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
     * @param int $issuedUnderAttestations
     * @param int $issuedGradAttestations
     * @param int $declinedUnderAttestations
     * @param int $declinedGradAttestations
     * @param object $cap
     *
     * @return array
     */
    public function getInstitutionAttestInfo($issuedUnderAttestations, $issuedGradAttestations, $declinedUnderAttestations, $declinedGradAttestations, $cap) {

        // Total Undergrad. issued PALs = Total issued PALs - Total Grad. Pals
//        $issuedUndergradAttestations = $issuedUnderAttestations - $issuedGradAttestations;

        // Total Undergrad. declined PALs = Total issued PALs - Total Grad. Pals
//        $declinedUndergradAttestations = $declinedUnderAttestations - $declinedGradAttestations;

        // Remaining Total attestations for the institution - (issued and declined)
        $remainingTotalAttestations = $cap->total_attestations - ($issuedUnderAttestations + $declinedUnderAttestations
                + $issuedGradAttestations + $declinedGradAttestations);

        // Calculate the default remaining Undergrad. PALs based on the Inst. Cap details
//        $undergradAttestationsLimitDefault = $cap->total_attestations - $cap->total_reserved_graduate_attestations;


        // Calculate the real remaining Undegrad. PALs based on the number of already Undergrad. PALs issued and declined
//        $undergradAttestationsRemaining = $undergradAttestationsLimitDefault - ($issuedUndergradAttestations + $declinedUnderAttestations);
        $undergradAttestationsRemaining = $this->calculateUndergradRemaining($issuedGradAttestations,
            $issuedUnderAttestations, $cap->total_attestations, $cap->total_reserved_graduate_attestations);

        // If Undegrad. PALs have been already issued, select the real remaining Undergrad. Attest. total as the new cap.
        //$undergradAttestationsLimitFinal = ($undergradAttestationsLimitDefault < $undergradAttestationsRemaining) ? $undergradAttestationsLimitDefault : $undergradAttestationsRemaining;

        // Last verification to always make sure than the remaining number of Undergrad. PALs is never higher than the total remaining PALs
//        if ($remainingTotalAttestations < $undergradAttestationsLimitFinal) {
//            $undergradAttestationsLimitFinal = $remainingTotalAttestations;
//        }
//        if ($remainingTotalAttestations < $undergradAttestationsLimitFinal) {
//            $undergradAttestationsLimitFinal = $remainingTotalAttestations;
//        }

        return array(
//            'issued' => $issuedUnderAttestations,
//            'declined' => $declinedUnderAttestations,
//            'issuedUndegrad' => $issuedUndergradAttestations,
//            'declinedUndegrad' => $declinedUndergradAttestations,
            'undergradRemaining' => $undergradAttestationsRemaining,
            'totalRemaining' => $remainingTotalAttestations,
        );
    }

    private function calculateUndergradRemaining($gradsIssued, $undergradsIssued, $total, $gradResLimit) {
        // Always reserve at least 100 for grads unless they exceed it
        $gradsCounted = max($gradResLimit, $gradsIssued);

        // Calculate remaining attestations for undergrads
        $undergradRemaining = $total - $gradsCounted - $undergradsIssued;

        return max(0, $undergradRemaining); // Ensure we never return negative values
    }
}
