<?php

/**
 * Class Pocketphp_Elasticsearch_Autoloader
 *
 * Autoload namespaced classes e.g. Elasticsearch\Client
 */
class Pocketphp_Elasticsearch_Autoloader
{
    private $classMap = array();
    private $classPath, $className;

    public function __construct()
    {
        $this->register();
    }

    public function find($class)
    {
        $libDir = Mage::getModuleDir('', 'Pocketphp_Elasticsearch') . '/lib/Pocketphp';

        $class = $this->applyPhp5ClassNameWorkAround($class);

        if ($this->isClassInMap($class))
            return $this->classMap[$class];

        $this->setClassPathAndName($class);
        $this->setFinalClassPath();

        if ($file = $this->canResolveIncludePath($libDir))
            return $file;

        return $this->classMap[$class] = false;
    }

    public function main($basedir)
    {
        $main = getenv("MAINCLASS");
        $main = new $main();
        return $main->main($basedir);
    }

    private function register()
    {
        spl_autoload_register(array($this, 'load'), true, true);
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string $class The name of the class
     * @return bool|null True if loaded, null otherwise
     */
    private function load($class)
    {
        if ($file = $this->find($class)) {
            include $file;

            return true;
        }
    }

    /**
     * @param $class
     * @return string
     */
    private function applyPhp5ClassNameWorkAround($class)
    {
        if ('\\' == $class[0]) {
            $class = substr($class, 1);
            return $class;
        }
        return $class;
    }

    /**
     * @param $class
     * @return bool
     */
    private function isClassInMap($class)
    {
        return isset($this->classMap[$class]);
    }

    /**
     * @param $class
     * @return int
     */
    private function isNamespacedClassName($class)
    {
        return $pos = strrpos($class, '\\');
    }

    /**
     * @param $class
     * @param $pos
     */
    private function setNamespacedClassPathAndName($class, $pos)
    {
        $this->classPath = strtr(substr($class, 0, $pos), '\\', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->className = substr($class, $pos + 1);
    }

    /**
     * @param $class
     */
    private function setPearLikeClassPathAndName($class)
    {
        $this->classPath = null;
        $this->className = $class;
    }

    private function setFinalClassPath()
    {
        $this->classPath .= strtr($this->className, '_', DIRECTORY_SEPARATOR) . '.php';
    }

    /**
     * @param $vendorDir
     * @return string
     */
    private function canResolveIncludePath($vendorDir)
    {
        return stream_resolve_include_path($vendorDir . DIRECTORY_SEPARATOR . $this->classPath);
    }

    /**
     * @param $class
     */
    private function setClassPathAndName($class)
    {
        if (false !== $pos = $this->isNamespacedClassName($class))
            $this->setNamespacedClassPathAndName($class, $pos);
        else
            $this->setPearLikeClassPathAndName($class);
    }
}