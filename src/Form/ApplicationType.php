<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType {
    /**
     * basic configuration for field config
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    protected function getConfiguration(string $label, string $placeholder, array $options = []): array
    {
        return array_merge([
            'label' => $label,
                'attr' => [
                    'placeholder' => $placeholder
                ]    
                ], $options);
    }
}