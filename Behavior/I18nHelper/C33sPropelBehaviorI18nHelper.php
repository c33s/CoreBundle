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
        if (!$this->getTable()->hasBehavior('i18n'))
        {
            return;
        }

        $attributes = '';
        $locales = implode(', ', array_map(function ($locale) { return "'$locale'"; }, $this->getDefaultLocales()));

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
            return;
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

            $varPlural = 'all'.$builder->getPluralizer()->getPluralForm($phpName);

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

/**
 * Get i18n value of the "{$phpName}" column, using locale fallback (reverse default locales)
 * if the value is empty.
 * Starts with either the given locale or the current/default locale, set previously using getTranslation().
 *
 * @param string \$locale
 *
 * @return mixed
 */
public function get{$phpName}WithFallback(\$locale = null)
{
    \$locale = null !== \$locale ? \$locale : \$this->currentLocale;

    \${$phpName} = \$this->getTranslation(\$locale)->get{$phpName}();
    if ('' != \${$phpName})
    {
        return \${$phpName};
    }

    \$locales = \$this->getI18nDefaultLocales();
    \$key = array_search(\$locale, \$locales);

    if (false === \$key || null === \$key || !isset(\$locales[\$key - 1]))
    {
        // no more fallbacks available, just return the value
        return \${$phpName};
    }

    return \$this->get{$phpName}WithFallback(\$locales[\$key - 1]);
}

EOF;

        }

        $methods .= <<<EOF

/**
 * Set an array of default locales to use for the {$this->getName()} behavior (getI18n*(), get*WithFallback()).
 *
 * @param array \$locales
 *
 * @return {$this->getTable()->getPhpName()}
 */
public function setI18nDefaultLocales(array \$locales)
{
    \$this->i18nDefaultLocales = \$locales;

    return \$this;
}

/**
 * Get an array of default locales used by the {$this->getName()} behavior (getI18n*(), get*WithFallback()).
 *
 * @return array
 */
public function getI18nDefaultLocales()
{
    return \$this->i18nDefaultLocales;
}

EOF;

        return $methods;
    }

    public function objectFilter(&$script)
    {
        if (!$this->getTable()->hasBehavior('i18n'))
        {
            return;
        }

        $this->addInterface($script, 'I18nModelInterface');
    }

    protected function addInterface(&$script, $interface)
    {
        $script = preg_replace('#(implements Persistent)#', '$1, '.$interface, $script);
    }
}
