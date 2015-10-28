<?php

namespace FM\TinyMCEBundle\Manager;

/**
 * Class ConfigurationManager
 * @package FM\TinyMCEBundle\Manager
 */
class ConfigurationManager
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * ConfigurationManager constructor.
     * @param $configuration array
     */
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}