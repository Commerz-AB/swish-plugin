<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin\Form\Type;

use Commerz\SwishPlugin\Bridge\SwishBridgeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SwishGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('environment', ChoiceType::class, [
                'choices' => [
                    'commerz_swish_plugin.ui.sandbox' => SwishBridgeInterface::SANDBOX_ENVIRONMENT,
                    'commerz_swish_plugin.ui.production' => SwishBridgeInterface::PRODUCTION_ENVIRONMENT,
                ],
                'label' => 'commerz_swish_plugin.ui.environment',
                'constraints' => [
                    new NotBlank([
                        'message' => 'commerz_swish_plugin.environment.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('payee_alias', TextType::class, [
                'label' => 'commerz_swish_plugin.ui.payee_alias',
                'required' => true,
            ])
            ->add('merchant_certificate_file', FileType::class, [
                'label' => 'Upload Certificate File (.pem)', // .csr, .p12, .pem
                'mapped' => false,
                'required' => false,
                'attr' => [],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/x-pem-file',         // .pem, .csr, .key (PEM format)
                            'application/pkcs10',             // .csr (PKCS#10 CSR)
                            'application/octet-stream',       // Generic binary format for keys
                            'text/plain',                     // Some CSRs, PEMs, and keys are plain text
                            'application/x-x509-ca-cert',     // Some PEM certificates
                            'application/x-pkcs12',           // .p12 (PKCS#12 format)
                            'application/x-x509-user-cert',   // Another PEM certificate type
                            'application/x-x509-server-cert', // Server certificate in PEM format
                        ],
                        'mimeTypesMessage' => 'Please upload a valid certificate file (.pem)', // .csr, .p12, .pem
                    ])
                ],
            ])
            ->add('merchant_certificate', TextType::class, [
                'label' => 'commerz_swish_plugin.ui.merchant_certificate',
                'required' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('smc_pass', TextType::class, [
                'label' => 'commerz_swish_plugin.ui.smc_pass',
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {

            $form = $event->getForm();
            $data = $event->getData();

            // Define upload directory
            $uploadDir = 'uploads/certificates/';
        
            /** @var UploadedFile|null $file */
            $file = $form->get('merchant_certificate_file')->getData();

            if ($file instanceof UploadedFile) {
                $filename = $file->getClientOriginalName();
                
                // Move the file to storage
                $file->move($uploadDir, $filename);
        
                // Save the path in the text field
                $data['merchant_certificate'] = $uploadDir . $filename;
            }

            $event->setData($data);
        });
    }
}
