<?php

namespace C33s\CoreBundle\Twig;

use C33s\CoreBundle\Util\InflectorInterface;


class QuickFilterExtension extends \Twig_Extension
{
    /**
    * @var \Twig_Environment
    */
    protected $environment;
    protected $inflector;
    
    public function __construct(InflectorInterface $inflector)
    {
        $this->inflector = $inflector;
    }
    
    /**
    * {@inheritDoc}
    */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;

    }

    public function getName()
    {
        return 'c33s_quick_filter_extension';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('camelize', array($this, 'camelcaseFilter')),
            new \Twig_SimpleFilter('underscorize', array($this, 'underscoreFilter')),
            new \Twig_SimpleFilter('camelcase', array($this, 'camelcaseFilter')),
            new \Twig_SimpleFilter('underscore', array($this, 'underscoreFilter')),
            new \Twig_SimpleFilter('youtube', array($this, 'youtubeFilter'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('columns', array($this, 'columnsFilter')),
            //new \Twig_SimpleFilter('hashdir', array($this, 'hashDirFilter'), array('is_safe' => array('html'))),
        );
    }

    public function camelcaseFilter($word)
    {
        return $this->inflector->camelize($word);
    }
    public function underscoreFilter($word)
    {
        return $this->inflector->underscore($word);
    }
    public function youtubeFilter($id, $flex=true, $privacy=true)
    {
        if ($privacy === true)
        {
            $domain = 'www.youtube-nocookie.com';
        }
        else
        {
            $domain = 'www.youtube.com';
        }
        $html = "";
        if ($flex === true)
        {
            $html .= '<div class="flex-video widescreen" style="margin: 0 auto;text-align:center;">';
        }
        $html .= '<iframe src="//'.$domain.'/embed/'.$id.'?rel=0&amp;wmode=opaque&amp;feature=player_embedded" style="border: 0px;" allowfullscreen></iframe>';
        if ($flex === true)
        {
            $html .= '</div>';
        }
        
        return $html;
    }
    
    /**
     * Convert array to array of arrays with a given size for each block.
     *
     * @param array $values
     * @param int $rowCount
     * @return array
     */
    public function columnsFilter($values, $columns)
    {
        // make sure rowCount is at least 1
        $columns = max(1, (int) $columns);
        
        $rows = array();
        $i = 0;
        foreach ($values as $value)
        {
            $rows[floor($i / $columns)][] = $value;
            ++$i;
        }
        
        return $rows;
    }
}
