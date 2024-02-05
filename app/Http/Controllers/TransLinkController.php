<?php

namespace App\Http\Controllers;

use App\Models\TransLink;
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
    public function store(Request $request)
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
