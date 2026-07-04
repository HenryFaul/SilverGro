<?php

namespace App\Http\Controllers;

use App\Models\TransportTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PcScApprovalController extends Controller
{
    /**
     * Approve a PC or SC transaction
     */
    public function approve(Request $request): RedirectResponse
    {
        $request->validate([
            'transport_trans_id' => ['required', 'integer', 'exists:transport_transactions,id'],
        ]);

        $user = Auth::user();
        $transport_trans = TransportTransaction::find($request->transport_trans_id);

        // Check if this is PC (2) or SC (3)
        $contract_type_id = $transport_trans->contract_type_id;

        if (!in_array($contract_type_id, [2, 3])) {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Invalid contract type for PC/SC approval');
            return redirect()->back();
        }

        // Update the appropriate field based on contract type
        if ($contract_type_id == 2) {
            // PC - Update a_pc
            if ($transport_trans->a_pc == null) {
                $max_a_pc = TransportTransaction::max("a_pc");

                if ($max_a_pc == null) {
                    $max_a_pc = 20000; // fixed starting point (a_mq/a_pc/a_sc all start at 20000)
                }

                if (is_numeric($max_a_pc)) {
                    $transport_trans->a_pc = ($max_a_pc + 1);
                    $transport_trans->save();
                }
            }

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'PC Approval Created - PC#: ' . $transport_trans->a_pc);
        } elseif ($contract_type_id == 3) {
            // SC - Update a_sc
            if ($transport_trans->a_sc == null) {
                $max_a_sc = TransportTransaction::max("a_sc");

                if ($max_a_sc == null) {
                    $max_a_sc = 20000; // fixed starting point (a_mq/a_pc/a_sc all start at 20000)
                }

                if (is_numeric($max_a_sc)) {
                    $transport_trans->a_sc = ($max_a_sc + 1);
                    $transport_trans->save();
                }
            }

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'SC Approval Created - SC#: ' . $transport_trans->a_sc);
        }

        return redirect()->back();
    }
}

