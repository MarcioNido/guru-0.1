<?php

namespace App\Http\Controllers;

/**
 * Guru 0.1 Api Controller Class
 *
 * @author Marcio Nido <marcionido@gmail.com>
 * @version 2017-12-30
 */
class ApiController extends Controller
{

    /**
     * Return a response code 422 (Unprocessable Entity) with a error status and a list of errors.
     * Should be used for validation errors
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseUnprocessableEntity($data)
    {
        return response()->json([
            'status' => 'fail',
            'errors' => $data,
        ], 422);
    }

    /**
     * Return a response code 201 (created) with a token attached to the response
     * @param $data
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseCreatedWithToken($data, $token)
    {
        return response()->json([
            'status'    => 'success',
            'data'      => $data,
            'token'     => $token,
        ], 201);
    }

    /**
     * Return a response code 200 (success) with a token attached to the response
     * @param $data
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseSuccessWithToken($data, $token)
    {
        return response()->json([
            'status'    => 'success',
            'data'      => $data,
            'token'     => $token,
        ], 200);
    }


}