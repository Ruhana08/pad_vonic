<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PollingController extends Controller
{
    public function create()
    {
        return view('admin.polling.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'payload' => ['required', 'json'],
            'action'  => ['required', 'in:draft,publish'],
        ]);

        $publish = $request->action === 'publish';
        $data = json_decode($request->payload, true);

        $rules = [
            'identity.title'      => 'required|string|max:200',
            'identity.start.date' => 'nullable|date',
            'identity.end.date'   => 'nullable|date|after_or_equal:identity.start.date',
        ];

        if ($publish) {
            $rules = array_merge($rules, [
                'identity.start.date'      => 'required|date',
                'identity.end.date'        => 'required|date|after_or_equal:identity.start.date',
                'peserta.question'         => 'required|string',
                'peserta.options'          => 'required|array|size:2',
                'peserta.options.*.label'  => 'required|string|max:150',
                'wisata.question'          => 'required|string',
                'wisata.options'           => 'required|array|min:2',
                'wisata.options.*.name'    => 'required|string|max:150',
                'wisata.options.*.label'   => 'nullable|string|max:50',
                'wisata.options.*.tags'    => 'array',
                'wisata.options.*.tags.*'  => 'string|max:30',
                'kaos.question'            => 'required|string',
                'kaos.sizes'               => 'required|array|min:1',
                'kaos.options'             => 'required|array|min:2',
                'kaos.options.*.name'      => 'required|string|max:150',
            ]);
        }

        $validator = Validator::make($data ?? [], $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        foreach ($data['kaos']['options'] ?? [] as $i => $opt) {
            $file = $request->file('kaos_image_' . $opt['uid']);
            if ($file) {
                $request->validate(['kaos_image_' . $opt['uid'] => 'image|max:2048']);
                $data['kaos']['options'][$i]['image'] = $file->store('polling/kaos', 'public');
            }
        }

        $status = $publish ? 'aktif' : 'draft';

        // TODO: simpan $data dengan kolom status = $status.
        // Bersihkan HTML rich text (description, question) di server, mis. mews/purifier.

        return redirect()->route('admin.polling.arsip')
            ->with('status', $publish ? 'Polling berhasil diposting.' : 'Draft polling disimpan.');
    }
}