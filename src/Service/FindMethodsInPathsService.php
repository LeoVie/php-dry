<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\OtherNodeTypeExpected;
use App\Factory\FilepathMethodsFactory;
use App\Factory\MethodSignature\MethodFromFilepathMethodsFactory;
use App\Model\Method\Method;
use Safe\Exceptions\FilesystemException;

class FindMethodsInPathsService
{
    public function __construct(
        private FilepathMethodsFactory           $filepathMethodsFactory,
        private MethodFromFilepathMethodsFactory $methodSignatureFromFilepathMethodsFactory,
    )
    {
    }

    /**
     * @param string[] $paths
     *
     * @return Method[]
     *
     * @throws FilesystemException
     * @throws OtherNodeTypeExpected
     */
    public function find(array $paths): array
    {
        $filepathMethodsArray = $this->filepathMethodsFactory->create($paths);

        $methodsArray = [];
        foreach ($filepathMethodsArray as $filepathMethods) {
            $methods = $this->methodSignatureFromFilepathMethodsFactory
                ->buildMultipleFromFilepathMethods($filepathMethods);

            $methodsArray = array_merge($methodsArray, $methods);
        }

        return $methodsArray;
    }
}