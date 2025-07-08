<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ContactController
{
    public function Create(ContactRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $contact = new Contact($data);

        $contact->user_id = $user->id;

        $contact->save();

        return response([
            "message" => "created!",
            "data" => $contact,
        ], 201);
    }

    public function Get()
    {
        $user = Auth::user();
        $contact = Contact::get()->where("user_id", $user->id);

        return response([
            "message" => "sucess",
            "data" => $contact,
        ], 200);
    }

    public function GetById(int $id)
    {
        $user = Auth::user();
        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();
        if (!$contact) {
            return new HttpResponseException(response([
                "message" => "Contact Not found!",
            ], 404));
        }

        return response([
            "message" => "sucess",
            "data" => $contact,
        ], 200);
    }

    public function Update(ContactRequest $request, int $id)
    {
        $data = $request->validated();
        $user = Auth::user();
        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

        if ($data["name"]) {
            $contact->name = $data['name'];
        };

        if ($data["email"]) {
            $contact->email = $data['email'];
        };

        if ($data["phone"]) {
            $contact->phone = $data['phone'];
        };

        $contact->save();

        return response([
            "message" => "updated"
        ], 200);
    }

    public function Delete(int $id)
    {
        $user = Auth::user();
        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();
        if (!$contact) {
            return new HttpResponseException(response([
                "message" => "Contact Not found!",
            ], 404));
        }

        $contact->delete();

        return response([
            "message" => "Deleted",
        ], 200);
    }

    public function Search(Request $request)
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input("size", 10);

        $contacts = Contact::query()->where("user_id", $user->id);

        if ($request->has('name')) {
            $contacts->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('email')) {
            $contacts->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->has('phone')) {
            $contacts->where('phone', 'like', '%' . $request->input('phone') . '%');
        }

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return response([
            "message" => "sucess",
            "data" => $contacts,
        ], 200);
    }
}
