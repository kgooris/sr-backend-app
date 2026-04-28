<?php

namespace AppBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FileTypeExtension
 *
 * @see http://symfony.com/doc/2.1/cookbook/form/create_form_type_extension.html
 */
class FileTypeExtension extends AbstractTypeExtension
{
	/**
	 * Returns the name of the type being extended.
	 *
	 * @return string The name of the type being extended
	 */
	public function getExtendedType()
	{
		return 'file';
	}

	/**
	 * Add the image_path option
	 *
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setOptional(array('file_path', 'file_name'));
	}

	/**
	 * Pass the image url to the view
	 *
	 * @param FormView $view
	 * @param FormInterface $form
	 * @param array $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		if (array_key_exists('file_path', $options)) {
			$parentData = $form->getParent()->getData();

			if (null !== $parentData) {
				$propertyPath = new PropertyPath($options['file_path']);
				$fileUrl = $propertyPath->getValue($parentData);
			} else {
				$fileUrl = null;
			}

			$view->set('file_url', $fileUrl);
		}

		if (array_key_exists('file_name', $options)) {
			$parentData = $form->getParent()->getData();

			if (null !== $parentData) {
				$propertyPath = new PropertyPath($options['file_name']);
				$fileName = $propertyPath->getValue($parentData);
			} else {
				$fileName = null;
			}

			$view->set('file_name', $fileName);
		}
	}
}