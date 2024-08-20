<?php

namespace App\Http\Controllers;

use App\Models\Contactos;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $contactos = Contactos::where('user_id', $user->id)->paginate(8);

        return response()->json($contactos);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'apellidos' => 'required|string',
            'email' => 'required|email',
            'telefono' => 'required|string',
            'ciudad' => 'required|string',
            'fechaDeNacimiento' => 'required|date',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación para la imagen
        ]);

        $user = auth()->user();

        // Guardar la imagen si se ha subido
        $imagen = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('contacto_imagenes', 'public');
        }

        $contacto = new Contactos([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'ciudad' => $request->ciudad,
            'fechaDeNacimiento' => $request->fechaDeNacimiento,
            'imagen' => $imagen, // Guardar la ruta de la imagen
            'user_id' => $user->id
        ]);

        $user->contactos()->save($contacto);

        return response()->json([
            'success' => true,
            'contacto' => $contacto,
            'message' => 'Contacto creado correctamente.'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contacto = Contactos::findOrFail($id);

        return response()->json([
            'success' => true,
            'contacto' => $contacto
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombre' => 'required|string',
            'apellidos' => 'required|string',
            'email' => 'required|email',
            'telefono' => 'required|string',
            'ciudad' => 'required|string',
            'fechaDeNacimiento' => 'required|date',
        ]);

        // Obtener el contacto del usuario autenticado por su ID
        $user = auth()->user();
        $contacto = $user->contactos()->find($id);

        if (!$contacto) {
            return response()->json([
                'success' => false,
                'message' => 'Contacto no encontrado.'
            ], 404);
        }

        // Actualizar los datos del contacto
        $contacto->nombre = $request->nombre;
        $contacto->apellidos = $request->apellidos;
        $contacto->email = $request->email;
        $contacto->telefono = $request->telefono;
        $contacto->ciudad = $request->ciudad;
        $contacto->fechaDeNacimiento = $request->fechaDeNacimiento;

        $contacto->save();

        return response()->json([
            'success' => true,
            'contacto' => $contacto,
            'message' => 'Contacto actualizado correctamente.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contacto = Contactos::findOrFail($id);
            $contacto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contacto eliminado correctamente.'
            ]);
    }
}
