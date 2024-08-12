<?php

namespace App\Http\Controllers;

use App\Models\TransLink;
use App\Models\TransLinkSplit;
use App\Models\TransportTransaction;
use Illuminate\Http\Request;

class TransLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {


        //dd($request->to_link_id_sc['id']);

        $request->validate([
            'mq_trans_id' => ['required','integer'],
            'link_type_id' => ['required','integer'],
        ]);

        $link_type_id = $request->link_type_id;


        if ($link_type_id===3){

            $request->validate([
                'to_link_id.id' => ['required','integer']
            ]);

            $trans_links = TransLink::where('linked_transport_trans_id','=',trim($request->mq_trans_id))->where('trans_link_type_id','=',$request->link_type_id)->withTrashed()->get();

            //remove old links
            if (count($trans_links) >=1){
                foreach ($trans_links as $link){
                    $link->forceDelete();
                }
            }

            //create new link
            TransLink::create([
                'trans_link_type_id' => $link_type_id,
                'transport_trans_id' => $request->to_link_id['id'],
                'linked_transport_trans_id' => $request->mq_trans_id
            ]);

        } else if ($link_type_id===4) {

            $request->validate([
                'to_link_id_sc.id' => ['required','integer']
            ]);

            $trans_links = TransLink::where('linked_transport_trans_id','=',trim($request->mq_trans_id))->where('trans_link_type_id','=',$request->link_type_id)->withTrashed()->get();

            //remove old links
            if (count($trans_links) >=1){
                foreach ($trans_links as $link){
                    $link->forceDelete();
                }
            }

            //create new link
            TransLink::create([
                'trans_link_type_id' => $request->link_type_id,
                'transport_trans_id' => $request->to_link_id_sc['id'],
                'linked_transport_trans_id' => $request->mq_trans_id
            ]);

        } else {

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Link created');

            return redirect()->back();
        }


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Link created');

        return redirect()->back();
    }

    public function storeSplits(Request $request): \Illuminate\Http\RedirectResponse
    {


        //dd($request->to_link_id_sc['id']);

        $request->validate([
            'mq_trans_id' => ['required','integer'],
            'link_type_id' => ['required','integer'],
        ]);

        $link_type_id = $request->link_type_id;

        if ($link_type_id===5){

            $request->validate([
                'to_link_id.id' => ['required','integer']
            ]);

            $trans_link_split = TransLinkSplit::where('linked_transport_trans_id','=',trim($request->mq_trans_id))->where('trans_link_type_id','=',$request->link_type_id)->withTrashed()->get();

            //remove old links
            if (count($trans_link_split) >=1){
                foreach ($trans_link_split as $link){
                    $link->forceDelete();
                }
            }

            $trans_link_split_primary = TransLinkSplit::where('linked_transport_trans_id','=',trim($request->to_link_id['id']))->where('trans_link_type_id','=',$request->link_type_id)->withTrashed()->get();

            //remove old links
            if (count($trans_link_split_primary) >=1){
                foreach ($trans_link_split_primary as $link){
                    $link->forceDelete();
                }
            }


            //create new link
          $created_link =  TransLinkSplit::create([
                'trans_link_type_id' => $link_type_id,
                'transport_trans_id' => $request->to_link_id['id'],
                'linked_transport_trans_id' => $request->mq_trans_id
            ]);

            if (!TransLinkSplit::where('trans_link_type_id',$link_type_id)->where('transport_trans_id',$request->to_link_id['id'])->where('linked_transport_trans_id',$request->to_link_id['id'])->exists()){
                //primary to primary
                $created_link =  TransLinkSplit::create([
                    'trans_link_type_id' => $link_type_id,
                    'transport_trans_id' => $request->to_link_id['id'],
                    'linked_transport_trans_id' => $request->to_link_id['id']
                ]);
            }

            $global_max_sl = TransportTransaction::max('sl_global_id') ?? 0;


            $trans_primary = TransportTransaction::where('id',$request->to_link_id['id'])->first();
            $trans_link = TransportTransaction::where('id',$request->mq_trans_id)->first();

            $trans_primary->is_split_load = true;
            $trans_primary->is_split_load_primary = true;
            $trans_primary->is_split_load_member = false;

            if ($trans_primary->sl_global_id == null ){
                $trans_primary->sl_global_id = $global_max_sl+1;
            }

            if ($trans_primary->sl_id == null ){
                $trans_primary->sl_id = 1;
            }



            $trans_primary->save();

            $trans_link->is_split_load = true;
            $trans_link->is_split_load_member = true;
            $trans_link->is_split_load_primary = false;

            if ($trans_link->sl_global_id == null ){
                $global_max_sl = TransportTransaction::max('sl_global_id') ?? 0;
                $trans_link->sl_global_id = $global_max_sl+1;
            }


            if ($trans_link->sl_id == null ){
                $count = TransLinkSplit::where('transport_trans_id', '=', $request->to_link_id['id'])->count()+1;
                $trans_link->sl_id = $count;
            }


            $trans_link->save();


        }  else {

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Link created');

            return redirect()->back();
        }


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Link created');

        return redirect()->back();
    }

    public function deleteSplits(Request $request, $trade): \Illuminate\Http\RedirectResponse
    {

        $request->validate([
            'link_type_id' => ['required','integer'],
        ]);

        $link_type_id = $request->link_type_id;

        if ($link_type_id===5){

            $trans_link_split = TransLinkSplit::where('linked_transport_trans_id','=',trim($trade))->where('trans_link_type_id','=',5)->withTrashed()->get();

            //remove old links
            if (count($trans_link_split) >=1){
                foreach ($trans_link_split as $link){
                    $link->forceDelete();
                }
            }

            $trans_link = TransportTransaction::where('id',$trade)->first();

            $trans_link->is_split_load = false;
            $trans_link->is_split_load_member = false;
            $trans_link->is_split_load_primary = false;
            $trans_link->save();


        }


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Link deleted');

        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(TransLink $transLink)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransLink $transLink)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransLink $transLink)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransLink $transLink)
    {
        //
    }
}
