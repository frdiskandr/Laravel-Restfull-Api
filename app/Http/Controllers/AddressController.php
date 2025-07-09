<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact($contactId)
    {
        $user = Auth::user();
        $contact = Contact::where('id', $contactId)->where('user_id', $user->id)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Contact not found'
                ]
            ])->setStatusCode(404));
        }
        return $contact;
    }

    private function getAddress($addressId, $contact)
    {
        $address = Address::where('id', $addressId)->where('contact_id', $contact->id)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => 'Address not found'
                ]
            ])->setStatusCode(404));
        }
        return $address;
    }

    /**
     * @OA\Post(
     *      path="/api/contacts/{contactId}/addresses",
     *      tags={"Address"},
     *      summary="Create a new address for a contact",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="contactId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="street", type="string", example="Jl. Contoh No. 1"),
     *              @OA\Property(property="city", type="string", example="Jakarta"),
     *              @OA\Property(property="province", type="string", example="DKI Jakarta"),
     *              @OA\Property(property="country", type="string", example="Indonesia"),
     *              @OA\Property(property="postal_code", type="string", example="12345")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Address created successfully",
     *          @OA\JsonContent(ref="#/components/schemas/AddressResource")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Contact not found"
     *      )
     * )
     */
    public function create(AddressRequest $request, $contactId): JsonResponse
    {
        $contact = $this->getContact($contactId);
        $data = $request->validated();

        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *      path="/api/contacts/{contactId}/addresses",
     *      tags={"Address"},
     *      summary="Get all addresses for a contact",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="contactId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AddressResource"))
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Contact not found"
     *      )
     * )
     */
    public function list($contactId)
    {
        $contact = $this->getContact($contactId);
        $addresses = Address::where('contact_id', $contact->id)->get();
        return AddressResource::collection($addresses);
    }

    /**
     * @OA\Get(
     *      path="/api/contacts/{contactId}/addresses/{addressId}",
     *      tags={"Address"},
     *      summary="Get a specific address for a contact",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="contactId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="addressId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/AddressResource")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Contact or Address not found"
     *      )
     * )
     */
    public function get($contactId, $addressId)
    {
        $contact = $this->getContact($contactId);
        $address = $this->getAddress($addressId, $contact);
        return new AddressResource($address);
    }

    /**
     * @OA\Put(
     *      path="/api/contacts/{contactId}/addresses/{addressId}",
     *      tags={"Address"},
     *      summary="Update a specific address for a contact",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="contactId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="addressId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="street", type="string", example="Jl. Contoh No. 1"),
     *              @OA\Property(property="city", type="string", example="Jakarta"),
     *              @OA\Property(property="province", type="string", example="DKI Jakarta"),
     *              @OA\Property(property="country", type="string", example="Indonesia"),
     *              @OA\Property(property="postal_code", type="string", example="12345")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/AddressResource")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Contact or Address not found"
     *      )
     * )
     */
    public function update(AddressRequest $request, $contactId, $addressId): AddressResource
    {
        $contact = $this->getContact($contactId);
        $address = $this->getAddress($addressId, $contact);
        
        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    /**
     * @OA\Delete(
     *      path="/api/contacts/{contactId}/addresses/{addressId}",
     *      tags={"Address"},
     *      summary="Delete a specific address for a contact",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="contactId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="addressId",
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
     *          description="Contact or Address not found"
     *      )
     * )
     */
    public function delete($contactId, $addressId): JsonResponse
    {
        $contact = $this->getContact($contactId);
        $address = $this->getAddress($addressId, $contact);
        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}