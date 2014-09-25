<?php

namespace C33s\CoreBundle\Traits;

use Propel\PropelBundle\Util\PropelInflector;

trait PropelModelTraits
{
    protected function setRelationFromDataArray($data, $properties)
    {
        $modelName = $properties['model'];                             //ACME\\ModelBundle\\Model\\ObjectItem
        $modelClassName = str_replace('//', '/', $modelName);          //ACME\ModelBundle\Model\ObjectItem
        $modelShortClassName = substr(strrchr($modelName, "\\"), 1);   //ObjectItem
        $namespace = substr($modelName, 0, strrpos($modelName, '\\')); //ACME\ModelBundle\Model

        $findMethod = $this->getFindMethod($properties);

        $objectPeer = $namespace.'\\'.$modelShortClassName.'Peer';
        $tableMap = \Propel::getDatabaseMap($objectPeer::DATABASE_NAME)->getTable($objectPeer::TABLE_NAME);

        $collection = new \PropelCollection();
        $collection->setModel($modelName);

        foreach ($data as $dataSet)
        {
            $item = new $modelClassName();

            foreach ($dataSet as $key => $value)
            {
                $columnName = ucfirst(PropelInflector::camelize($key));

                if (is_callable(array($item, $method = 'set'.$columnName)))
                {
                    $value = $this->handleRelation($tableMap, $columnName, $namespace, $findMethod);
                    $item->$method($value);
                } else
                {
                    throw new \InvalidArgumentException(sprintf('Column "%s" does not exist for class "%s".', $key, get_class($item)));
                }
            }

            $collection->append($item);
        }

        $collectionSetMethodName = 'set'.ucfirst($modelShortClassName).'s';

        if (is_callable(array($collection, $collectionSetMethodName)))
        {
            return $this->$collectionSetMethodName($collection);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Column "%s" does not exist for class "%s".', $collectionSetMethodName, get_class($this)));
        }

        return false;
    }

    protected function getFindMethod($properties)
    {
        if (array_key_exists($properties['find_method']))
        {
            return $properties['find_method'];
        }

        return 'findOneBySlug';
    }

    protected function handleRelation($value, $tableMap, $columnName, $namespace, $findMethod)
    {
        if ($tableMap->hasRelation($columnName))
        {
            $objectQuery = $namespace.'\\'.$columnName.'Query';
            $relatedObject = $objectQuery::create()->$findMethod($value);

            if (!$relatedObject)
            {
                throw new \InvalidArgumentException(sprintf('No Related Object found with id "%s" with the Class "%s".', $value, $namespace.'\\'.$columnName));
            }
            $value = $relatedObject;
        }

        return $value;
    }
}
