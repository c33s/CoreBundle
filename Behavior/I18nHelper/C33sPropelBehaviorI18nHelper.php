<?php

class C33sPropelBehaviorI18nHelper extends Behavior
{
    protected $parameters = array(
        'default_locales' => array(),
    );

    /**
     * i18n behavior has modification order 70, we want to be loaded afterwards
     *
     * @var int
     */
    protected $tableModificationOrder = 75;

    protected function getNamespacedClassName()
    {
        return $this->getTable()->getNamespace().'\\'.$this->getTable()->getPhpName();
    }

    protected function getDefaultLocales()
    {
        $locales = explode(',', $this->getParameter('default_locales'));
        $locales = array_map('trim', $locales);

        return array_filter($locales);
    }

    public function objectAttributes()
    {
        $attributes = '';
        $locales = implode(', ', array_map(function($locale) { return "'$locale'"; }, $this->getDefaultLocales()));

        $attributes .= <<<EOF

/**
 * Default locales used by get[Column]I18ns()
 *
 * @var array
 */
protected \$i18nDefaultLocales = array({$locales});

EOF;

        return $attributes;
    }

    public function objectMethods(OMBuilder $builder)
    {
        if (!$this->getTable()->hasBehavior('i18n'))
        {
            throw new \RuntimeException('Cannot add c33s_i18n_helper behavior to table without i18n behavior');
        }

        $builder->declareClass('C33s\\CoreBundle\\Behavior\\I18nHelper\\I18nModelInterface');

        $methods = '';
        $behavior = $this->getTable()->getBehavior('i18n');
        $i18nTable = $behavior->getI18nTable();
        $i18nModel = $i18nTable->getPhpName();
        $i18nVar = lcfirst($i18nModel);

        foreach ($behavior->getI18nColumns() as $column)
        {
            /* @var $column \Column */
            $phpName = $column->getPhpName();
            $var = lcfirst($phpName);

            $varPlural = 'all' . $builder->getPluralizer()->getPluralForm($phpName);

            $default = $column->getDefaultValueString();

            $methods .= <<<EOF

/**
 * Get all available translations of the "{$phpName}" column.
 * This returns an associative array with locale => value pairs.
 *
 * @return array
 */
public function getI18n{$phpName}()
{
    \${$varPlural} = array();

    foreach (\$this->get{$i18nModel}s() as \${$i18nVar})
    {
        \${$varPlural}[\${$i18nVar}->getLocale()] = \${$i18nVar}->get{$phpName}();
    }
    foreach (\$this->getI18nDefaultLocales() as \$locale)
    {
        // pre-fill default locale values
        if (!array_key_exists(\$locale, \${$varPlural}))
        {
            \${$varPlural}[\$locale] = {$default};
        }
    }

    return \${$varPlural};
}

/**
 * Set translations of the "{$phpName}" column.
 * Accepts an associative array with locale => value pairs.
 *
 * @return {$this->getTable()->getPhpName()}
 */
public function setI18n{$phpName}(\${$varPlural})
{
    foreach (\${$varPlural} as \$locale => \${$var})
    {
        \${$i18nVar} = \$this->getTranslation(\$locale);
        \${$i18nVar}->set{$phpName}(\${$var});
    }

    return \$this;
}

EOF;

        }

        $methods .= <<<EOF

public function setI18nDefaultLocales(array \$locales)
{
    \$this->i18nDefaultLocales = \$locales;

    return \$this;
}

public function getI18nDefaultLocales()
{
    return \$this->i18nDefaultLocales;
}

EOF;

        return $methods;
    }

    public function objectFilter(&$script)
    {
        $this->addInterface($script, 'I18nModelInterface');
    }

    protected function addInterface(&$script, $interface)
    {
        $script = preg_replace('#(implements Persistent)#', '$1, '.$interface, $script);
    }
}
