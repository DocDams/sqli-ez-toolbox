<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Services\Parameter;

use SQLI\EzToolboxBundle\Exceptions\ParameterHandlerUnknownParameterNameException;

class ParameterHandlerRepository
{
    /** @var array<ParameterHandlerInterface> */
    private array $handlers = array();

    public function __construct()
    {
    }

    public function addHandler(ParameterHandlerInterface $handler): void
    {
        /** @phpstan-ignore-next-line */
        $this->handlers[$handler::PARAMETER_NAME] = $handler;
    }

    /**
     * @return array
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @param $handlerName
     * @return ParameterHandlerInterface
     * @throws ParameterHandlerUnknownParameterNameException
     */
    public function getHandler($handlerName): ParameterHandlerInterface
    {
        if (array_key_exists($handlerName, $this->handlers)) {
            return $this->handlers[$handlerName];
        } else {
            throw new ParameterHandlerUnknownParameterNameException("Parameter handler $handlerName unknown");
        }
    }
}
