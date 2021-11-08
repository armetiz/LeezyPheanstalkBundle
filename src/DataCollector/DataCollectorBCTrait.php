<?php
declare(strict_types=1);

namespace Leezy\PheanstalkBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

if (version_compare(Kernel::VERSION, '4.3.0', '>=')) {
    trait DataCollectorBCTrait
    {
        public function collect(Request $request, Response $response, \Throwable $exception = null)
        {
            $this->doCollect($request, $response, $exception);
        }

        protected abstract function doCollect(Request $request, Response $response, \Throwable $exception = null);
    }
} else {
    trait DataCollectorBCTrait
    {
        public function collect(Request $request, Response $response, \Exception $exception = null)
        {
            $this->doCollect($request, $response, $exception);
        }

        protected abstract function doCollect(Request $request, Response $response, \Throwable $exception = null);
    }
}
