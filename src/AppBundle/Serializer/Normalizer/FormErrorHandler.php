<?php

namespace AppBundle\Serializer\Normalizer;

use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\YamlSerializationVisitor;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FormErrorHandler
 *
 * @package AppBundle\Serializer\Normalizer
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class FormErrorHandler implements SubscribingHandlerInterface
{
    private $translator;

    /**
     * FormErrorHandler constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public static function getSubscribingMethods()
    {
        $methods = array();
        foreach (array('xml', 'json', 'yml') as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Symfony\Component\Form\Form',
                'format' => $format,
            );
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Symfony\Component\Form\FormError',
                'format' => $format,
            );
        }

        return $methods;
    }

    public function serializeFormToXml(XmlSerializationVisitor $visitor, Form $form, array $type)
    {
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument(null, null, false);
            $visitor->document->appendChild($formNode = $visitor->document->createElement('form'));
            $visitor->setCurrentNode($formNode);
        } else {
            $visitor->getCurrentNode()->appendChild(
                $formNode = $visitor->document->createElement('form')
            );
        }

        $formNode->setAttribute('name', $form->getName());

        $formNode->appendChild($errorsNode = $visitor->document->createElement('errors'));
        foreach ($form->getErrors() as $error) {
            $errorNode = $visitor->document->createElement('entry');
            $errorNode->appendChild($this->serializeFormErrorToXml($visitor, $error, array()));
            $errorsNode->appendChild($errorNode);
        }

        foreach ($form->all() as $child) {
            if ($child instanceof Form) {
                if (null !== $node = $this->serializeFormToXml($visitor, $child, array())) {
                    $formNode->appendChild($node);
                }
            }
        }

        return $formNode;
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param Form                     $form
     * @param array                    $type
     *
     * @return \ArrayObject
     */
    public function serializeFormToJson(JsonSerializationVisitor $visitor, Form $form, array $type)
    {
        return $this->convertFormToArray($visitor, $form);
    }

    public function serializeFormToYml(YamlSerializationVisitor $visitor, Form $form, array $type)
    {
        return $this->convertFormToArray($visitor, $form);
    }

    public function serializeFormErrorToXml(XmlSerializationVisitor $visitor, FormError $formError, array $type)
    {
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument(null, null, true);
        }

        return $visitor->document->createCDATASection($this->getErrorMessage($formError));
    }

    public function serializeFormErrorToJson(JsonSerializationVisitor $visitor, FormError $formError, array $type)
    {
        return $this->getErrorMessage($formError);
    }

    public function serializeFormErrorToYml(YamlSerializationVisitor $visitor, FormError $formError, array $type)
    {
        return $this->getErrorMessage($formError);
    }

    private function convertFormToArray(GenericSerializationVisitor $visitor, Form $data, $name = null)
    {
//        if (null === $name) {
//            $name = $data->getParent()->getName();
//        }

        $isRoot = null === $visitor->getRoot();

        $errors = array();
        $form = new \ArrayObject();
        foreach ($data->getErrors() as $error) {
            $errors[] = [
                'context' => ($name ? $name.'.' : '').$data->getName(),
                'code' => $error->getMessageTemplate(),
                'message' => $error->getMessage(),
            ];
        }

        foreach ($data->all() as $child) {
            if ($child instanceof Form) {
                $childErrors = $this->convertFormToArray($visitor, $child, ($name ? $name.'.' : '').$child->getParent()->getName());
                if (!empty($childErrors)) {
                    $errors = array_merge($errors, $childErrors);
                }
            }
        }

        if ($isRoot) {
            $visitor->setRoot($form);

            return $form['errors'] = $errors;
        }

        return $errors;
    }
}
