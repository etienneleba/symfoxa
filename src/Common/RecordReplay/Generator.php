<?php

namespace App\Common\RecordReplay;

use ReflectionClass;
use ReflectionMethod;

class Generator
{

    public function __construct(
        private readonly RecordReplayController $recordReplayController,
    )
    {
    }

    function createProxy(object $target): object
    {
        $interfaces = class_implements($target);

        if (!$interfaces) {
            throw new InvalidArgumentException("The target object must implement at least one interface.");
        }

        $reflection = new ReflectionClass($target);
        $methods = [];
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if($method->getName() === "__construct") {
                continue;
            }
            $methods[$method->getName()] = $method;
        }


        // Generate the methods for the proxy class
        $methodsCode = '';
        foreach ($interfaces as $interface) {
            $reflection = new ReflectionClass($interface);
            foreach ($reflection->getMethods() as $method) {
                $methods[$method->getName()] = $method;
            }
        }


        foreach ($methods as $method) {
            $methodName = $method->getName();
            $parameters = [];
            $arguments = [];

            foreach ($method->getParameters() as $param) {
                $paramString = '$' . $param->getName();
                if ($param->hasType()) {
                    $type = $param->getType();
                    $paramString = ($type->allowsNull() ? '?' : '') . $type . ' ' . $paramString;
                }
                if ($param->isDefaultValueAvailable()) {
                    $paramString .= ' = ' . var_export($param->getDefaultValue(), true);
                }

                $parameters[] = $paramString;
                $arguments[] = '$' . $param->getName();
            }

            $parametersString = implode(', ', $parameters);
            $argumentsString = implode(', ', $arguments);

            // Handle return type
            $returnType = '';
            $returnStatement = 'return';
            if ($method->hasReturnType()) {
                $type = $method->getReturnType();
                $returnType = ': ' . ($type->allowsNull() ? '?' : '') . $type;

                if ((string)$type === 'void') {
                    $returnStatement = ''; // No return statement for void methods
                }
            }

            $methodsCode .= "
                public function $methodName($parametersString)$returnType {
                    $returnStatement \$this->call('$methodName', [$argumentsString]);
                }
            ";
        }

        // Create the proxy class dynamically
        $proxyClassName = 'RecordReplay_' . (new ReflectionClass($target))->getShortName() . spl_object_id($target);
        $interfacesString = implode(', ', $interfaces);

        $classCode = "
        class $proxyClassName extends App\Common\RecordReplay\RecordReplayGenericDecorator implements $interfacesString {
            $methodsCode
        }
    ";

        if (!class_exists($classCode)) {
            eval($classCode);
        }

        return new $proxyClassName($target, $this->recordReplayController);
    }


}
