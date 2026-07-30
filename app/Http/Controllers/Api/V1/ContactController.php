<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexContactRequest $request)
    {
        $query = Contact::with(['category', 'tags']);

        // keyword検索 (first_name, last_name, email)
        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 性別フィルタ
        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        // カテゴリフィルタ
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // 日付フィルタ
        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        $perPage = $request->input('per_page', 20);
        $contacts = $query->latest()->paginate($perPage);

        return ContactResource::collection($contacts);
    }

    // AP02: 詳細取得
    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);
        return new ContactResource($contact);
    }
    /**
     * Store a newly created resource in storage.
     */
    // AP03: 新規作成
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $contact = Contact::create($validated);

        if (!empty($validated['tag_ids'])) {
            $contact->tags()->attach($validated['tag_ids']);
        }

        $contact->load(['category', 'tags']);
        return new ContactResource($contact);
    }

    /**
     * Display the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    // AP04: 更新
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $validated = $request->validated();
        $contact->update($validated);

        if (isset($validated['tag_ids'])) {
            $contact->tags()->sync($validated['tag_ids']);
        }

        $contact->load(['category', 'tags']);
        return new ContactResource($contact);
    }

    /**
     * Remove the specified resource from storage.
     */
    // AP05: 削除
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json(null, 204);
    }
}
