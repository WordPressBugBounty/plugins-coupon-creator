<?php



namespace Pngx\Vendor {

    use BrianHenryIE\Strauss\Types\AutoloadAliasInterface;

    /**
     * @see AutoloadAliasInterface
     *
     * @phpstan-type ClassAliasArray array{'type':'class',isabstract:bool,classname:string,namespace?:string,extends:string,implements:array<string>}
     * @phpstan-type InterfaceAliasArray array{'type':'interface',interfacename:string,namespace?:string,extends:array<string>}
     * @phpstan-type TraitAliasArray array{'type':'trait',traitname:string,namespace?:string,use:array<string>}
     * @phpstan-type AutoloadAliasArray array<string,ClassAliasArray|InterfaceAliasArray|TraitAliasArray>
     */
    class AliasAutoloader
    {
        private string $includeFilePath;

        /**
         * @var AutoloadAliasArray
         */
        private array $autoloadAliases = array (
  'lucatume\\DI52\\App' => 
  array (
    'type' => 'class',
    'classname' => 'App',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\App',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\CallableBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'CallableBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\CallableBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
      1 => 'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ClassBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ClassBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\ClassBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
      1 => 'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ClosureBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ClosureBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\ClosureBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\Factory' => 
  array (
    'type' => 'class',
    'classname' => 'Factory',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\Factory',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\Parameter' => 
  array (
    'type' => 'class',
    'classname' => 'Parameter',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\Parameter',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\Resolver' => 
  array (
    'type' => 'class',
    'classname' => 'Resolver',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\Resolver',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\ValueBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'ValueBuilder',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\ValueBuilder',
    'implements' => 
    array (
      0 => 'lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Container' => 
  array (
    'type' => 'class',
    'classname' => 'Container',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\Container',
    'implements' => 
    array (
      0 => 'ArrayAccess',
      1 => 'Psr\\Container\\ContainerInterface',
    ),
  ),
  'lucatume\\DI52\\ContainerException' => 
  array (
    'type' => 'class',
    'classname' => 'ContainerException',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\ContainerException',
    'implements' => 
    array (
      0 => 'Psr\\Container\\ContainerExceptionInterface',
    ),
  ),
  'lucatume\\DI52\\NestedParseError' => 
  array (
    'type' => 'class',
    'classname' => 'NestedParseError',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\NestedParseError',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\NotFoundException' => 
  array (
    'type' => 'class',
    'classname' => 'NotFoundException',
    'isabstract' => false,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\NotFoundException',
    'implements' => 
    array (
      0 => 'Psr\\Container\\NotFoundExceptionInterface',
    ),
  ),
  'lucatume\\DI52\\ServiceProvider' => 
  array (
    'type' => 'class',
    'classname' => 'ServiceProvider',
    'isabstract' => true,
    'namespace' => 'lucatume\\DI52',
    'extends' => 'Pngx\\Vendor\\lucatume\\DI52\\ServiceProvider',
    'implements' => 
    array (
    ),
  ),
  'lucatume\\DI52\\Builders\\BuilderInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'BuilderInterface',
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 
    array (
      0 => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\BuilderInterface',
    ),
  ),
  'lucatume\\DI52\\Builders\\ReinitializableBuilderInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ReinitializableBuilderInterface',
    'namespace' => 'lucatume\\DI52\\Builders',
    'extends' => 
    array (
      0 => 'Pngx\\Vendor\\lucatume\\DI52\\Builders\\ReinitializableBuilderInterface',
    ),
  ),
  'Psr\\Container\\ContainerExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ContainerExceptionInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'Pngx\\Vendor\\Psr\\Container\\ContainerExceptionInterface',
    ),
  ),
  'Psr\\Container\\ContainerInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ContainerInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'Pngx\\Vendor\\Psr\\Container\\ContainerInterface',
    ),
  ),
  'Psr\\Container\\NotFoundExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'NotFoundExceptionInterface',
    'namespace' => 'Psr\\Container',
    'extends' => 
    array (
      0 => 'Pngx\\Vendor\\Psr\\Container\\NotFoundExceptionInterface',
    ),
  ),
);

        public function __construct()
        {
            $this->includeFilePath = __DIR__ . '/autoload_alias.php';
        }

        /**
         * @param string $class
         */
        public function autoload($class): void
        {
            if (!isset($this->autoloadAliases[$class])) {
                return;
            }
            switch ($this->autoloadAliases[$class]['type']) {
                case 'class':
                        $this->load(
                            $this->classTemplate(
                                $this->autoloadAliases[$class]
                            )
                        );
                    break;
                case 'interface':
                    $this->load(
                        $this->interfaceTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                case 'trait':
                    $this->load(
                        $this->traitTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                default:
                    // Never.
                    break;
            }
        }

        private function load(string $includeFile): void
        {
            file_put_contents($this->includeFilePath, $includeFile);
            include $this->includeFilePath;
            file_exists($this->includeFilePath) && unlink($this->includeFilePath);
        }

        /**
         * @param ClassAliasArray $class
         */
        private function classTemplate(array $class): string
        {
            $abstract = $class['isabstract'] ? 'abstract ' : '';
            $classname = $class['classname'];
            if (isset($class['namespace'])) {
                $namespace = "namespace {$class['namespace']};";
                $extends = '\\' . $class['extends'];
                $implements = empty($class['implements']) ? ''
                : ' implements \\' . implode(', \\', $class['implements']);
            } else {
                $namespace = '';
                $extends = $class['extends'];
                $implements = !empty($class['implements']) ? ''
                : ' implements ' . implode(', ', $class['implements']);
            }
            return <<<EOD
                <?php
                $namespace
                $abstract class $classname extends $extends $implements {}
                EOD;
        }

        /**
         * @param InterfaceAliasArray $interface
         */
        private function interfaceTemplate(array $interface): string
        {
            $interfacename = $interface['interfacename'];
            $namespace = isset($interface['namespace'])
            ? "namespace {$interface['namespace']};" : '';
            $extends = isset($interface['namespace'])
            ? '\\' . implode('\\ ,', $interface['extends'])
            : implode(', ', $interface['extends']);
            return <<<EOD
                <?php
                $namespace
                interface $interfacename extends $extends {}
                EOD;
        }

        /**
         * @param TraitAliasArray $trait
         */
        private function traitTemplate(array $trait): string
        {
            $traitname = $trait['traitname'];
            $namespace = isset($trait['namespace'])
            ? "namespace {$trait['namespace']};" : '';
            $uses = isset($trait['namespace'])
            ? '\\' . implode(';' . PHP_EOL . '    use \\', $trait['use'])
            : implode(';' . PHP_EOL . '    use ', $trait['use']);
            return <<<EOD
                <?php
                $namespace
                trait $traitname { 
                    use $uses; 
                }
                EOD;
        }
    }

    spl_autoload_register([ new AliasAutoloader(), 'autoload' ]);
}
