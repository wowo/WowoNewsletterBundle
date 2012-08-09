<?php

namespace Wowo\NewsletterBundle\Newsletter\Placeholders;

use Wowo\NewsletterBundle\Newsletter\Placeholders\Exception\InvalidPlaceholderMappingException;

class PlaceholderProcessor implements PlaceholderProcessorInterface
{
    protected $mapping;
    protected $referenceClass;

    protected $placeholder_delimiter_left = '{{';
    protected $placeholder_delimiter_right = '}}';
    protected $placeholder_regex = '#delim_lef\s*placeholder\s*delim_right#';

    public function setMapping(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function setReferenceClass($referenceClass)
    {
        if (!class_exists($referenceClass)) {
            throw new \InvalidArgumentException(sprintf('Class %s doesn\'t exist!', $referenceClass));
        }
        $this->referenceClass = $referenceClass;
    }

    public function process($object, $body)
    {

        if (null == $this->mapping) {
            throw new \BadMethodCallException('Placeholders mapping ain\'t configured yet');
        }
        if (get_class($object) != $this->referenceClass) {
            throw new \InvalidArgumentException(sprintf('Object passed to method isn\'t an instance of referenceClass (%s != %s)', get_class($object), $this->referenceClass));
        }
        $this->validatePlaceholders();

        foreach ($this->mapping as $placeholder => $source) {
            $value = $this->getPlaceholderValue($object, $source);
            $body = $this->replacePlaceholder($placeholder, $value, $body);
        }

        return $body;
    }

    /**
     * Get value from object based on source (property or method). It claims that validation were done
     * 
     * @param mixed $object 
     * @param mixed $source 
     * @access protected
     * @return void
     */
    protected function getPlaceholderValue($object, $source)
    {
        $rc = new \ReflectionClass(get_class($object));
        if ($rc->hasProperty($source)) {
            return $object->$source;
        } else {
            return call_user_func(array($object, $source));
        }
    }

    protected function replacePlaceholder($placeholder, $value, $body)
    {
        $regex = str_replace(
            array('delim_lef', 'delim_right', 'placeholder'),
            array($this->placeholder_delimiter_left, $this->placeholder_delimiter_right, $placeholder),
            $this->placeholder_regex
        );
        return preg_replace($regex, $value, $body);
    }
    /**
     * It looks firstly for properties, then for method (getter)
     * 
     */
    protected function validatePlaceholders()
    {
        $rc = new \ReflectionClass($this->referenceClass);
        foreach ($this->mapping as $placeholder => $source) {
            if ($rc->hasProperty($source)) {
                $rp = new \ReflectionProperty($this->referenceClass, $source);
                if (!$rp->isPublic()) {
                    throw new InvalidPlaceholderMappingException(
                        sprintf('A placeholder %s defines source %s as a property, but it isn\'t public visible', $placeholder, $source),
                        InvalidPlaceholderMappingException::NON_PUBLIC_PROPERTY);
                }
            } elseif($rc->hasMethod($source)) {
                $rm = new \ReflectionMethod($this->referenceClass, $source);
                if (!$rm->isPublic()) {
                    throw new InvalidPlaceholderMappingException(
                        sprintf('A placeholder %s defines source %s as a method (getter), but it isn\'t public visible', $placeholder, $source),
                        InvalidPlaceholderMappingException::NON_PUBLIC_METHOD);
                }
            } else {
                throw new InvalidPlaceholderMappingException(
                    sprintf('Unable to map placeholder %s with source %s', $placeholder, $source),
                    InvalidPlaceholderMappingException::UNABLE_TO_MAP);
            }
        }
    }
}
