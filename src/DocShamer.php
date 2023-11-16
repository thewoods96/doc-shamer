<?php

namespace Thewoods96\DocShamer;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

class DocShamer extends Command
{
    /**
     * Runs jobs which are required to be run after each deploy
     *
     * @var string
     */
    protected $signature = 'doc-shamer
                                {path : The file path to the OpenAPI spec}
                                {--show-coverage}
                                {--dry-run}';

    public function handle()
    {
        //Get routes defined within provided spec
        $documentedRoutes = $this->getDocumentedRoutes();
        
        //Get routes defined within parent laravel application
        $applicationRoutes = $this->getAllApplicationRoutes();

        $ignoredRoutes = [];
        $missingRoutes = [];
        $existingRoutes = [];
        foreach ($applicationRoutes as $applicationRoute) {

            //Attempt to find route from docs
            $documentedRouteMethods = Arr::get($documentedRoutes, $applicationRoute['uri']);
            if (!$documentedRouteMethods) {
                Arr::set($applicationRoute, 'documented', false);
                $missingRoutes[] = $applicationRoute;

                continue;
            }
            
            //Check route methods and mark as existing (already documented) if they match
            if (in_array(strtolower($applicationRoute['method']), $documentedRouteMethods)) {
                Arr::set($applicationRoute, 'documented', true);
                
                if (Arr::get($applicationRoute, 'ignore', false)) {
                    $ignoredRoutes[] = $applicationRoute;
                } else {
                    $existingRoutes[] = $applicationRoute;
                }
            }
        }
        
        $totalCount = count($existingRoutes) + count($missingRoutes);

        $this->renderMessage(empty($missingRoutes), count($existingRoutes), $totalCount);
    
        if ($this->option('show-coverage')) {

            if (!empty($missingRoutes)) {
                $this->renderRoutes($missingRoutes);
            }

            if (!empty($existingRoutes) && !empty($missingRoutes)) {
                $this->output->newLine();
                $this->info('At least you documented something...');
                $this->output->newLine();
            }
    
            $this->renderRoutes($existingRoutes);
    
            if (!empty($ignoredRoutes)) {
                $this->output->newLine();
                $this->info('Ignored Routes');
                $this->output->newLine();
                $this->renderRoutes($ignoredRoutes);
            }
        }

        if ($this->option('dry-run') || empty($missingRoutes)) {
            return 0;
        }

        return 1;
    }

    private function renderMessage(bool $successful, int $documentedRouteCount, $totalRouteCount)
    {
        if ($successful) {
            $this->output->newLine();
            $this->info("Congrats! You have documented all $totalRouteCount endpoints!");
            $this->output->newLine();
        } else {
            $coverage = floor(($documentedRouteCount/$totalRouteCount) * 100);

            $this->output->newLine();
            $this->error("🔔🔔🔔 SHAME! You have documented $documentedRouteCount out of a total $totalRouteCount endpoints (~$coverage% coverage) 🔔🔔🔔");
            $this->output->newLine();
        }
    }

    /**
     * Get all routes from the supplied OpenAPI Spec and return and array in format below;
     * [ '/example/{id}/route' => ['GET', 'POST'] ]
     *
     * @return array
     */
    private function getDocumentedRoutes(): array
    {
        $docSpec = Yaml::parseFile($this->argument('path'));
        $documentedPaths = $docSpec['paths'];

        return array_map(function ($path) {
            return array_keys($path);
        }, $documentedPaths);
    }

    /**
     * Get all routes from Laravel application and return array of route data
     * we need to check its existence in the provided spec.
     *
     * @return array
     */
    private function getAllApplicationRoutes(): array
    {
        $routes = Route::getRoutes();

        if (!is_array($routes)) {
            $routes = $routes->getIterator()->getArrayCopy();
        }
        
        return array_map(function(\Illuminate\Routing\Route $route) {
            $uri = $route->uri();

            if (strpos($uri, '/') !== 1 && strlen($uri) > 1) {
                $uri = "/$uri";
            }

            $route = [
                'uri' => $uri,
                'method' => Arr::first($route->methods()),
                'documented' => false,
                'ignore' => false
            ];

            //Exists to handle any wierd default/auth package routes we dont ever want to document 
            if (in_array($route['uri'], Config::get('doc-shamer.routesToExclude')) 
                || Str::contains($route['uri'], Config::get('doc-shamer.routePartialsToExclude'))) {
                Arr::set($route, 'documented', false);
                Arr::set($route, 'ignore', true);
            }

            return $route;
        }, $routes);
    }

    private function renderRoutes(array $routes)
    {
        $this->output->newLine();
        $this->table(
            ['method', 'path', 'documented'], 
            array_map(function ($route) {
                return [
                    'method' => $route['method'],
                    'path' => $route['uri'],
                    'documented' => $route['documented'] ? '✅' : '❌'
                ];
            }, (array) $routes)
        );
        $this->output->newLine();
    }
}
