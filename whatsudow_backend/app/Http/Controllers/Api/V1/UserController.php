<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Phone;
use App\Models\Document;
use App\Models\Description;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    // ver perfil del usuario
    public function showProfile($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->load('phone', 'company', 'document', 'description');

        return response()->json($user);
    }


    // actualizar nombre
    public function updateName(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'name' => 'required|string',
        ]);

        $user->name = $request->input('name');
        $user->save();

        return response()->json(['message' => 'Nombre actualizado correctamente']);
    }


    // actualizar email
    public function updateEmail(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $userId,
        ]);

        $user->email = $request->input('email');
        $user->save();

        return response()->json(['message' => 'Correo electrónico actualizado con éxito']);
    }


    // actualizar contraseña
    public function updatePassword(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada exitosamente']);
    }


    // actualizar telefono
    public function updatePhone(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'phone' => 'required|numeric',
        ]);

        $phone = Phone::updateOrCreate(['user_id' => $user->id], ['phone' => $request->input('phone')]);
        $user->phone()->associate($phone);
        $user->save();

        return response()->json(['message' => 'Teléfono actualizado con éxito']);
    }

    // actualizar compañia
    public function updateCompany(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'company' => 'required|string',
        ]);
    
        $company = $user->company;
    
        if (!$company) {
            $company = new Company();
            $company->user_id = $user->id; 
        }
    
        $company->company = $request->input('company');
        $company->save();
    
        $user->company()->save($company);

        return response()->json(['message' => 'Empresa actualizada con éxito']);
    }

    // actualizar documento
    public function updateDocument(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'document' => 'required|string',
        ]);
    
        $document = $user->document;
    
        if (!$document) {
            $document = new Document();
            $document->user_id = $user->id; // Asignar el ID del usuario
        }
    
        $document->document_identification = $request->input('document');
        $document->save();
    
        $user->document()->save($document);
    
        return response()->json(['message' => 'Documento actualizado con éxito']);
    }

    // actualizar descripcion
    public function updateDescription(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'description' => 'required|string',
        ]);
    
        $description = $user->company;
    
        if (!$description) {
            $description = new Description();
            $description->user_id = $user->id; 
        }
    
        $description->description = $request->input('description');
        $description->save();
    
        $user->description()->save($description);

        return response()->json(['message' => 'Descripción actualizada con éxito']);
    }
    


    // borrar cuenta
    public function deleteAccount($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Cuenta eliminada con éxito']);
    }
}
