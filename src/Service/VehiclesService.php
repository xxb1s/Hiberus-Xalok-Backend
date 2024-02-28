<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class VehiclesService
{
    /**
     * @param ValidatorInterface $validator
     * @param array $data
     * @psalm-param array{brand: string, model: string, plate: string, license: string} $data
     * @return array
     * @psalm-return array{success: bool, valid: bool, msg:string|array}
     */
    public function validate(ValidatorInterface $validator, array $data): array
    {
        try {
            $validations = new Assert\Collection([
                'brand' => new Assert\Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 255])
                ]),
                'model' => new Assert\Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 255])
                ]),
                'plate' => new Assert\Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 10])
                ]),
                'license' => new Assert\Sequentially([
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 1, 'max' => 1])
                ])
            ]);

            $violations = $validator->validate($data, $validations);
            $errors = [];
            foreach ($violations as $violation) {
                $errors[str_replace(['[', ']'],  '', $violation->getPropertyPath())] = $violation->getMessage();
            }
            if ($violations->count() > 0) {
                return [
                    'success' => true,
                    'valid' => false,
                    'msg' => $errors
                ];
            }

            return [
                'success' => true,
                'valid' => true,
                'msg' => ''
            ];
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'valid' => null,
                'msg' => $exception->getMessage()
            ];
        }
    }
}