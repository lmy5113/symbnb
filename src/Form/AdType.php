<?php

namespace App\Form;

use App\Entity\Ad;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre', "titre de l'annonce"))
            ->add('slug', TextType::class, $this->getConfiguration('Adresse web', "l'addresse web de l'annonce (automatique)",['required' => false]))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', "introduction"))
            ->add('content', TextareaType::class, $this->getConfiguration('Contenu', "contenu"))
            ->add('coverImage', UrlType::class, $this->getConfiguration("Url de l'image", "url de l'image"))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Nombre de chambre', "nombre de chambre"))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix par nuit', "prix"))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
