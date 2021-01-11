<?php

namespace Servidor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSite extends FormRequest
{
    private const GIT_NOT_FOUND = 128;
    private const GIT_NO_MATCHING_REFS = 2;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (bool) $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'redirect_type' => 'required_if:type,redirect|nullable|integer',
            'redirect_to' => 'required_if:type,redirect|nullable|string',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $data = $validator->getData();

            if (!isset($data['source_branch'], $data['source_repo'])) {
                return;
            }

            $stringOpts = [
                $data['source_repo'],
                $data['source_branch'],
            ];
            exec('git ls-remote --heads --exit-code "' . implode('" "', $stringOpts) . '"', $o, $status);
            unset($o);

            if (self::GIT_NOT_FOUND === $status) {
                $validator->errors()->add('source_repo', "This repo couldn't be found. Does it require auth?");
            } elseif (self::GIT_NO_MATCHING_REFS === $status) {
                $validator->errors()->add('source_branch', "This branch doesn't exist.");
            } elseif (0 !== $status) {
                $validator->errors()->add('source_repo', 'Branch listing failed. Is this repo valid?');
            }
        });
    }
}
