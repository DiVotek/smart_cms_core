<?php

namespace SmartCms\Core\Services;

use Illuminate\Contracts\Support\Responsable;

class ScmsResponse implements Responsable
{
    public function __construct(public bool $status = true, public array $data = [], public array $errors = [], public array $triggers = [], public array $headers = []) {}

    public function toArray()
    {
        return [
            'status' => $this->status,
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }

    public function toResponse($request)
    {
        $headers = $this->headers;
        $headers['X-SCMS-RESPONSE'] = true;
        if (count($this->triggers)) {
            $headers['X-SCMS-TRIGGERS'] = json_encode($this->triggers);
        }

        return response()->json($this->toArray())->withHeaders($headers);
    }
}
