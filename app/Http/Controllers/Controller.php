<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return a trade document under a name people can attach straight to an email.
     *
     * The documents used to stream with no filename, so every one of them saved
     * as "document.pdf" and had to be renamed by hand before it could be sent -
     * slow, and an easy way to attach the wrong one. They are now named after the
     * trade, the document and who it is for, e.g.
     * "MQ20282 Purchase Order - Pioneer Foods (Pty) Ltd.pdf".
     *
     * ?download=1 saves it straight to the Downloads folder instead of opening it.
     */
    protected function pdfResponse(\Illuminate\Http\Request $request, $pdf, $trans, string $document, ?string $party = null)
    {
        $number = $trans->a_mq ? 'MQ' . $trans->a_mq
            : ($trans->a_pc ? 'PC' . $trans->a_pc
            : ($trans->a_sc ? 'SC' . $trans->a_sc
            : 'ID' . $trans->id));

        $name = $number . ' ' . $document;
        if ($party) {
            $name .= ' - ' . \Illuminate\Support\Str::limit(trim($party), 40, '');
        }

        // Characters Windows and macOS refuse in a filename.
        $name = preg_replace('/[\\\\\/:*?"<>|]+/', '', $name);
        $name = trim(preg_replace('/\s+/', ' ', $name)) . '.pdf';

        return $request->boolean('download') ? $pdf->download($name) : $pdf->stream($name);
    }
}
