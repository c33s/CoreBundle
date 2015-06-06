<?php

namespace C33s\CoreBundle\Admingenerator\Guesser;

use Admingenerator\GeneratorBundle\Guesser\PropelORMFieldGuesser as BaseGuesser;
use Admingenerator\GeneratorBundle\Exception\NotImplementedException;

class PropelORMFieldGuesser extends BaseGuesser
{
    protected static $currentClass;

    protected static $i18nColumns = array();

    protected function getMetadatas($class = null)
    {
        if ($class) {
            static::$currentClass = $class;
        }

        return $this->getTable(static::$currentClass);
    }

    public function getDbType($class, $fieldName)
    {
        $dbType = parent::getDbType($class, $fieldName);

        if ('virtual' !== $dbType)
        {
            // currently not interested in non-virtual db types
            return $dbType;
        }

        // there are some virtual column types that can be automatically resolved
        if ($this->isI18nModel($class) && 'I18n' == mb_substr($fieldName, 0, 4))
        {
            // this only works for the default i18n_phpname setting of the i18n behavior
            $i18nClass     = $class.'I18n';
            $i18nFieldName = mb_substr($fieldName, 4);

            $i18nColumn = $this->getColumn($i18nClass, $i18nFieldName);
            if (null !== $i18nColumn)
            {
                $dbType = $this->getDbType($i18nClass, $i18nFieldName);

                if ('virtual' !== $dbType)
                {
                    // found an i18n column, remember this for later
                    static::$i18nColumns[$fieldName] = array(
                        'class'         => $class,
                        'i18nClass'     => $i18nClass,
                        'i18nFieldName' => $i18nFieldName,
                        'i18nDbType'    => $dbType,
                        'i18nColumn'    => $i18nColumn,
                    );

                    return 'i18n';
                }
            }
        }

        if ($this->isAttachableModel($class))
        {
            // check for single file upload fields
            if ('File' === mb_substr($fieldName, -4))
            {
                $fileFieldName = mb_substr($fieldName, 0, mb_strlen($fieldName) - 4);

                $fileColumn = $this->getColumn($class, $fileFieldName);
                if (null !== $fileColumn)
                {
                    return 'single_file';
                }
            }

            if ('GeneralAttachmentsCollection' === $fieldName || 'AllAttachmentsCollection' === $fieldName)
            {
                return 'multi_file';
            }
        }

        return $dbType;
    }

    public function getFormType($dbType, $columnName)
    {
        $config    = $this->container->getParameter('admingenerator.propel_form_types');
        $formTypes = array();

        foreach ($config as $key => $value) {
            // if config is all uppercase use it to retrieve \PropelColumnTypes
            // constant, otherwise use it literally
            if ($key === strtoupper($key)) {
                $key = constant('\PropelColumnTypes::'.$key);
            }

            $formTypes[$key] = $value;
        }

        if (array_key_exists($dbType, $formTypes)) {
            return $formTypes[$dbType];
        } elseif ('i18n' === $dbType) {
            return 'collection';
        } elseif ('single_file' === $dbType) {
            return 'afe_single_upload';
        } elseif ('multi_file' === $dbType) {
            return 'afe_collection_upload';
        } elseif ('virtual' === $dbType) {
            throw new NotImplementedException(
                'The dbType "'.$dbType.'" is only for list implemented '
                .'(column "'.$columnName.'" in "'.static::$currentClass.'")'
            );
        } else {
            throw new NotImplementedException(
                'The dbType "'.$dbType.'" is not yet implemented '
                .'(column "'.$columnName.'" in "'.static::$currentClass.'")'
            );
        }
    }

    public function getFormOptions($formType, $dbType, $columnName)
    {
        $options = parent::getFormOptions($formType, $dbType, $columnName);

        if ('i18n' === $dbType)
        {
            $i18nOptions = static::$i18nColumns[$columnName];

            $options['type']    = $this->getFormType($i18nOptions['i18nDbType'], $i18nOptions['i18nFieldName']);
            $options['options'] = $this->getFormOptions($options['type'], $i18nOptions['i18nDbType'], $i18nOptions['i18nFieldName']);
        }

        if ('single_file' === $dbType)
        {
            $options['previewFilter'] = 'gallery_thumb';
            // cut off the "File" suffix
            $options['deleteable'] = substr($columnName, 0, strlen($columnName) - 4);
        }

        if ('multi_file' === $dbType)
        {
            $options['nameable']        = true;
            $options['acceptFileTypes'] = '/^.*$/';

            $options['previewMaxWidth']  = 80;
            $options['previewMaxHeight'] = 60;
            $options['allow_add']        = true;
            $options['allow_delete']     = true;
            $options['error_bubbling']   = false;
            $options['by_reference']     = false;
            $options['type']             = '\\C33s\\AttachmentAdminBundle\\Form\\Type\\Attachment\\EditType';
            $options['options']          = array(
                'data_class' => '\\C33s\\AttachmentBundle\\Model\\Attachment'
            );
        }

        return $options;
    }

    protected function isRequired($fieldName)
    {
        $column = $this->getColumn(static::$currentClass, $fieldName);

        return $column ? $column->isNotNull() : false;
    }

    protected function isI18nColumn($columnName)
    {
        return array_key_exists($columnName, static::$i18nColumns);
    }

    protected function isI18nModel($class)
    {
        return array_key_exists('C33s\\CoreBundle\\Behavior\\I18nHelper\\I18nModelInterface', class_implements($class));
    }

    protected function isAttachableModel($class)
    {
        return array_key_exists('C33s\\AttachmentBundle\\Attachment\\AttachableObjectInterface', class_implements($class));
    }
}
