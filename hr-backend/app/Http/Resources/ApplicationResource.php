<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'candidate_name' => $this->candidate?->name,
            'job_title'      => $this->jobList?->title,
            'apply_date' => $this->apply_date?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
