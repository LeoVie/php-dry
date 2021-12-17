<?php

declare(strict_types=1);

namespace App\Service;

use App\Factory\FilepathMethodsFactory;
use App\Factory\MethodFactory;
use App\Model\Method\Method;
use LeoVie\PhpFilesystem\Exception\InvalidBoundaries;
use LeoVie\PhpMethodsParser\Exception\NodeTypeNotConvertable;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;

class FindMethodsInPathsService
{
    public function __construct(
        private FilepathMethodsFactory $filepathMethodsFactory,
        private MethodFactory          $methodFactory,
    )
    {
    }

    /**
     * @param string[] $paths
     *
     * @return Method[]
     *
     * @throws FilesystemException
     * @throws StringsException
     * @throws NodeTypeNotConvertable
     * @throws InvalidBoundaries
     */
    public function find(array $paths): array
    {
        $filepathMethodsArray = $this->filepathMethodsFactory->create($paths);

        $methodsArray = [];
        foreach ($filepathMethodsArray as $filepathMethods) {
            $methods = $this->methodFactory
                ->buildMultipleFromFilepathMethods($filepathMethods);

            $methodsArray = array_merge($methodsArray, $methods);
        }

        return $methodsArray;
    }
}