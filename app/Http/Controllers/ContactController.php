<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;


class ContactController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/contact",
     *      tags={"Contact"},
     *      summary="Create a new contact",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *              @OA\Property(property="phone", type="string", example="1234567890")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Contact created successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      )
     * )
     */
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

    /**
     * @OA\Get(
     *      path="/api/contacts",
     *      tags={"Contact"},
     *      summary="Get all contacts",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function Get()
    {
        $user = Auth::user();
        $contact = Contact::get()->where("user_id", $user->id);

        return response([
            "message" => "sucess",
            "data" => $contact,
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/contact/{id}",
     *      tags={"Contact"},
     *      summary="Get a contact by ID",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Contact not found"
     *      )
     * )
     */
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

    /**
     * @OA\Patch(
     *      path="/api/contact/{id}",
     *      tags={"Contact"},
     *      summary="Update a contact",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *              @OA\Property(property="phone", type="string", example="1234567890")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Contact not found"
     *      )
     * )
     */
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

    /**
     * @OA\Delete(
     *      path="/api/contact/{id}",
     *      tags={"Contact"},
     *      summary="Delete a contact",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Contact not found"
     *      )
     * )
     */
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

    /**
     * @OA\Get(
     *      path="/api/contacts/search",
     *      tags={"Contact"},
     *      summary="Search for contacts",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="phone",
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="size",
     *          in="query",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
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
