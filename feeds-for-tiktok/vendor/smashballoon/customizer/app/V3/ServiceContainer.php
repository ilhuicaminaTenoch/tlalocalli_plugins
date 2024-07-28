<?php

namespace Smashballoon\Customizer\V3;

use Smashballoon\Stubs\Services\ServiceProvider;

class ServiceContainer extends ServiceProvider
{

    /**
     * @var ServiceProvider[]
     */
    public $services = [
        CustomizerBootstrapService::class
    ];

    /**
     * Constructor.
     * 
     * @return void
     */
    public function __construct()
    {
        $container = Container::getInstance();
        foreach ($this->services as $service) {
			$container->set( $service, new $service() );
		}
    }

    public function register()
    {
        $container = Container::getInstance();

        foreach ($this->services as $service) {
			$serviceInstance = $container->get( $service );

			if ($serviceInstance !== null) {
				$serviceInstance->register();
			}
		}
    }
}